<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Customer\Services\HotWalletService;
use Webkul\Customer\Models\CustomerTransaction;
use Illuminate\Support\Str;

class ProcessOrderCashbackJob implements ShouldQueue
{
    use Queueable;

    public $orderId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(OrderRepository $orderRepository, HotWalletService $hotWalletService)
    {
        $order = $orderRepository->find($this->orderId);

        if (!$order || !$order->customer_id) {
            Log::error("ProcessOrderCashbackJob: Order [{$this->orderId}] not found or guest checkout.");
            return;
        }

        $customer = $order->customer;
        $orderTotal = $order->base_grand_total; // Sum of order body
        $bonusAmount = $orderTotal * 0.05;      // 5% Cashback bonus

        Log::info("ProcessOrderCashbackJob: Starting DUAL minting for Order #{$order->increment_id} (Customer ID: {$customer->id})");

        // 1. DUAL-Minting: PART 1 - Body Refund (100%)
        try {
            $reasonBody = "Order Refund #{$order->increment_id}";
            $txBody = $hotWalletService->mintCoin($customer, $orderTotal, $reasonBody);

            if ($txBody) {
                $transactionBody = CustomerTransaction::create([
                    'uuid'           => Str::uuid(),
                    'customer_id'    => $customer->id,
                    'amount'         => $orderTotal,
                    'type'           => 'order_refund',
                    'status'         => 'pending',
                    'reference_type' => get_class($order),
                    'reference_id'   => $order->id,
                    'notes'          => "Возврат тела платежа (Заказ #{$order->increment_id})",
                    'metadata'       => [
                        'tx_hash' => $txBody,
                        'network' => 'arbitrum_one'
                    ],
                ]);

                // Update internal balance optimistically
                $customer->increment('balance', $orderTotal);

                // Start verification watcher
                VerifyWeb3TransactionJob::dispatch($transactionBody->id, $txBody);
            }
        } catch (\Exception $e) {
            Log::error("ProcessOrderCashbackJob [Body]: Minting failed for Order [{$this->orderId}]: " . $e->getMessage());
        }

        // 2. DUAL-Minting: PART 2 - Cashback Bonus (5%)
        try {
            $reasonBonus = "Cashback Bonus for Order #{$order->increment_id}";
            $txBonus = $hotWalletService->mintCoin($customer, $bonusAmount, $reasonBonus);

            if ($txBonus) {
                $transactionBonus = CustomerTransaction::create([
                    'uuid'           => Str::uuid(),
                    'customer_id'    => $customer->id,
                    'amount'         => $bonusAmount,
                    'type'           => 'cashback',
                    'status'         => 'pending',
                    'reference_type' => get_class($order),
                    'reference_id'   => $order->id,
                    'notes'          => "Бонус 5% за покупку (Заказ #{$order->increment_id})",
                    'metadata'       => [
                        'tx_hash' => $txBonus,
                        'network' => 'arbitrum_one'
                    ],
                ]);

                // Update internal balance optimistically
                $customer->increment('balance', $bonusAmount);

                // Start verification watcher
                VerifyWeb3TransactionJob::dispatch($transactionBonus->id, $txBonus);
            }
        } catch (\Exception $e) {
            Log::error("ProcessOrderCashbackJob [Bonus]: Minting failed for Order [{$this->orderId}]: " . $e->getMessage());
        }
    }
}
