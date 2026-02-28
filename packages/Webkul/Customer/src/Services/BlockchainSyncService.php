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
     * Process a single deposit: log it and top up balance.
     */
    protected function processDeposit(CryptoAddress $cryptoAddress, array $txData): \Webkul\Customer\Models\CryptoTransaction
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($cryptoAddress, $txData) {
            $cryptoTx = \Webkul\Customer\Models\CryptoTransaction::create([
                'customer_id' => $cryptoAddress->customer_id,
                'tx_id' => $txData['tx_id'],
                'network' => $cryptoAddress->network,
                'from_address' => $cryptoAddress->address,
                'to_address' => $txData['to'],
                'amount' => $txData['amount'],
                'status' => 'completed',
            ]);

            // Top up customer balance
            $customer = $cryptoAddress->customer;
            $customer->balance += $txData['amount'];
            $customer->save();

            // Create customer transaction log
            \Webkul\Customer\Models\CustomerTransaction::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'customer_id' => $customer->id,
                'amount' => $txData['amount'],
                'type' => 'deposit',
                'status' => 'completed',
                'reference_type' => \Webkul\Customer\Models\CryptoTransaction::class,
                'reference_id' => $cryptoTx->id,
                'notes' => "Crypto recharge via {$cryptoAddress->network}",
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
}
