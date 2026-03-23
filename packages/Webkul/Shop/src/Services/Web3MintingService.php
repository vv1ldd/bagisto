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
        return $this->sendMintTransaction($this->contractAddress, '731133e9', $recipientAddress, $tokenId, $amount, true);
    }

    /**
     * Mint an ERC-20 Cashback Coin to a specific user.
     *
     * @param string $recipientAddress The user's 0x address
     * @param float $amount The amount of coins to mint (will be adjusted for 18 decimals)
     * @return string|null The Transaction Hash (TxHash) or null on failure
     */
    public function mintCashbackCoin(string $recipientAddress, float $amount): ?string
    {
        $tokenContract = config('meanly.cashback_token_contract_address');
        if (empty($tokenContract)) {
            Log::error("Web3MintingService: Missing CASHBACK_TOKEN_CONTRACT_ADDRESS in config/meanly.php");
            return null;
        }

        // ERC-20 typically has 18 decimals. 
        // We convert the float amount to a large integer: amount * 10^18
        $amountWithDecimals = number_format($amount, 18, '.', '');
        $amountBI = new BigInteger(str_replace('.', '', $amountWithDecimals));

        return $this->sendMintTransaction($tokenContract, '40c10f19', $recipientAddress, $amountBI, null, false);
    }

    /**
     * Generic helper to send a mint/transfer transaction.
     */
    protected function sendMintTransaction(string $contract, string $selector, string $recipient, $val1, $val2 = null, bool $isErc1155 = false): ?string
    {
        if (empty($this->privateKey) || empty($contract)) {
            Log::error("Web3MintingService: Missing PRIVATE_KEY or CONTRACT_ADDRESS");
            return null;
        }

        try {
            $adminAddress = $this->privateKeyToAddress($this->privateKey);
            $nonce = $this->getTransactionCount($adminAddress);

            $paddedAddress = str_pad(str_replace('0x', '', $recipient), 64, '0', STR_PAD_LEFT);
            
            if ($isErc1155) {
                // ERC-1155 mint(address,id,amount,bytes)
                $paddedId = str_pad(dechex((int)$val1), 64, '0', STR_PAD_LEFT);
                $paddedAmount = str_pad(dechex((int)$val2), 64, '0', STR_PAD_LEFT);
                $dataPointer = str_pad(dechex(128), 64, '0', STR_PAD_LEFT);
                $dataLength = str_pad(dechex(0), 64, '0', STR_PAD_LEFT);
                $dataPayload = $selector . $paddedAddress . $paddedId . $paddedAmount . $dataPointer . $dataLength;
            } else {
                // ERC-20 mint(address,amount)
                // val1 is BigInteger
                $amountHex = $val1->toHex();
                $paddedAmount = str_pad($amountHex, 64, '0', STR_PAD_LEFT);
                $dataPayload = $selector . $paddedAddress . $paddedAmount;
            }

            $gasPriceHex = $this->getGasPrice();
            $gasLimitHex = dechex(150000);
            $chainId = env('EVM_CHAIN_ID', 421614); 

            $transaction = new Transaction(dechex($nonce), $gasPriceHex, $gasLimitHex, $contract, '0', $dataPayload);
            $signedTx = $transaction->getRaw($this->privateKey, $chainId);
            $txHash = $this->sendRawTransaction('0x' . $signedTx);

            Log::info("Web3MintingService: Tx sent. Hash: " . $txHash);
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
