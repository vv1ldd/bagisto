<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Customer\Services\HotWalletService;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Repositories\CustomerTransactionRepository;
use Illuminate\Support\Facades\Log;

class SbpController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Customer\Services\HotWalletService  $hotWalletService
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected HotWalletService $hotWalletService,
        protected CustomerRepository $customerRepository,
        protected CustomerTransactionRepository $customerTransactionRepository
    ) {}

    /**
     * Display the SBP confirmation bridge page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function confirm()
    {
        /** @var \Webkul\Sales\Models\Order $order */
        $orderId = session()->get('order_id');
        $order = null;

        if ($orderId) {
            $order = $this->orderRepository->find($orderId);
        }

        if (! $order) {
            $order = $this->orderRepository->where('customer_id', auth()->guard('customer')->id())
                ->where('created_at', '>=', now()->subMinutes(5))
                ->latest()
                ->first();
        }

        if (! $order) {
            return redirect()->route('shop.checkout.onepage.index');
        }

        if (! $order) {
            return redirect()->route('shop.checkout.onepage.index');
        }

        return view('shop::checkout.onepage.sbp-confirm', [
            'order'        => $order,
            'is_test_mode' => core()->getConfigData('sales.payment_methods.sbp.test_mode'),
        ]);
    }

    /**
     * SBP Webhook / Manual Callback Simulation
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback($orderId)
    {
        try {
            Log::info("SBP Callback: START", ['order_id' => $orderId]);

            $order = $this->orderRepository->find($orderId);
            if (! $order) $order = $this->orderRepository->findOneByField('increment_id', $orderId);

            if (! $order) return response()->json(['success' => false, 'error' => 'Order not found'], 404);
            
            $payment = $order->payment;
            if (! $payment) return response()->json(['success' => false, 'error' => 'Order payment not found'], 400);

            $additional = $payment->additional ?? [];
            if (is_string($additional)) $additional = json_decode($additional, true) ?? [];

            $additional['sbp_payment_received'] = true;
            
            $order->status = 'pending_payment';
            $order->save();

            $payment->update(['additional' => $additional]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("SBP Callback Error: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mint the base amount (1:1) after payment is received.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function mintBase($orderId)
    {
        try {
            Log::info("SBP Mint Base: START", ['order_id' => $orderId]);

            $order = $this->orderRepository->find($orderId);
            if (! $order) $order = $this->orderRepository->findOneByField('increment_id', $orderId);

            if (! $order) return response()->json(['success' => false, 'error' => 'Order not found'], 404);

            $payment = $order->payment;
            if (! $payment) return response()->json(['success' => false, 'error' => 'Order payment not found'], 400);

            $additional = $payment->additional ?? [];
            if (is_string($additional)) $additional = json_decode($additional, true) ?? [];

            $customer = $order->customer;
            
            $grandTotal = (float) $order->grand_total;
            Log::info("SBP Mint Base: MINTING amount {$grandTotal} for order #{$order->increment_id}");

            $tx1 = $this->hotWalletService->mintCoin(
                $customer, 
                $grandTotal, 
                "Оплата заказа #{$order->increment_id} (СБП 1:1)"
            );

            if (! $tx1) {
                throw new \Exception("Blockchain Minting Failed for 1:1 Base Amount");
            }

            // Sync with internal transaction history
            $this->customerTransactionRepository->create([
                'uuid'           => (string) Str::uuid(),
                'customer_id'    => $customer->id,
                'amount'         => $grandTotal,
                'type'           => 'deposit',
                'status'         => 'completed',
                'reference_type' => get_class($order),
                'reference_id'   => $order->id,
                'notes'          => "Минтинг по СБП (Заказ #{$order->increment_id})",
                'metadata'       => ['tx_hash' => $tx1],
            ]);

            $additional['mint_tx_base'] = $tx1;
            $additional['is_ready_for_passkey'] = true;

            $payment->update(['additional' => $additional]);

            return response()->json(['success' => true, 'tx' => $tx1]);
        } catch (\Exception $e) {
            Log::error("SBP Mint Base Error: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the current status of an order for state recovery.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($orderId)
    {
        try {
            $order = $this->orderRepository->find($orderId);
            if (! $order) $order = $this->orderRepository->findOneByField('increment_id', $orderId);
            
            if (! $order) return response()->json(['success' => false, 'error' => 'Not found'], 404);

            $payment = $order->payment;
            $additional = $payment?->additional ?? [];
            if (is_string($additional)) $additional = json_decode($additional, true) ?? [];

            return response()->json([
                'success'  => true,
                'received' => $additional['sbp_payment_received'] ?? false,
                'is_ready' => $additional['is_ready_for_passkey'] ?? false,
                'tx_base'  => $additional['mint_tx_base'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Mint the 5% bonus for the order.
     * Called from the success page for maximum "wow" factor.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function mintBonus($orderId)
    {
        try {
            $order = $this->orderRepository->find($orderId);
            if (! $order) $order = $this->orderRepository->findOneByField('increment_id', $orderId);

            if (! $order) return response()->json(['success' => false, 'message' => 'ORDER_NOT_FOUND'], 404);

            $payment = $order->payment;
            $additional = $payment?->additional ?? [];
            if (is_string($additional)) $additional = json_decode($additional, true) ?? [];

            if (! empty($additional['mint_tx_bonus'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'ALREADY_MINTED',
                    'tx'      => $additional['mint_tx_bonus']
                ]);
            }

            $bonusAmount = (float) $order->grand_total * 0.05;
            $txBonus = $this->hotWalletService->mintCoin($order->customer, $bonusAmount, "Order #{$order->increment_id} Bonus");

            if (! $txBonus) {
                throw new \Exception("Blockchain Minting Failed for Bonus Amount");
            }

            // Sync with internal transaction history
            $this->customerTransactionRepository->create([
                'uuid'           => (string) Str::uuid(),
                'customer_id'    => $order->customer_id,
                'amount'         => $bonusAmount,
                'type'           => 'cashback',
                'status'         => 'completed',
                'reference_type' => get_class($order),
                'reference_id'   => $order->id,
                'notes'          => "Бонус 5% за оплату через СБП (Заказ #{$order->increment_id})",
                'metadata'       => ['tx_hash' => $txBonus],
            ]);

            // Also mint a gift NFT for SBP orders
            $nftMetadata = "https://meanly.ru/api/nft/metadata/" . $order->increment_id; 
            $txNft = $this->hotWalletService->mintGift($order->customer, $nftMetadata, "Order #{$order->increment_id} SBP Reward");

            $additional['mint_tx_bonus'] = $txBonus;
            $additional['mint_tx_nft'] = $txNft;
            $additional['mint_amount_bonus'] = $bonusAmount;

            $payment->update(['additional' => $additional]);

            return response()->json([
                'success' => true, 
                'tx' => $txBonus, 
                'tx_nft' => $txNft,
                'amount' => $bonusAmount
            ]);
        } catch (\Exception $e) {
            Log::error("SBP Mint Bonus Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Finalize the order after Passkey confirmation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function finish(Request $request, $orderId)
    {
        try {
            $order = $this->orderRepository->find($orderId);
            if (! $order) $order = $this->orderRepository->findOneByField('increment_id', $orderId);

            if (! $order) return response()->json(['success' => false, 'message' => 'ORDER_NOT_FOUND'], 404);

            $payment = $order->payment;
            $additional = $payment?->additional ?? [];
            if (is_string($additional)) $additional = json_decode($additional, true) ?? [];

            if (! ($additional['sbp_payment_received'] ?? false)) {
                return response()->json(['success' => false, 'message' => 'NOT_PAID'], 400);
            }

            if (! $request->has('passkey_assertion')) {
                return response()->json(['success' => false, 'message' => 'SIGNATURE_MISSING'], 400);
            }

            $txHash = $request->input('passkey_assertion.id');
            $additional['web3_tx_hash'] = $txHash;
            $additional['finalized_at'] = now()->toDateTimeString();
            $additional['is_paid'] = true;

            $order->update(['status' => 'processing']);
            $payment->update(['method' => 'credits', 'additional' => $additional]);

            // Ensure order_id is in session for the success page
            session()->put('order_id', $order->id);

            return response()->json(['success' => true, 'message' => 'SUCCESS']);
        } catch (\Exception $e) {
            Log::error("SBP Finish Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
