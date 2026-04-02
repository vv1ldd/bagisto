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
        // Get the latest order for the current customer
        $order = $this->orderRepository->where('customer_id', auth()->guard('customer')->id())
            ->latest()
            ->first();

        if (! $order) {
            return redirect()->route('shop.checkout.cart.index');
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

        // Dispatch NFT/Cashback service
        \App\Jobs\ProcessOrderCashbackJob::dispatch($order->id);

        return response()->json([
            'success' => true,
            'message' => 'Заказ успешно подтвержден и оплачен.'
        ]);
    }
}
