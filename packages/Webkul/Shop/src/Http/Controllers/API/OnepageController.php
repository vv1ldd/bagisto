<?php

namespace Webkul\Shop\Http\Controllers\API;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Webkul\Checkout\Facades\Cart;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Payment\Facades\Payment;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;
use Webkul\Shipping\Facades\Shipping;
use Webkul\Shop\Http\Requests\CartAddressRequest;
use Webkul\Shop\Http\Resources\CartResource;
use Illuminate\Support\Facades\Mail;
use Webkul\Shop\Mail\Customer\OtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\VerifyWeb3TransactionJob;
use App\Jobs\ProcessOrderCashbackJob;

class OnepageController extends APIController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected CustomerRepository $customerRepository
    ) {
    }

    /**
     * Return cart summary.
     */
    public function summary(): JsonResource
    {
        $cart = Cart::getCart();

        return new CartResource($cart);
    }

    /**
     * Send OTP to customer email.
     */
    public function sendOtp(Request $request): object
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $otp = (string) rand(100000, 999999);

        session([
            'checkout_otp_email' => $email,
            'checkout_otp_code' => $otp,
            'checkout_otp_verified' => false,
        ]);

        try {
            Mail::queue(new OtpNotification($email, $otp));

            return new JsonResource([
                'success' => true,
                'message' => 'Код отправлен на вашу почту.',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("OTP Send Error: " . $e->getMessage());
            return (new JsonResource([
                'success' => false,
                'message' => 'Не удалось отправить код. Пожалуйста, попробуйте позже.',
            ]))->response()->setStatusCode(500);
        }
    }

    /**
     * Verify OTP code.
     */
    public function verifyOtp(Request $request): object
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $storedEmail = session('checkout_otp_email');
        $storedOtp = session('checkout_otp_code');

        if ($request->input('code') === $storedOtp) {
            session(['checkout_otp_verified' => true]);

            return new JsonResource([
                'success' => true,
                'message' => 'Email успешно подтвержден.',
            ]);
        }

        return (new JsonResource([
            'success' => false,
            'message' => 'Неверный код. Пожалуйста, проверьте и попробуйте снова.',
        ]))->response()->setStatusCode(422);
    }

    /**
     * Store address.
     */
    public function storeAddress(CartAddressRequest $cartAddressRequest): JsonResource
    {
        $params = $cartAddressRequest->all();

        if (
            !auth()->guard('customer')->check()
            && !Cart::getCart()->hasGuestCheckoutItems()
        ) {
            return new JsonResource([
                'redirect' => true,
                'data' => route('shop.customer.session.index'),
            ]);
        }

        if (
            !auth()->guard('customer')->check()
            && !session('checkout_otp_verified')
        ) {
            return new JsonResource([
                'error' => true,
                'message' => 'Необходимо подтвердить Email через OTP.',
            ]);
        }

        if (Cart::hasError()) {
            return new JsonResource([
                'redirect' => true,
                'redirect_url' => route('shop.checkout.cart.index'),
            ]);
        }

        Cart::saveAddresses($params);

        if ($cart = Cart::getCart()) {
            if (!empty($params['billing']['is_gift'])) {
                $additional = $cart->additional ?? [];

                $additional['is_gift'] = true;
                $additional['gift_email'] = $params['billing']['gift_email'] ?? null;

                $cart->additional = $additional;
                $cart->save();
            }
        }

        $cart = Cart::getCart();

        Cart::collectTotals();

        if ($cart->haveStockableItems()) {
            if (!$rates = Shipping::collectRates()) {
                return new JsonResource([
                    'redirect' => true,
                    'redirect_url' => route('shop.checkout.cart.index'),
                ]);
            }

            return new JsonResource([
                'redirect' => false,
                'data' => $rates,
            ]);
        }

        return new JsonResource([
            'redirect' => false,
            'data' => Payment::getSupportedPaymentMethods(),
        ]);
    }

    /**
     * Return supported payment methods.
     */
    public function paymentMethods()
    {
        try {
            $data = Payment::getSupportedPaymentMethods();
            return response()->json($data);
        } catch (\Throwable $e) {
            return response()->json(['payment_methods' => []], 200);
        }
    }

    /**
     * Store shipping method.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeShippingMethod()
    {
        $validatedData = $this->validate(request(), [
            'shipping_method' => 'required',
        ]);

        if (
            Cart::hasError()
            || !$validatedData['shipping_method']
            || !Cart::saveShippingMethod($validatedData['shipping_method'])
        ) {
            return response()->json([
                'redirect_url' => route('shop.checkout.cart.index'),
            ], Response::HTTP_FORBIDDEN);
        }

        Cart::collectTotals();

        return response()->json(Payment::getSupportedPaymentMethods());
    }

    /**
     * Store payment method.
     *
     * @return array
     */
    public function storePaymentMethod()
    {
        $validatedData = $this->validate(request(), [
            'payment' => 'required',
        ]);

        if (
            Cart::hasError()
            || !$validatedData['payment']
            || !Cart::savePaymentMethod($validatedData['payment'])
        ) {
            return response()->json([
                'redirect_url' => route('shop.checkout.cart.index'),
            ], Response::HTTP_FORBIDDEN);
        }

        Cart::collectTotals();

        $cart = Cart::getCart();

        return [
            'cart' => new CartResource($cart),
        ];
    }

    /**
     * Store order
     */
    public function storeOrder()
    {
        if (Cart::hasError()) {
            return new JsonResource([
                'redirect' => true,
                'redirect_url' => route('shop.checkout.cart.index'),
            ]);
        }

        Cart::collectTotals();

        $cart = Cart::getCart();

        // For digital-only carts auto-save billing from customer profile if not set.
        if ($cart && !$cart->haveStockableItems() && !$cart->billing_address) {
            $this->autoSaveBillingFromProfile();
            $cart = Cart::getCart();
        }

        try {
            $this->validateOrder();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        $cart = Cart::getCart();

        // ----------------------------------------------------
        // HOT WALLET GASLESS RELAY: Meanly Wallet Interception
        // ----------------------------------------------------
        if ($cart->payment->method === 'credits') {
            $passkeyAssertion = request()->input('passkey_assertion');
            
            if (!$passkeyAssertion) {
                return response()->json([
                    'message' => 'Биометрическая подпись (Passkey) обязательна.',
                ], 422);
            }

            try {
                $signer = app(\Webkul\Customer\Services\PasskeyWeb3Signer::class);
                $txHash = $signer->processGaslessCheckout(
                    auth()->guard('customer')->user(),
                    is_array($passkeyAssertion) ? $passkeyAssertion : json_decode($passkeyAssertion, true),
                    $cart->base_grand_total
                );

                \Illuminate\Support\Facades\Log::info("Web3 Order Broadcasted Successfully", ['tx' => $txHash, 'amount' => $cart->base_grand_total]);

                // 1. Decrement local balance optimistically (Sync with on-chain broadcast)
                $customer = auth()->guard('customer')->user();
                $customer->decrement('balance', $cart->base_grand_total);

                // 2. Clear cached totals and store the hash for transaction recording
                session(['last_web3_tx_hash' => $txHash]);

                // Order is fully broadcasted!
                // Final confirmation will be handled in the background.
                
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Web3 Checkout Broadcast Error: " . $e->getMessage());
                return response()->json([
                    'message' => "Ошибка Web3: " . $e->getMessage(),
                ], 400);
            }
        }
        // ----------------------------------------------------

        if ($redirectUrl = Payment::getRedirectUrl($cart)) {
            return new JsonResource([
                'redirect' => true,
                'redirect_url' => $redirectUrl,
            ]);
        }

        $data = (new OrderResource($cart))->jsonSerialize();

        if ($txHash = session('last_web3_tx_hash')) {
            $data['web3_tx_hash'] = $txHash;
            session()->forget('last_web3_tx_hash');
        }

        $order = $this->orderRepository->create($data);

        // ----------------------------------------------------
        // WEB3/CREDITS POST-ORDER PROCESSING
        // ----------------------------------------------------
        if ($cart->payment->method === 'credits') {
            $txHash = session('last_web3_tx_hash');
            $customer = auth()->guard('customer')->user();
            
            // Create "purchase" log in CustomerTransaction
            \Webkul\Customer\Models\CustomerTransaction::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'customer_id' => $customer->id,
                'amount' => -$order->base_grand_total,
                'type' => 'purchase',
                'status' => 'completed',
                'notes' => "Оплата заказа #{$order->increment_id}",
                'web3_tx_hash' => $txHash,
            ]);

            // Dispatch NFT/Cashback service
            \App\Jobs\ProcessOrderCashbackJob::dispatch($order);
            
            Log::info("Web3 Order Finalized", ['order' => $order->id, 'tx' => $txHash]);
        }
        // ----------------------------------------------------

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return new JsonResource([
            'redirect' => true,
            'redirect_url' => route('shop.checkout.onepage.success'),
        ]);
    }

    /**
     * Auto-save a minimal billing address from the authenticated customer's profile.
     * Used for digital orders where no address entry is needed.
     */
    protected function autoSaveBillingFromProfile(): void
    {
        $customer = auth()->guard('customer')->user();

        if (!$customer) {
            return;
        }

        Cart::saveAddresses([
            'billing' => [
                'first_name' => $customer->first_name ?: ($customer->username ?? 'Customer'),
                'last_name' => $customer->last_name ?: '-',
                'email' => $customer->email,
                'address' => ['Digital'],
                'city' => 'Digital',
                'country' => 'RU',
                'state' => '',
                'postcode' => '',
                'phone' => $customer->phone ?? '',
                'use_for_shipping' => true,
            ],
        ]);
    }

    /**
     * Validate order before creation.
     *
     * @return void|\Exception
     */
    public function validateOrder()
    {
        $cart = Cart::getCart();

        $minimumOrderAmount = core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?: 0;

        if (
            auth()->guard('customer')->check()
            && auth()->guard('customer')->user()->is_suspended
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.suspended-account-message'));
        }

        if (
            auth()->guard('customer')->user()
            && !auth()->guard('customer')->user()->status
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.inactive-account-message'));
        }

        if (!Cart::haveMinimumOrderAmount()) {
            throw new \Exception(trans('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]));
        }

        if ($cart->haveStockableItems() && !$cart->shipping_address) {
            throw new \Exception(trans('shop::app.checkout.onepage.address.check-shipping-address'));
        }

        if (!$cart->billing_address) {
            throw new \Exception(trans('shop::app.checkout.onepage.address.check-billing-address'));
        }

        if (
            $cart->haveStockableItems()
            && !$cart->selected_shipping_rate
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.specify-shipping-method'));
        }

        if (!$cart->payment) {
            throw new \Exception(trans('shop::app.checkout.cart.specify-payment-method'));
        }
    }
}
