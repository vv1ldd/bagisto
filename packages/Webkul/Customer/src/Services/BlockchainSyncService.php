<?php

namespace Webkul\Customer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Webkul\Customer\Models\CryptoAddress;

class BlockchainSyncService
{
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
        $response = Http::get("https://toncenter.com/api/v2/getTransactions", [
            'address' => $cryptoAddress->address,
            'limit' => 20,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['result']) && is_array($data['result'])) {
                $challengeNano = (string) bcmul((string) $cryptoAddress->verification_amount, '1000000000');

                foreach ($data['result'] as $tx) {
                    if (isset($tx['in_msg']['value']) && isset($tx['in_msg']['destination'])) {
                        // Check if amount matches AND recipient is our cold wallet (A)
                        if ($tx['in_msg']['value'] === $challengeNano && $tx['in_msg']['destination'] === $destinationAddress) {
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

            $destinationAddress = strtolower(config("crypto.verification_addresses.{$cryptoAddress->network}"));

            foreach ($transactions as $tx) {
                // Skip if already processed
                if (\Webkul\Customer\Models\CryptoTransaction::where('tx_id', $tx['tx_id'])->exists()) {
                    continue;
                }

                // Check if it's going to our cold wallet
                if (strtolower($tx['to']) === $destinationAddress) {
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

    /**
     * Fetch recent TON transactions.
     */
    protected function fetchTonTransactions(string $address): array
    {
        $response = Http::get("https://toncenter.com/api/v2/getTransactions", [
            'address' => $address,
            'limit' => 20,
        ]);

        $results = [];

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['result']) && is_array($data['result'])) {
                foreach ($data['result'] as $tx) {
                    if (isset($tx['in_msg']['value']) && isset($tx['in_msg']['destination'])) {
                        $results[] = [
                            // Using the logical time and transaction hash as a pseudo ID
                            'tx_id' => $tx['transaction_id']['hash'],
                            'to' => $tx['in_msg']['destination'],
                            'amount' => (float) ($tx['in_msg']['value'] / 1000000000),
                        ];
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
        // A robust implementation would use TonApi or Toncenter V3 to track Jetton transfers. 
        // For simplicity, we assume we're querying the main address for valid inbound Jetton transfers.
        $response = Http::get("https://tonapi.io/v2/blockchain/accounts/{$cryptoAddress->address}/transfers", [
            'limit' => 20,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['transfers']) && is_array($data['transfers'])) {
                // USDT has 6 decimals
                $challengeMicro = (string) bcmul((string) $cryptoAddress->verification_amount, '1000000');

                foreach ($data['transfers'] as $tx) {
                    if (isset($tx['amount']) && isset($tx['destination']['address'])) {
                        // Check if amount matches AND recipient is our cold wallet
                        // Note: In TON, Jetton addresses formats can vary, we assume standard base64url or hex matching here. 
                        // Real implementation requires address normalization.
                        if ($tx['amount'] === $challengeMicro && $tx['destination']['address'] === $destinationAddress) {
                            return true;
                        }
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
        $response = Http::get("https://tonapi.io/v2/blockchain/accounts/{$address}/transfers", [
            'limit' => 20,
        ]);

        $results = [];

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['transfers']) && is_array($data['transfers'])) {
                foreach ($data['transfers'] as $tx) {
                    if (isset($tx['amount']) && isset($tx['destination']['address'])) {
                        $results[] = [
                            'tx_id' => $tx['hash'], // Hash of the specific transfer
                            'to' => $tx['destination']['address'],
                            'amount' => (float) ($tx['amount'] / 1000000), // 6 decimals
                        ];
                    }
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
}
