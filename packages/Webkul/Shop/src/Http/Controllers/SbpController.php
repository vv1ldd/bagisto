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
                /** @var \Webkul\Sales\Models\Order $order */
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
        try {
            Log::info("SBP Callback: Payment Received Signal", ['order_id' => $orderId]);

            $order = $this->orderRepository->find($orderId);
            
            if (! $order) {
                // Try finding by increment_id just in case
                $order = $this->orderRepository->findOneByField('increment_id', $orderId);
            }

            if (! $order) {
                Log::error("SBP Callback: Order not found", ['order_id' => $orderId]);
                return response()->json(['success' => false, 'error' => 'Order not found'], 404);
            }
            
            $additional = $order->additional ?? [];
            if (is_string($additional)) {
                $additional = json_decode($additional, true) ?? [];
            }

            $additional['sbp_payment_received'] = true;
            
            $order->status = 'pending_payment';
            $order->additional = $additional;
            $order->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("SBP Callback Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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
            
            if (! $order) {
                $order = $this->orderRepository->findOneByField('increment_id', $orderId);
            }

            if (! $order) {
                return response()->json(['success' => false, 'error' => 'Order not found'], 404);
            }

            $customer = $order->customer;
            $additional = $order->additional ?? [];
            if (is_string($additional)) {
                $additional = json_decode($additional, true) ?? [];
            }

            // 1. Mint Base Amount (1:1 RUB to MC)
            Log::info("SBP Mint Base: Minting base coins", ['customer' => $customer?->email, 'amount' => $order->grand_total]);
            
            $tx1 = $this->hotWalletService->mintCoin(
                $customer, 
                (float) $order->grand_total, 
                "Оплата заказа #{$order->increment_id} (СБП 1:1)"
            );

            $additional['mint_tx_base'] = $tx1;
            $additional['mint_amount_base'] = (float) $order->grand_total;
            $additional['is_ready_for_passkey'] = true;

            $order->additional = $additional;
            $order->save();

            Log::info("SBP Mint Base: SUCCESS", [
                'order_id' => $order->id,
                'tx_base' => $tx1
            ]);

            return response()->json([
                'success' => true,
                'tx'      => $tx1,
            ]);
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
            
            if (! $order) {
                $order = $this->orderRepository->findOneByField('increment_id', $orderId);
            }
            
            if (! $order) return response()->json(['success' => false, 'error' => 'Not found'], 404);

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
            
            if (! $order) {
                $order = $this->orderRepository->findOneByField('increment_id', $orderId);
            }

            if (! $order) {
                return response()->json(['success' => false, 'message' => 'ORDER_NOT_FOUND'], 404);
            }

            $additional = $order->additional;
            if (is_string($additional)) {
                $additional = json_decode($additional, true) ?? [];
            }

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
            Log::info("SBP Finish Attempt: START", ['order_id' => $orderId]);
            
            $order = $this->orderRepository->find($orderId);
            
            if (! $order) {
                $order = $this->orderRepository->findOneByField('increment_id', $orderId);
            }

            if (! $order) {
                return response()->json(['success' => false, 'message' => 'ORDER_NOT_FOUND'], 404);
            }

            $additional = $order->additional;
            if (is_string($additional)) {
                $additional = json_decode($additional, true) ?? [];
            }

            Log::info("SBP Finish Check", [
                'order_id' => $orderId,
                'received_flag' => $additional['sbp_payment_received'] ?? 'not_found'
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
            if ($order->payment) {
                $order->payment->update(['method' => 'credits']);
            }

            Log::info("SBP Finish SUCCESS", ['order_id' => $orderId, 'tx' => $txHash]);

            return response()->json([
                'success' => true,
                'message' => 'SUCCESS'
            ]);
        } catch (\Exception $e) {
            Log::error("SBP Finish Fatal Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
