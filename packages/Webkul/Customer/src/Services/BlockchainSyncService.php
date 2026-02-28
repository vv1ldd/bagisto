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
}
