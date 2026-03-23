<?php

namespace Webkul\Shop\Services;

use kornrunner\Ethereum\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use phpseclib3\Math\BigInteger;

class Web3MintingService
{
    protected string $rpcUrl;
    protected string $privateKey;
    protected string $contractAddress;

    public function __construct()
    {
        // For Arbitrum Sepolia testing, user can set this in .env
        $this->rpcUrl = env('ALCHEMY_RPC_URL', 'https://arb-sepolia.g.alchemy.com/v2/YOUR_ALCHEMY_KEY');
        $this->privateKey = env('ADMIN_ETH_PRIVATE_KEY', '');
        $this->contractAddress = env('MINT_CONTRACT_ADDRESS', '');
    }

    /**
     * Mint an ERC-1155 NFT to a specific user.
     *
     * @param string $recipientAddress The user's 0x address
     * @param int $tokenId The ID of the NFT to mint
     * @param int $amount How many copies to mint
     * @return string|null The Transaction Hash (TxHash) or null on failure
     */
    public function mintGiftNft(string $recipientAddress, int $tokenId, int $amount = 1): ?string
    {
        if (empty($this->privateKey) || empty($this->contractAddress)) {
            Log::error("Web3MintingService: Missing PRIVATE_KEY or CONTRACT_ADDRESS in .env");
            return null;
        }

        try {
            // 1. Get current Nonce (Transaction count) for the Admin wallet
            $adminAddress = $this->privateKeyToAddress($this->privateKey);
            $nonce = $this->getTransactionCount($adminAddress);

            // 2. Build the data payload for the ERC-1155 mint(address,uint256,uint256,bytes) function
            // Function selector: keccak256("mint(address,uint256,uint256,bytes)") = 0x731133e9
            $functionSelector = '731133e9';
            $paddedAddress = str_pad(str_replace('0x', '', $recipientAddress), 64, '0', STR_PAD_LEFT);
            $paddedTokenId = str_pad(dechex($tokenId), 64, '0', STR_PAD_LEFT);
            $paddedAmount = str_pad(dechex($amount), 64, '0', STR_PAD_LEFT);
            // Default empty bytes parameter for 'data'
            $dataPointer = str_pad(dechex(128), 64, '0', STR_PAD_LEFT);
            $dataLength = str_pad(dechex(0), 64, '0', STR_PAD_LEFT);
            
            $dataPayload = $functionSelector . $paddedAddress . $paddedTokenId . $paddedAmount . $dataPointer . $dataLength;

            // 3. Get network gas price (Arbitrum is very cheap)
            $gasPriceHex = $this->getGasPrice();
            // Estimate gas limit, default to 150000 for minting
            $gasLimitHex = dechex(150000);

            // 4. Construct raw transaction object
            // Arbitrum Sepolia chain ID is 421614. Arbitrum One is 42161.
            $chainId = env('EVM_CHAIN_ID', 421614); 

            $transaction = new Transaction(
                dechex($nonce),
                $gasPriceHex,
                $gasLimitHex,
                $this->contractAddress,
                '0', // Sending 0 ETH with this transaction
                $dataPayload
            );

            // 5. Sign the offline raw transaction
            $signedTx = $transaction->getRaw($this->privateKey, $chainId);

            // 6. Broadcast to Alchemy
            $txHash = $this->sendRawTransaction('0x' . $signedTx);

            Log::info("Web3MintingService: Successfully sent mint transaction. TxHash: " . $txHash);
            
            return $txHash;

        } catch (\Exception $e) {
            Log::error("Web3MintingService Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * JSON-RPC call to get transaction count (nonce).
     */
    protected function getTransactionCount(string $address): int
    {
        $response = Http::post($this->rpcUrl, [
            'jsonrpc' => '2.0',
            'method' => 'eth_getTransactionCount',
            'params' => [$address, 'pending'],
            'id' => 1,
        ]);

        return hexdec($response->json('result', '0x0'));
    }

    /**
     * JSON-RPC call to get current gas price.
     */
    protected function getGasPrice(): string
    {
        $response = Http::post($this->rpcUrl, [
            'jsonrpc' => '2.0',
            'method' => 'eth_gasPrice',
            'params' => [],
            'id' => 1,
        ]);

        return str_replace('0x', '', $response->json('result', '0x100000000'));
    }

    /**
     * JSON-RPC call to broadcast raw signed transaction.
     */
    protected function sendRawTransaction(string $signedTx): string
    {
        $response = Http::post($this->rpcUrl, [
            'jsonrpc' => '2.0',
            'method' => 'eth_sendRawTransaction',
            'params' => [$signedTx],
            'id' => 1,
        ]);

        if ($response->json('error')) {
            throw new \Exception("RPC Error: " . json_encode($response->json('error')));
        }

        return $response->json('result');
    }

    /**
     * Helper: Get public address from private key.
     */
    protected function privateKeyToAddress(string $privateKey): string
    {
        $curve = new \phpseclib3\Crypt\EC\Curves\secp256k1();
        $privateKeyInt = new BigInteger($privateKey, 16);
        $publicKeyPoint = $curve->multiplyPoint($curve->getBasePoint(), $privateKeyInt);
        $affinePoint = $curve->convertToAffine($publicKeyPoint);
        
        $xHex = str_pad($affinePoint[0]->toBigInteger()->toHex(), 64, '0', STR_PAD_LEFT);
        $yHex = str_pad($affinePoint[1]->toBigInteger()->toHex(), 64, '0', STR_PAD_LEFT);
        $publicKeyBin = hex2bin($xHex . $yHex);

        $keccak = new \kornrunner\Keccak();
        $hash = $keccak->hash($publicKeyBin, 256);
        
        return '0x' . substr($hash, -40);
    }
}
