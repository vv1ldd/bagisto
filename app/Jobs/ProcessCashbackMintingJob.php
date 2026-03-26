<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Webkul\Shop\Services\Web3MintingService;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Customer\Models\CustomerTransaction;

class ProcessCashbackMintingJob implements ShouldQueue
{
    use Queueable;

    public $orderId;
    public $customerId;
    public $amount;
    public $currency;

    /**
     * Create a new job instance.
     * 
     * @param int $orderId
     * @param int $customerId
     * @param float $amount The cashback amount in coins
     * @param string $currency The code of the currency used for minting
     */
    public function __construct(int $orderId, int $customerId, float $amount, string $currency = 'RUB')
    {
        $this->orderId = $orderId;
        $this->customerId = $customerId;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Execute the job.
     */
    public function handle(
        \Webkul\Customer\Services\HotWalletService $hotWalletService,
        CustomerRepository $customerRepository,
        OrderRepository $orderRepository
    ) {
        $customer = $customerRepository->find($this->customerId);
        $order = $orderRepository->find($this->orderId);

        if (!$customer || !$order) {
            Log::error("ProcessCashbackMintingJob: Customer [{$this->customerId}] or Order [{$this->orderId}] not found.");
            return;
        }

        // Get primary crypto address (using service logic or fallback)
        $cryptoAddress = $this->getRecipientAddress($customer);
        if (!$cryptoAddress) {
            Log::warning("ProcessCashbackMintingJob: No verified Arbitrum address for Customer [{$customer->id}]. Skipping on-chain minting.");
            return;
        }

        Log::info("ProcessCashbackMintingJob: Starting On-Chain Minting for Customer [{$customer->id}] and Order [{$order->id}].");

        // 1. Mint Cashback Coin (ERC20)
        $coinTxHash = null;
        if ($this->amount > 0) {
            $reason = "Order #{$order->increment_id} Cashback";
            Log::info("ProcessCashbackMintingJob: Minting {$this->amount} {$this->currency} for {$cryptoAddress} (Reason: {$reason})");
            $coinTxHash = $hotWalletService->mintCoin($customer, $this->amount, $reason);
        }

        // 2. Mint Gift NFT (ERC721)
        $nftMetadataUri = url("/nft/metadata/{$order->id}");
        $nftReason = "Order #{$order->increment_id} Gift";
        Log::info("ProcessCashbackMintingJob: Minting Gift NFT for {$cryptoAddress} (Reason: {$nftReason})");
        $nftTxHash = $hotWalletService->mintGift($customer, $nftMetadataUri, $nftReason);

        if ($coinTxHash || $nftTxHash) {
            // Record this in the wallet history
            CustomerTransaction::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'customer_id' => $customer->id,
                'amount' => $this->amount,
                'type' => 'cashback',
                'status' => 'completed',
                'reference_type' => get_class($order),
                'reference_id' => $order->id,
                'notes' => "On-Chain Reward for Order #{$order->increment_id}",
                'metadata' => [
                    'coin_tx' => $coinTxHash,
                    'nft_tx' => $nftTxHash,
                    'network' => 'arbitrum_one',
                    'currency' => $this->currency,
                    'address' => $cryptoAddress,
                ],
            ]);
            
            Log::info("ProcessCashbackMintingJob: Completed. CoinTx: " . ($coinTxHash ?? 'none') . " | NftTx: " . ($nftTxHash ?? 'none'));
        } else {
            Log::error("ProcessCashbackMintingJob: Failed both minting operations for Customer [{$customer->id}].");
            throw new \Exception("Web3 Minting Failed.");
        }
    }

    protected function getRecipientAddress($customer): ?string
    {
        $addressRecord = $customer->crypto_addresses()
            ->where('network', '=', 'arbitrum_one')
            ->whereNotNull('verified_at')
            ->first();

        return $addressRecord ? $addressRecord->address : (str_starts_with((string)$customer->credits_id, '0x') ? $customer->credits_id : null);
    }
}
