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
        Web3MintingService $mintingService,
        CustomerRepository $customerRepository,
        OrderRepository $orderRepository
    ) {
        $customer = $customerRepository->find($this->customerId);
        $order = $orderRepository->find($this->orderId);

        if (!$customer || !$order) {
            Log::error("ProcessCashbackMintingJob: Customer [{$this->customerId}] or Order [{$this->orderId}] not found.");
            return;
        }

        // Only mint if they have an active crypto address (credits_id starting with 0x)
        if (empty($customer->credits_id) || strpos($customer->credits_id, '0x') !== 0) {
            Log::warning("ProcessCashbackMintingJob: Customer [{$customer->id}] does not have a valid 0x credits_id for on-chain cashback.");
            return;
        }

        Log::info("ProcessCashbackMintingJob: Starting On-Chain Cashback Minting for Customer [{$customer->id}] and Order [{$order->id}]. Amount: {$this->amount}");

        // Mint the cashback coins
        $txHash = $mintingService->mintCashbackCoin($customer->credits_id, $this->amount);

        if ($txHash) {
            Log::info("ProcessCashbackMintingJob: Successfully minted cashback tokens. TxHash: {$txHash}");
            
            // Record this in the wallet history
            CustomerTransaction::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'customer_id' => $customer->id,
                'amount' => $this->amount,
                'type' => 'cashback',
                'status' => 'completed',
                'reference_type' => get_class($order),
                'reference_id' => $order->id,
                'notes' => "On-Chain Cashback ({$this->currency}) for Order #{$order->increment_id}",
                'metadata' => [
                    'transaction_id' => $txHash,
                    'network' => 'arbitrum_one',
                    'currency' => $this->currency,
                ],
            ]);
        } else {
            Log::error("ProcessCashbackMintingJob: Failed to mint cashback tokens for Customer [{$customer->id}].");
            // Throwing exception so the queue worker retries (depending on queue config)
            throw new \Exception("Web3 Cashback Minting Failed.");
        }
    }
}
