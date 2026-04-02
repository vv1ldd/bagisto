<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Customer\Services\HotWalletService;
use Webkul\Customer\Repositories\CustomerRepository;

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

        return view('shop::checkout.onepage.sbp-confirm', compact('order'));
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

        // 1. Mint Base Amount (The payment body)
        $tx1 = $this->hotWalletService->mintCoin(
            $customer, 
            (float) $order->grand_total, 
            "Зачисление средств для заказа #{$order->increment_id}"
        );

        // 2. Mint Bonus (5% Cashback for SBP)
        $bonusAmount = (float) $order->grand_total * 0.05;
        $tx2 = $this->hotWalletService->mintCoin(
            $customer, 
            $bonusAmount, 
            "Бонус +5% СБП за заказ #{$order->increment_id}"
        );

        // Update order status and save transaction proofs
        $additional = $order->additional ?? [];
        $additional['sbp_payment_received'] = true;
        $additional['mint_tx_base'] = $tx1;
        $additional['mint_tx_bonus'] = $tx2;

        $order->update([
            'status'     => 'pending_payment',
            'additional' => $additional,
        ]);

        return response()->json([
            'success' => true,
            'tx1'     => $tx1,
            'tx2'     => $tx2,
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

        // 1. Verify SBP Payment was actually received and minted
        if (! ($order->additional['sbp_payment_received'] ?? false)) {
            return response()->json([
                'success' => false, 
                'message' => 'Оплата СБП не подтверждена или монеты еще не на числены.'
            ], 400);
        }

        // 2. Change payment method to 'credits' for internal accounting
        // and record the web3 transaction hash as proof
        $order->payment->update(['method' => 'credits']);

        $txHash = $request->input('web3_tx_hash', 'sbp_' . Str::random(20));

        $additional = $order->additional ?? [];
        $additional['web3_tx_hash'] = $txHash;
        $additional['finalized_at'] = now()->toDateTimeString();

        $order->update([
            'additional' => $additional,
        ]);

        // Trigger balance deduction and purchase logging
        $this->orderRepository->deductCreditsForOrder($order, $txHash);

        return response()->json([
            'success' => true,
            'message' => 'Заказ успешно подтвержден и оплачен.'
        ]);
    }
}
