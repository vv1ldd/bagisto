<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Webkul\Shop\Services\Web3MintingService;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Sales\Repositories\OrderRepository;

class ProcessNftMintingJob implements ShouldQueue
{
    use Queueable;

    public $orderId;
    public $customerId;
    public $tokenId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId, int $customerId, int $tokenId = 1)
    {
        $this->orderId = $orderId;
        $this->customerId = $customerId;
        $this->tokenId = $tokenId;
    }

    /**
     * Execute the job.
     */
    public function handle(
        Web3MintingService $mintingService,
        CustomerRepository $customerRepository,
        OrderRepository $orderRepository
    ) {
        $customer = $customerRepository->find($this->customerId);
        $order = $orderRepository->find($this->orderId);

        if (!$customer || !$order) {
            Log::error("ProcessNftMintingJob: Customer or Order not found.");
            return;
        }

        // Only mint if they have an active crypto address
        if (empty($customer->credits_id) || strpos($customer->credits_id, '0x') !== 0) {
            Log::warning("ProcessNftMintingJob: Customer [{$customer->id}] does not have a valid 0x credits_id.");
            return;
        }

        Log::info("ProcessNftMintingJob: Starting NFT Minting for Customer [{$customer->id}] and Order [{$order->id}]");

        // Mint 1 copy of the token
        $txHash = $mintingService->mintGiftNft($customer->credits_id, $this->tokenId, 1);

        if ($txHash) {
            Log::info("ProcessNftMintingJob: Successfully minted NFT. TxHash: {$txHash}");
            
            // Log this transaction locally so the user can see it in their history
            \Webkul\Customer\Models\CustomerTransaction::create([
                'customer_id' => $customer->id,
                'transaction_id' => $txHash,
                'type' => 'nft_gift',
                'amount' => 1,
                'status' => 'completed',
                'currency' => 'MGF', // Meanly Gift Token
                'title' => 'Подарочный NFT за заказ #' . $order->id,
            ]);
        } else {
            Log::error("ProcessNftMintingJob: Failed to mint NFT for Customer [{$customer->id}].");
            // You might throw an exception here so the Queue worker retries it, 
            // but ensure your RPC logic handles failed transactions safely.
            // throw new \Exception("Web3 Minting Failed.");
        }
    }
}
