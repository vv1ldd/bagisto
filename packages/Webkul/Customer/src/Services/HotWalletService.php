<?php

namespace Webkul\Customer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kornrunner\Ethereum\Transaction;
use kornrunner\Keccak;
use Webkul\Customer\Models\Customer;

class HotWalletService
{
    protected string $rpcUrl;
    protected string $privateKey;
    protected string $hotWalletAddress;
    protected string $coinAddress;
    protected string $nftAddress;
    protected int $chainId;

    public function __construct()
    {
        $this->rpcUrl = config('crypto.rpc_url_arbitrum', 'https://arb1.arbitrum.io/rpc');
        $this->privateKey = config('crypto.hot_wallet_private_key', '');
        $this->coinAddress = config('crypto.meanly_coin_address', '');
        $this->nftAddress = config('crypto.meanly_gift_address', '');
        $this->hotWalletAddress = config('crypto.hot_wallet_address', '');
        $this->chainId = (int) config('crypto.arbitrum_chain_id', 42161);
    }

    /**
     * Mints Meanly Coin (ERC20) to a user's verified crypto address.
     */
    public function mintCoin(Customer $customer, float $amount, string $reason = 'Cashback'): ?string
    {
        if (empty($this->privateKey) || empty($this->coinAddress)) {
            Log::error("HotWalletService: Missing PK or Coin Address configuration.");
            return null;
        }

        $cryptoAddress = $this->getPrimaryCryptoAddress($customer);
        if (!$cryptoAddress) {
            Log::error("HotWalletService: Customer {$customer->id} does not have a verified Arbitrum address.");
            return null;
        }

        // Amount needs to be converted to wei (assuming 18 decimals)
        $amountInWei = bcmul((string) $amount, bcpow('10', '18', 0), 0);
        
        // mint(address,uint256,string) signature is 0xd3fc9864
        $functionSignature = 'd3fc9864';
        
        // Encode arguments: address (32), uint256 (32), string offset (32), string length (32), string data (...)
        $data = $functionSignature;
        $data .= str_pad(str_replace('0x', '', strtolower($cryptoAddress)), 64, '0', STR_PAD_LEFT);
        $data .= str_pad($this->bcdechex($amountInWei), 64, '0', STR_PAD_LEFT);
        
        // Offset for string starts after the first 3 static slots (3 * 32 = 96 = 0x60)
        $data .= str_pad(dechex(96), 64, '0', STR_PAD_LEFT);
        
        // String data (length + content padding)
        $data .= $this->encodeString($reason);

        return $this->sendTransaction($this->coinAddress, $data);
    }

    /**
     * Mints a Meanly Gift NFT (ERC721) to a user's verified crypto address.
     */
    public function mintGift(Customer $customer, string $metadataUri, string $reason = 'Purchase Gift'): ?string
    {
        if (empty($this->privateKey) || empty($this->nftAddress)) {
            Log::error("HotWalletService: Missing PK or NFT Address configuration.");
            return null;
        }

        $cryptoAddress = $this->getPrimaryCryptoAddress($customer);
        if (!$cryptoAddress) {
            Log::error("HotWalletService: Customer {$customer->id} does not have a verified Arbitrum address.");
            return null;
        }

        // safeMint(address,string,string) signature is 0xeca81d42
        $functionSignature = 'eca81d42';
        
        // Encode arguments: address (32), string1 offset (32), string2 offset (32)
        $data = $functionSignature;
        $data .= str_pad(str_replace('0x', '', strtolower($cryptoAddress)), 64, '0', STR_PAD_LEFT);
        
        // Offset for string 1 starts after 3 static slots (3 * 32 = 96 = 0x60)
        $data .= str_pad(dechex(96), 64, '0', STR_PAD_LEFT);
        
        // Calculate offset for string 2: 96 + 32 (length1) + padded data1
        $encodedString1 = $this->encodeString($metadataUri);
        $offset2 = 96 + (strlen($encodedString1) / 2); // Divide by 2 because it's hex
        $data .= str_pad(dechex($offset2), 64, '0', STR_PAD_LEFT);
        
        $data .= $encodedString1;
        $data .= $this->encodeString($reason);

        return $this->sendTransaction($this->nftAddress, $data);
    }

    /**
     * Sends raw ETH from the hot wallet to an address.
     */
    public function sendEth(string $to, float $amount): ?string
    {
        if (empty($this->privateKey)) {
            Log::error("HotWalletService: Missing PK configuration.");
            return null;
        }

        // Convert amount to wei
        $amountInWei = bcmul((string) $amount, bcpow('10', '18', 0), 0);
        $hexValue = '0x' . $this->bcdechex($amountInWei);

        // Simple ETH transfer has no data
        return $this->sendTransaction($to, '', $hexValue);
    }

    /**
     * Creates, signs, and broadcasts a raw transaction.
     */
    protected function sendTransaction(string $toAddress, string $data, string $valueHex = '0x0'): ?string
    {
        try {
            // Use configured hot wallet address instead of deriving it
            $hotWalletAddress = $this->hotWalletAddress;

            if (empty($hotWalletAddress)) {
                throw new \Exception("HOT_WALLET_ADDRESS is not configured.");
            }

            // Fetch current nonce
            $nonceHex = $this->rpcCall('eth_getTransactionCount', [$hotWalletAddress, 'pending']);
            $nonce = hexdec($nonceHex);

            // Fetch gas price (Arbitrum is very cheap, but we read it dynamically)
            $gasPriceHex = $this->rpcCall('eth_gasPrice', []);
            
            // Adding a 10% buffer to Gas Price to ensure it goes through fast
            $gasPrice = hexdec($gasPriceHex);
            $gasPriceBuffered = dechex((int)($gasPrice * 1.1));

            // Estimate Gas Limit
            $estimateGasParams = [
                'from' => $hotWalletAddress,
                'to' => $toAddress,
                'data' => (strpos($data, '0x') === 0) ? $data : '0x' . $data,
                'value' => $valueHex
            ];
            
            try {
                $gasLimitHex = $this->rpcCall('eth_estimateGas', [$estimateGasParams]);
            } catch (\Exception $e) {
                // If estimation fails (e.g. simple transfer), use standard 21k for ETH or a safe default for contracts
                $gasLimitHex = (empty($data)) ? dechex(21000) : dechex(500000);
            }

            $gasLimit = hexdec($gasLimitHex);
            // Add a safety buffer to the gas limit
            $gasLimitBuffered = dechex((int)($gasLimit * 1.5));

            // Build Legacy Transaction Structure
            $transaction = new Transaction(
                dechex($nonce),
                $gasPriceBuffered,
                $gasLimitBuffered,
                $toAddress,
                $valueHex,
                $data
            );

            // Sign
            $rawTx = $transaction->getRaw($this->privateKey, $this->chainId);

            // Broadcast
            $txHash = $this->rpcCall('eth_sendRawTransaction', ['0x' . $rawTx]);
            
            Log::info("HotWalletService: Transaction sent successfully. Hash: {$txHash}");
            return $txHash;

        } catch (\Exception $e) {
            Log::error("HotWalletService: Failed to send transaction: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Checks the receipt of a transaction to see if it was successful.
     */
    public function getTransactionReceipt(string $txHash): ?array
    {
        try {
            $receipt = $this->rpcCall('eth_getTransactionReceipt', [$txHash]);
            
            if ($receipt) {
                return [
                    'status' => hexdec($receipt['status'] ?? '0x0') === 1 ? 'success' : 'failed',
                    'blockNumber' => hexdec($receipt['blockNumber'] ?? '0x0'),
                ];
            }
            
            return null; // Still pending or not found
        } catch (\Exception $e) {
            Log::error("HotWalletService: Failed to get transaction receipt: " . $e->getMessage());
            return null;
        }
    }
    /**
     * RPC Helper block
     */
    protected function rpcCall(string $method, array $params)
    {
        $response = Http::post($this->rpcUrl, [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => uniqid()
        ]);

        $body = $response->json();

        if (isset($body['error'])) {
            throw new \Exception("RPC Error [{$method}]: " . json_encode($body['error']));
        }

        return $body['result'] ?? null;
    }

    protected function getPrimaryCryptoAddress(Customer $customer): ?string
    {
        $addressRecord = $customer->crypto_addresses()
            ->where('network', 'arbitrum_one')
            ->whereNotNull('verified_at')
            ->first();

        if ($addressRecord && ! empty($addressRecord->address)) {
            return (string) $addressRecord->address;
        }

        return $customer->credits_id;
    }

    protected function bcdechex(string $dec): string
    {
        $hex = '';
        do {
            $last = bcmod($dec, 16);
            $hex = dechex((int) $last) . $hex;
            $dec = bcdiv(bcsub($dec, $last), 16);
        } while ($dec > 0);
        return $hex ?: '0';
    }

    protected function encodeString(string $str): string
    {
        $hexStr = bin2hex($str);
        $length = strlen($str);
        
        $encoded = str_pad(dechex($length), 64, '0', STR_PAD_LEFT);
        $encoded .= $hexStr;
        
        // Pad to multiple of 32 bytes (64 hex characters)
        $paddingBytes = 64 - (strlen($hexStr) % 64);
        if ($paddingBytes < 64) {
            $encoded .= str_repeat('0', $paddingBytes);
        }
        
        return $encoded;
    }
}
