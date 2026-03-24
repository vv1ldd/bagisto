

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
    public function mintCoin(Customer $customer, float $amount): ?string
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
        // e.g., 5.5 = 5500000000000000000
        $amountInWei = bcmul((string) $amount, bcpow('10', '18', 0), 0);
        
        // mint(address,uint256) signature is 0x40c10f19
        $functionSignature = '40c10f19';
        $paddedAddress = str_pad(str_replace('0x', '', $cryptoAddress), 64, '0', STR_PAD_LEFT);
        
        // Convert wei amount to hex
        $hexAmount = dechex((int)$amountInWei); // Simple conversion, might need BigInteger for very large amounts
        // Let's use a robust hex converter for large numbers
        $hexAmount = $this->bcdechex($amountInWei);
        $paddedAmount = str_pad($hexAmount, 64, '0', STR_PAD_LEFT);

        $data = $functionSignature . $paddedAddress . $paddedAmount;

        return $this->sendTransaction($this->coinAddress, $data);
    }

    /**
     * Mints a Meanly Gift NFT (ERC721) to a user's verified crypto address.
     */
    public function mintGift(Customer $customer, string $metadataUri): ?string
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

        // safeMint(address,string) signature is 0xd204c45e
        $functionSignature = 'd204c45e';
        $paddedAddress = str_pad(str_replace('0x', '', $cryptoAddress), 64, '0', STR_PAD_LEFT);
        
        // Encode dynamic string type for ABI (Offset, Length, Data, Padding)
        $offset = str_pad(dechex(64), 64, '0', STR_PAD_LEFT); // Offset is always 0x40 (64 bytes) for the second parameter here
        $length = str_pad(dechex(strlen($metadataUri)), 64, '0', STR_PAD_LEFT);
        $encodedString = bin2hex($metadataUri);
        $paddingBytes = 64 - (strlen($encodedString) % 64);
        if ($paddingBytes < 64) {
            $encodedString .= str_repeat('0', $paddingBytes);
        }

        $data = $functionSignature . $paddedAddress . $offset . $length . $encodedString;

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
            ->where('network', '=', 'arbitrum_one')
            ->whereNotNull('verified_at')
            ->first();

        return $addressRecord ? $addressRecord->address : $customer->credits_id;
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
}
