<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Customer\Services\HotWalletService;
use Webkul\Customer\Repositories\CustomerRepository;
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
        protected CustomerRepository $customerRepository
    ) {}

    /**
     * Display the SBP confirmation bridge page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function confirm()
    {
        // 1. Check if we already have an order (e.g. page refresh)
        $order = $this->orderRepository->where('customer_id', auth()->guard('customer')->id())
            ->where('created_at', '>=', now()->subMinutes(5))
            ->latest()
            ->first();

        // 2. If no recent order, try to create one from the current cart
        if (! $order) {
            $cart = \Webkul\Checkout\Facades\Cart::getCart();
            
            if ($cart) {
                // Ensure totals are fresh
                \Webkul\Checkout\Facades\Cart::collectTotals();
                
                // Preparing data and creating order
                $data = (new \Webkul\Sales\Transformers\OrderResource($cart))->jsonSerialize();
                $order = $this->orderRepository->create($data);
                
                // Deactivate cart after order creation
                \Webkul\Checkout\Facades\Cart::deActivateCart();
                session()->flash('order_id', $order->id);
            }
        }

        if (! $order) {
            // If still no order and no cart, redirect to checkout
            return redirect()->route('shop.checkout.onepage.index');
        }

        return view('shop::checkout.onepage.sbp-confirm', [
            'order'        => $order,
            'is_test_mode' => core()->getConfigData('sales.payment_methods.sbp.test_mode'),
        ]);
    }

    /**
     * Handle SBP payment callback (simulated or real).
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);
        $customer = $order->customer;

        if (! $customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        // 1. Mint Base Amount (1:1 RUB to MC)
        $tx1 = $this->hotWalletService->mintCoin(
            $customer, 
            (float) $order->grand_total, 
            "Оплата заказа #{$order->increment_id} (СБП 1:1)"
        );

        // Update order status and save transaction proofs
        $additional = $order->additional ?? [];
        if (is_string($additional)) {
            $additional = json_decode($additional, true) ?? [];
        }

        $additional['sbp_payment_received'] = true;
        $additional['mint_tx_base'] = $tx1;
        $additional['mint_amount_base'] = (float) $order->grand_total;
        $additional['is_ready_for_passkey'] = true;

        Log::info("SBP Callback: Payment received and base minted", [
            'order_id' => $order->id,
            'tx_base' => $tx1
        ]);

        $order->update([
            'status'     => 'pending_payment',
            'additional' => $additional,
        ]);

        Log::info("SBP Callback Processed", [
            'order' => $order->increment_id,
            'base_tx' => $tx1,
        ]);

        return response()->json([
            'success' => true,
            'tx'      => $tx1,
            'amount'  => (float) $order->grand_total,
        ]);
    }

    /**
     * Get the current status of an order for state recovery.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        $additional = $order->additional;
        if (is_string($additional)) {
            $additional = json_decode($additional, true);
        }

        return response()->json([
            'success'  => true,
            'received' => $additional['sbp_payment_received'] ?? false,
            'is_ready' => $additional['is_ready_for_passkey'] ?? false,
            'tx_base'  => $additional['mint_tx_base'] ?? null,
        ]);
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
        $order = $this->orderRepository->findOrFail($orderId);
        $additional = $order->additional;

        if (!empty($additional['mint_tx_bonus'])) {
            return response()->json([
                'success' => true,
                'message' => 'ALREADY_MINTED',
                'tx' => $additional['mint_tx_bonus']
            ]);
        }

        $bonusAmount = (float) $order->grand_total * 0.05;
        
        Log::info("SBP: Minting bonus for order", ['order_id' => $orderId, 'amount' => $bonusAmount]);

        $txBonus = $this->hotWalletService->mintCoin(
            $order->customer, 
            $bonusAmount,
            "Order #{$order->increment_id} Bonus"
        );

        $additional['mint_tx_bonus'] = $txBonus;
        $additional['mint_amount_bonus'] = $bonusAmount;

        $order->update(['additional' => $additional]);

        return response()->json([
            'success' => true,
            'tx'      => $txBonus,
            'amount'  => $bonusAmount
        ]);
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
        $order = $this->orderRepository->findOrFail($orderId);

        $additional = $order->additional;
        if (is_string($additional)) {
            $additional = json_decode($additional, true);
        }

        Log::info("SBP Finish Attempt", [
            'order_id' => $orderId,
            'received_flag' => $additional['sbp_payment_received'] ?? 'not_found',
            'additional_dump' => $additional
        ]);

        // 1. Verify SBP Payment status
        if (! ($additional['sbp_payment_received'] ?? false)) {
            Log::error("SBP Finish: Order not marked as paid", ['order_id' => $orderId]);
            return response()->json([
                'success' => false, 
                'message' => 'NOT_PAID'
            ], 400);
        }

        // 2. Validate Passkey presence
        if (! $request->has('passkey_assertion')) {
            return response()->json([
                'success' => false,
                'message' => 'SIGNATURE_MISSING'
            ], 400);
        }

        $txHash = $request->input('passkey_assertion.id');

        $additional['web3_tx_hash'] = $txHash;
        $additional['finalized_at'] = now()->toDateTimeString();
        $additional['is_paid'] = true;

        $order->update([
            'status'     => 'processing',
            'additional' => $additional,
        ]);

        // Record the transaction in the history for visibility
        $order->payment->update(['method' => 'credits']);

        return response()->json([
            'success' => true,
            'message' => 'SUCCESS'
        ]);
    }
}
