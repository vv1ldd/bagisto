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
