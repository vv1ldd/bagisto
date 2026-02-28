<?php

namespace Webkul\Customer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Webkul\Customer\Models\CryptoAddress;

class BlockchainSyncService
{
    /**
     * Sync deposits for ALL verified addresses of a customer.
     * Implements a 5-minute cooldown per address to protect API rate limits.
     */
    public function syncCustomerDeposits($customer): void
    {
        $addresses = $customer->crypto_addresses()
            ->whereNotNull('verified_at')
            ->get();

        foreach ($addresses as $cryptoAddress) {
            // Rate-limit: only sync if last sync was more than 5 minutes ago
            if (
                $cryptoAddress->last_sync_at &&
                now()->diffInMinutes($cryptoAddress->last_sync_at) < 5
            ) {
                continue;
            }

            try {
                $this->syncDeposits($cryptoAddress);

                // Update last_sync_at timestamp
                $cryptoAddress->update(['last_sync_at' => now()]);
            } catch (\Exception $e) {
                Log::error("On-demand sync failed for address {$cryptoAddress->address}: " . $e->getMessage());
            }
        }
    }


    /**
     * Sync balance for a specific crypto address.
     */
    public function syncBalance(CryptoAddress $cryptoAddress): bool
    {
        try {
            $balance = match ($cryptoAddress->network) {
                'bitcoin' => $this->fetchBitcoinBalance($cryptoAddress->address),
                'ethereum' => $this->fetchEthereumBalance($cryptoAddress->address),
                'ton' => $this->fetchTonBalance($cryptoAddress->address),
                'usdt_ton' => $this->fetchUsdtTonBalance($cryptoAddress->address),
                'dash' => $this->fetchDashBalance($cryptoAddress->address),
                default => null,
            };

            if ($balance !== null) {
                $cryptoAddress->update([
                    'balance' => $balance,
                    'last_sync_at' => now(),
                ]);

                return true;
            }
        } catch (\Exception $e) {
            Log::error("Failed to sync crypto balance for {$cryptoAddress->network} address {$cryptoAddress->address}: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Verify ownership of a crypto address by looking for the challenge amount.
     */
    public function verifyOwnership(CryptoAddress $cryptoAddress): bool
    {
        try {
            if ($cryptoAddress->isVerified()) {
                return true;
            }

            $isVerified = match ($cryptoAddress->network) {
                'bitcoin' => $this->checkBitcoinVerification($cryptoAddress),
                'ethereum' => $this->checkEthereumVerification($cryptoAddress),
                'ton' => $this->checkTonVerification($cryptoAddress),
                'usdt_ton' => $this->checkUsdtTonVerification($cryptoAddress),
                'dash' => $this->checkDashVerification($cryptoAddress),
                default => false,
            };

            if ($isVerified) {
                $cryptoAddress->update([
                    'verified_at' => now(),
                    'is_active' => true,
                ]);

                // Credit the verification payment as a regular deposit
                $this->syncDeposits($cryptoAddress);

                return true;
            }
        } catch (\Exception $e) {
            Log::error("Verification failed for {$cryptoAddress->network} address {$cryptoAddress->address}: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Check Bitcoin verification by scanning recent transactions.
     * Checks if there's a transaction to the platform's cold wallet (A) with the challenge amount.
     */
    protected function checkBitcoinVerification(CryptoAddress $cryptoAddress): bool
    {
        $destinationAddress = config('crypto.verification_addresses.bitcoin');
        $response = Http::get("https://blockchain.info/rawaddr/{$cryptoAddress->address}");

        if ($response->successful()) {
            $data = $response->json();
            $challengeSatoshis = (int) round($cryptoAddress->verification_amount * 100000000);

            foreach ($data['txs'] ?? [] as $tx) {
                foreach ($tx['out'] ?? [] as $output) {
                    // Check if amount matches AND recipient is our cold wallet (A)
                    if ((int) $output['value'] === $challengeSatoshis && ($output['addr'] ?? '') === $destinationAddress) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check Ethereum verification by scanning recent transactions.
     * Checks if there's a transaction to the platform's cold wallet (A) with the challenge amount.
     */
    protected function checkEthereumVerification(CryptoAddress $cryptoAddress): bool
    {
        $destinationAddress = strtolower(config('crypto.verification_addresses.ethereum'));
        $response = Http::get("https://api.etherscan.io/api", [
            'module' => 'account',
            'action' => 'txlist',
            'address' => $cryptoAddress->address,
            'startblock' => 0,
            'endblock' => 99999999,
            'page' => 1,
            'offset' => 20,
            'sort' => 'desc',
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['result']) && is_array($data['result'])) {
                $challengeWei = (string) bcmul($cryptoAddress->verification_amount, '1000000000000000000');

                foreach ($data['result'] as $tx) {
                    // Check if amount matches AND recipient is our cold wallet (A)
                    if ($tx['value'] === $challengeWei && strtolower($tx['to'] ?? '') === $destinationAddress) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check TON verification by scanning recent transactions via public TON API (Toncenter).
     * Checks if there's a transaction to the platform's cold wallet (A) with the challenge amount.
     */
    protected function checkTonVerification(CryptoAddress $cryptoAddress): bool
    {
        $destinationAddress = config('crypto.verification_addresses.ton');
        // Toncenter API limits apply, but it's usable for basic checks without API key
        // Scan THE COLD WALLET history for incoming native TON transfers
        $response = Http::get("https://toncenter.com/api/v2/getTransactions", [
            'address' => $destinationAddress,
            'limit' => 50,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['result']) && is_array($data['result'])) {
                $challengeNano = (string) bcmul((string) $cryptoAddress->verification_amount, '1000000000');
                $cleanSender = preg_replace('/[^a-zA-Z0-9\-_]/', '', $cryptoAddress->address);

                foreach ($data['result'] as $tx) {
                    if (isset($tx['in_msg']['value']) && isset($tx['in_msg']['source'])) {
                        $receivedSource = preg_replace('/[^a-zA-Z0-9\-_]/', '', $tx['in_msg']['source']);

                        // Check if amount matches AND sender is the user's address
                        if ($tx['in_msg']['value'] === $challengeNano && strtolower($receivedSource) === strtolower($cleanSender)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Fetch Bitcoin balance from Blockchain.info.
     * Returns balance in BTC.
     */
    protected function fetchBitcoinBalance(string $address): ?float
    {
        $response = Http::get("https://blockchain.info/rawaddr/{$address}");

        if ($response->successful()) {
            $data = $response->json();
            // final_balance is in Satoshis
            return (float) ($data['final_balance'] / 100000000);
        }

        return null;
    }

    /**
     * Fetch Ethereum balance from Etherscan.
     * Returns balance in ETH.
     * Note: For production, an API key should be used.
     */
    protected function fetchEthereumBalance(string $address): ?float
    {
        // Using Etherscan free API. 
        $response = Http::get("https://api.etherscan.io/api", [
            'module' => 'account',
            'action' => 'balance',
            'address' => $address,
            'tag' => 'latest',
            // 'apikey' => config('services.etherscan.key'),
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['result']) && $data['status'] === "1") {
                // Result is in Wei (10^18)
                return (float) ($data['result'] / 10 ** 18);
            }
        }

        return null;
    }

    /**
     * Fetch TON balance from Toncenter.
     * Returns balance in TON.
     */
    protected function fetchTonBalance(string $address): ?float
    {
        $response = Http::get("https://toncenter.com/api/v2/getAddressBalance", [
            'address' => $address,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['result'])) {
                // Result is in NanoTON (10^9)
                return (float) ($data['result'] / 1000000000);
            }
        }

        return null;
    }

    /**
     * Scan for new deposits from a verified address to the platform's cold wallet.
     */
    public function syncDeposits(CryptoAddress $cryptoAddress): array
    {
        if (!$cryptoAddress->isVerified()) {
            return [];
        }

        $newTransactions = [];

        try {
            $transactions = match ($cryptoAddress->network) {
                'bitcoin' => $this->fetchBitcoinTransactions($cryptoAddress->address),
                'ethereum' => $this->fetchEthereumTransactions($cryptoAddress->address),
                'ton' => $this->fetchTonTransactions($cryptoAddress->address),
                'usdt_ton' => $this->fetchUsdtTonTransactions($cryptoAddress->address),
                'dash' => $this->fetchDashTransactions($cryptoAddress->address),
                default => [],
            };

            $destinationAddress = config("crypto.verification_addresses.{$cryptoAddress->network}");

            // For TON networks, we should normalize comparison to HEX
            $destHex = preg_replace('/[^a-zA-Z0-9\-_]/', '', $destinationAddress);
            if (str_contains($cryptoAddress->network, 'ton')) {
                $destInfo = Http::get("https://tonapi.io/v2/accounts/{$destHex}");
                if ($destInfo->successful() && isset($destInfo->json()['address'])) {
                    $destHex = $destInfo->json()['address'];
                }
            }

            foreach ($transactions as $tx) {
                // Skip if already processed
                if (\Webkul\Customer\Models\CryptoTransaction::where('tx_id', $tx['tx_id'])->exists()) {
                    continue;
                }

                // Match destination (cold wallet)
                $receivedDest = $tx['to'];

                if (str_contains($cryptoAddress->network, 'ton')) {
                    $isMatch = $this->isSameTonAddress($receivedDest, $destinationAddress) || ($destHex && $this->isSameTonAddress($receivedDest, $destHex));
                } else {
                    $cleanReceived = preg_replace('/[^a-zA-Z0-9\-_]/', '', $receivedDest);
                    $isMatch = (strtolower($cleanReceived) === strtolower($destHex) || strtolower($cleanReceived) === strtolower($destinationAddress));
                }

                if ($isMatch) {
                    $newTransactions[] = $this->processDeposit($cryptoAddress, $tx);
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to sync deposits for {$cryptoAddress->network} address {$cryptoAddress->address}: " . $e->getMessage());
        }

        return $newTransactions;
    }

    /**
     * Process a single deposit: log it and top up native balance.
     */
    protected function processDeposit(CryptoAddress $cryptoAddress, array $txData): \Webkul\Customer\Models\CryptoTransaction
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($cryptoAddress, $txData) {
            $exchangeRateService = app(\Webkul\Customer\Services\ExchangeRateService::class);
            $exchangeRate = $exchangeRateService->getRate($cryptoAddress->network);
            $fiatAmount = $txData['amount'] * $exchangeRate;

            $cryptoTx = \Webkul\Customer\Models\CryptoTransaction::create([
                'customer_id' => $cryptoAddress->customer_id,
                'tx_id' => $txData['tx_id'],
                'network' => $cryptoAddress->network,
                'from_address' => $cryptoAddress->address,
                'to_address' => $txData['to'],
                'amount' => $txData['amount'],
                'exchange_rate' => $exchangeRate,
                'fiat_amount' => $fiatAmount,
                'status' => 'completed',
            ]);

            // Top up customer's specific crypto balance
            $customer = $cryptoAddress->customer;
            $cryptoBalance = $customer->balances()->firstOrCreate(
                ['currency_code' => $cryptoAddress->network]
            );
            $cryptoBalance->amount += $txData['amount'];
            $cryptoBalance->save();

            // Update main balance field with fiat equivalent for compatibility
            $customer->balance += $fiatAmount;
            $customer->save();

            // Create customer transaction log (recording the fiat equivalent)
            \Webkul\Customer\Models\CustomerTransaction::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'customer_id' => $customer->id,
                'amount' => $fiatAmount, // Log fiat equivalent in main transaction history
                'type' => 'deposit',
                'status' => 'completed',
                'reference_type' => \Webkul\Customer\Models\CryptoTransaction::class,
                'reference_id' => $cryptoTx->id,
                'notes' => "Crypto recharge via {$cryptoAddress->network} ({$txData['amount']} @ {$exchangeRate})",
            ]);

            return $cryptoTx;
        });
    }

    /**
     * Fetch recent Bitcoin transactions.
     */
    protected function fetchBitcoinTransactions(string $address): array
    {
        $response = Http::get("https://blockchain.info/rawaddr/{$address}");
        $results = [];

        if ($response->successful()) {
            $data = $response->json();
            foreach ($data['txs'] ?? [] as $tx) {
                foreach ($tx['out'] ?? [] as $output) {
                    $results[] = [
                        'tx_id' => $tx['hash'],
                        'to' => $output['addr'] ?? '',
                        'amount' => (float) ($output['value'] / 100000000),
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Fetch recent Ethereum transactions.
     */
    protected function fetchEthereumTransactions(string $address): array
    {
        $response = Http::get("https://api.etherscan.io/api", [
            'module' => 'account',
            'action' => 'txlist',
            'address' => $address,
            'startblock' => 0,
            'endblock' => 99999999,
            'page' => 1,
            'offset' => 20,
            'sort' => 'desc',
        ]);

        $results = [];

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['result']) && is_array($data['result'])) {
                foreach ($data['result'] as $tx) {
                    $results[] = [
                        'tx_id' => $tx['hash'],
                        'to' => $tx['to'] ?? '',
                        'amount' => (float) ($tx['value'] / 10 ** 18),
                    ];
                }
            }
        }

        return $results;
    }

    protected function fetchTonTransactions(string $address): array
    {
        // Use TonAPI for more consistent address formatting (Raw HEX)
        $response = Http::get("https://tonapi.io/v2/blockchain/accounts/{$address}/transactions", [
            'limit' => 30,
        ]);

        $results = [];

        if ($response->successful()) {
            $data = $response->json();
            $transactions = $data['transactions'] ?? [];

            foreach ($transactions as $tx) {
                // Incoming to user wallet (could be a test/return, usually not a deposit we care about for shop refill)
                if (isset($tx['in_msg']['value']) && isset($tx['in_msg']['destination']['address'])) {
                    $results[] = [
                        'tx_id' => $tx['hash'],
                        'to' => $tx['in_msg']['destination']['address'],
                        'amount' => (float) (($tx['in_msg']['value'] ?? 0) / 1000000000),
                    ];
                }

                // Outgoing from user wallet (This is how they deposit to the SHOP)
                if (isset($tx['out_msgs']) && is_array($tx['out_msgs'])) {
                    foreach ($tx['out_msgs'] as $outMsg) {
                        if (isset($outMsg['value']) && isset($outMsg['destination']['address'])) {
                            $results[] = [
                                'tx_id' => $tx['hash'] . '_' . ($outMsg['created_lt'] ?? microtime(true)),
                                'to' => $outMsg['destination']['address'],
                                'amount' => (float) (($outMsg['value'] ?? 0) / 1000000000),
                            ];
                        }
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Check USDT(TON) verification via public TON API.
     * Checks if there's a Jetton transfer to our cold wallet with the challenge amount.
     * USDT on TON has 6 decimals.
     */
    protected function checkUsdtTonVerification(CryptoAddress $cryptoAddress): bool
    {
        $destinationAddress = config('crypto.verification_addresses.usdt_ton');

        // Normalize: trim and keep alphanumeric + dash + underscore (Ledger format)
        $cleanSender = preg_replace('/[^a-zA-Z0-9\-_]/', '', $cryptoAddress->address);
        $cleanDest = preg_replace('/[^a-zA-Z0-9\-_]/', '', $destinationAddress);

        // Fetch HEX for both to ensure robust comparison
        $senderHex = $cleanSender;
        $senderInfo = Http::get("https://tonapi.io/v2/accounts/{$cleanSender}");
        if ($senderInfo->successful() && isset($senderInfo->json()['address'])) {
            $senderHex = $senderInfo->json()['address'];
        }

        $destHex = $cleanDest;
        $destInfo = Http::get("https://tonapi.io/v2/accounts/{$cleanDest}");
        if ($destInfo->successful() && isset($destInfo->json()['address'])) {
            $destHex = $destInfo->json()['address'];
        }

        // Query the COLD WALLET history for incoming Jetton transfers
        // The endpoint /accounts/{address}/jettons/history returns an "operations" key
        $response = Http::get("https://tonapi.io/v2/accounts/{$cleanDest}/jettons/history", [
            'limit' => 35,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $operations = $data['operations'] ?? [];

            // USDT has 6 decimals
            $challengeMicro = (string) bcmul((string) $cryptoAddress->verification_amount, '1000000');

            foreach ($operations as $op) {
                if (($op['operation'] ?? '') === 'transfer') {
                    $source = $op['source']['address'] ?? '';
                    $amount = (string) ($op['amount'] ?? '0');
                    $symbol = $op['jetton']['symbol'] ?? '';

                    // Match criteria:
                    // 1. Source matches sender (hex or original string)
                    // 2. Amount matches (subunit comparison)
                    // 3. Symbol contains USD (USDT / USD₮)
                    $isSourceMatch = ($source === $cleanSender || $source === $senderHex);
                    $isAmountMatch = ($amount === $challengeMicro || (float) $amount === (float) $challengeMicro);
                    $isSymbolMatch = str_contains(strtoupper($symbol), 'USD');

                    if ($isSourceMatch && $isAmountMatch && $isSymbolMatch) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Fetch USDT(TON) balance.
     * Returns balance in USDT.
     */
    protected function fetchUsdtTonBalance(string $address): ?float
    {
        // Using TonAPI to get Jetton balances
        $response = Http::get("https://tonapi.io/v2/accounts/{$address}/jettons");

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['balances']) && is_array($data['balances'])) {
                foreach ($data['balances'] as $jetton) {
                    // Check for standard USDT master contract on TON
                    // EQCxE6mUtQJKFnGfaROTKOt1lZbDiiX1kCixRv7Nw2Id_sDs
                    if (($jetton['jetton']['symbol'] ?? '') === 'USD₮') {
                        // Result is in 6 decimals
                        return (float) ($jetton['balance'] / 1000000);
                    }
                }
                return 0.0; // If jetton not found in balances, balance is 0
            }
        }

        return null;
    }

    /**
     * Fetch recent USDT(TON) transactions.
     */
    protected function fetchUsdtTonTransactions(string $address): array
    {
        // Normalize
        $address = preg_replace('/[^a-zA-Z0-9\-_]/', '', $address);

        $response = Http::get("https://tonapi.io/v2/accounts/{$address}/jettons/history", [
            'limit' => 20,
        ]);

        $results = [];

        if ($response->successful()) {
            $data = $response->json();
            $operations = $data['operations'] ?? [];

            foreach ($operations as $op) {
                if (($op['operation'] ?? '') === 'transfer') {
                    $symbol = $op['jetton']['symbol'] ?? '';

                    // Only process USDT-related symbols
                    if (!str_contains(strtoupper($symbol), 'USD')) {
                        continue;
                    }

                    $results[] = [
                        'tx_id' => $op['transaction_hash'] ?? microtime(true),
                        'to' => $op['destination']['address'] ?? '',
                        'amount' => (float) (($op['amount'] ?? 0) / 1000000), // 6 decimals
                        'symbol' => $symbol,
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Check DASH verification by scanning recent transactions via BlockCypher API.
     */
    protected function checkDashVerification(CryptoAddress $cryptoAddress): bool
    {
        $destinationAddress = config('crypto.verification_addresses.dash');
        $response = Http::get("https://api.blockcypher.com/v1/dash/main/addrs/{$cryptoAddress->address}/full");

        if ($response->successful()) {
            $data = $response->json();
            $challengeDuffs = (int) round($cryptoAddress->verification_amount * 100000000);

            foreach ($data['txs'] ?? [] as $tx) {
                foreach ($tx['outputs'] ?? [] as $output) {
                    if ((int) $output['value'] === $challengeDuffs && in_array($destinationAddress, $output['addresses'] ?? [])) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Fetch DASH balance via BlockCypher API.
     * Returns balance in DASH.
     */
    protected function fetchDashBalance(string $address): ?float
    {
        $response = Http::get("https://api.blockcypher.com/v1/dash/main/addrs/{$address}/balance");

        if ($response->successful()) {
            $data = $response->json();
            // result is in Duffs (10^8)
            return (float) ($data['balance'] / 100000000);
        }

        return null;
    }

    /**
     * Fetch recent DASH transactions via BlockCypher API.
     */
    protected function fetchDashTransactions(string $address): array
    {
        $response = Http::get("https://api.blockcypher.com/v1/dash/main/addrs/{$address}/full");
        $results = [];

        if ($response->successful()) {
            $data = $response->json();
            foreach ($data['txs'] ?? [] as $tx) {
                foreach ($tx['outputs'] ?? [] as $output) {
                    foreach ($output['addresses'] ?? [] as $outAddress) {
                        $results[] = [
                            'tx_id' => $tx['hash'],
                            'to' => $outAddress,
                            'amount' => (float) ($output['value'] / 100000000),
                        ];
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Helper to compare two TON addresses robustly.
     */
    protected function isSameTonAddress(string $addr1, string $addr2): bool
    {
        $addr1 = preg_replace('/[^a-zA-Z0-9\-_]/', '', $addr1);
        $addr2 = preg_replace('/[^a-zA-Z0-9\-_]/', '', $addr2);

        if (strtolower($addr1) === strtolower($addr2)) {
            return true;
        }

        // Check if they match after swapping EQ <-> UQ prefixes (common TON alias issue)
        $altAddr1 = $addr1;
        if (str_starts_with($addr1, 'UQ'))
            $altAddr1 = 'EQ' . substr($addr1, 2);
        elseif (str_starts_with($addr1, 'EQ'))
            $altAddr1 = 'UQ' . substr($addr1, 2);

        if (strtolower($altAddr1) === strtolower($addr2)) {
            return true;
        }

        return false;
    }

    /**
     * Get HEX address from user-friendly TON address via TonAPI.
     */
    protected function getTonHexAddress(string $address): ?string
    {
        try {
            $resp = Http::get("https://tonapi.io/v2/accounts/" . preg_replace('/[^a-zA-Z0-9\-_]/', '', $address));
            if ($resp->successful()) {
                return $resp->json()['address'] ?? null;
            }
        } catch (\Exception $e) {
        }
        return null;
    }
}
