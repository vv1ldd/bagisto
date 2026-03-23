<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class NftService
{
    /**
     * Mint a gift NFT to a specific user wallet address.
     *
     * @param string $userWalletAddress The recipient's Arbitrum wallet address.
     * @param int $giftId The ID of the gift token to mint.
     * @param int $amount Number of tokens to mint (default 1).
     * @param string $data Additional hex data (default "0x").
     * @return array ['success' => bool, 'output' => string|null, 'error' => string|null]
     */
    public function mintGift(string $userWalletAddress, int $giftId, int $amount = 1, string $data = "0x"): array
    {
        $contractAddress = env('MINT_CONTRACT_ADDRESS');
        $privateKey = env('ADMIN_ETH_PRIVATE_KEY');
        $rpcUrl = env('ALCHEMY_RPC_URL');

        if (!$contractAddress || !$privateKey || !$rpcUrl) {
            $error = 'Missing required environment variables for NFT minting (MINT_CONTRACT_ADDRESS, ADMIN_ETH_PRIVATE_KEY, ALCHEMY_RPC_URL).';
            Log::error($error);
            return ['success' => false, 'error' => $error];
        }

        // We use Foundry's `cast send` to execute the transaction safely.
        // Assumes Foundry is installed in the Docker container at the standard root path.
        $castPath = '/root/.foundry/bin/cast';

        // The cast command:
        // cast send <CONTRACT> "mint(address,uint256,uint256,bytes)" <TO> <ID> <AMOUNT> <DATA>
        $command = [
            $castPath,
            'send',
            $contractAddress,
            'mint(address,uint256,uint256,bytes)',
            $userWalletAddress,
            (string) $giftId,
            (string) $amount,
            $data,
            '--rpc-url', $rpcUrl,
            '--private-key', $privateKey,
            '--legacy',
            '--gas-price', '100000000' // 0.1 gwei to bypass Arbitrum basefee fluctuations
        ];

        Log::info("Executing cast send to mint NFT Gift #{$giftId} to {$userWalletAddress}");

        $result = Process::run($command);

        if ($result->successful()) {
            Log::info("Successfully minted NFT Gift #{$giftId}", ['output' => $result->output()]);
            return [
                'success' => true,
                'output' => $result->output()
            ];
        } else {
            Log::error("Failed to mint NFT Gift #{$giftId}", ['error' => $result->errorOutput()]);
            return [
                'success' => false,
                'error' => $result->errorOutput()
            ];
        }
    }
}
