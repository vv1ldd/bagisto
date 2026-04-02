<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Customer\Services\PasskeyWeb3Signer;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyAuthenticationOptionsAction;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Customer\Services\HotWalletService;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Repositories\CustomerTransactionRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        protected CustomerTransactionRepository $customerTransactionRepository,
        protected PasskeyWeb3Signer $passkeyWeb3Signer,
        protected GeneratePasskeyAuthenticationOptionsAction $generatePasskeyOptionsAction
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

        // Generate Targeted Passkey Authentication Options for the finish step
        $optionsJson = $this->generateTargetedPasskeyOptions(auth()->guard('customer')->user());
        session()->put('passkey-authentication-options-json', $optionsJson);

        return view('shop::checkout.onepage.sbp-confirm', [
            'order'        => $order,
            'is_test_mode' => core()->getConfigData('sales.payment_methods.sbp.test_mode'),
            'passkeyOptions' => json_decode($optionsJson, true)
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

            // Refresh targeted passkey options in session
            if (! session()->has('passkey-authentication-options-json')) {
                $optionsJson = $this->generateTargetedPasskeyOptions(auth()->guard('customer')->user());
                session()->put('passkey-authentication-options-json', $optionsJson);
                Log::info("SBP Status: Refreshed targeted passkey options in session", ['order' => $order->id]);
            }

            return response()->json([
                'success'  => true,
                'received' => $additional['sbp_payment_received'] ?? false,
                'is_ready' => $additional['is_ready_for_passkey'] ?? false,
                'tx_base'  => $additional['mint_tx_base'] ?? null,
                // Provide refreshed options if needed by frontend to sync
                'passkeyOptions' => json_decode(session()->get('passkey-authentication-options-json'), true)
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

            // 0. Ensure Payment via SBP was indeed recorded
            if (! ($additional['sbp_payment_received'] ?? false)) {
                Log::warning("SBP Finish Rejected: Payment not received in DB", ['order' => $order->id]);
                return response()->json(['success' => false, 'message' => 'NOT_PAID'], 400);
            }

            // 1. Get the Passkey Assertion (Signature)
            $passkeyAssertion = $request->input('passkey_assertion');
            
            if (! $passkeyAssertion) {
                Log::warning("SBP Finish Rejected: passkey_assertion missing", ['order' => $order->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Биометрическая подпись (Passkey) обязательна.'
                ], 422);
            }

            $customer = $order->customer;
            $amount = (float) $order->grand_total;

            try {
                Log::info("SBP 2.0 Web3 Finalization: Processing signature", ['order' => $order->id, 'amount' => $amount]);
                
                // Mirroring OnepageController logic: robust decoding of the assertion
                $txHash = $this->passkeyWeb3Signer->processGaslessCheckout(
                    $customer,
                    is_array($passkeyAssertion) ? $passkeyAssertion : json_decode($passkeyAssertion, true),
                    $amount
                );

                Log::info("SBP 2.0 Web3 Finalization: Success", ['order' => $order->id, 'tx' => $txHash]);

                // 2. Decrement local balance (Mirroring OnepageController)
                $customer->decrement('balance', $amount);

                // 3. Sync with internal transaction history (Deduction/Purchase)
                $this->customerTransactionRepository->create([
                    'uuid'           => (string) Str::uuid(),
                    'customer_id'    => $customer->id,
                    'amount'         => -1 * $amount,
                    'type'           => 'purchase',
                    'status'         => 'completed',
                    'reference_type' => get_class($order),
                    'reference_id'   => $order->id,
                    'notes'          => "Оплата заказа по СБП (Списание MC #{$order->increment_id})",
                    'metadata'       => ['tx_hash' => $txHash],
                ]);

                // 4. Finalize order metadata
                $additional['finish_tx_hash'] = $txHash;
                $additional['finalized_at'] = now()->toDateTimeString();
                $additional['is_paid'] = true;

                $order->update(['status' => 'processing']);
                $payment->update(['method' => 'credits', 'additional' => $additional]);

                // Clear session for redirects
                session()->put('order_id', $order->id);

                return response()->json(['success' => true, 'message' => 'SUCCESS']);

            } catch (\Exception $e) {
                Log::error("SBP 2.0 Web3 Finalization Error: " . $e->getMessage(), ['order' => $order->id]);
                return response()->json([
                    'success' => false,
                    'message' => "Ошибка Web3: " . $e->getMessage(),
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error("SBP Finish System Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Generate "Targeted" Passkey Authentication Options.
     * Restricts the browser prompt to only show the user's registered keys.
     *
     * @param  \Webkul\Customer\Models\Customer  $customer
     * @return string (JSON)
     */
    private function generateTargetedPasskeyOptions($customer)
    {
        // 1. Get base options from Spatie
        config(['passkeys.relying_party.id' => request()->getHost()]);
        $optionsJson = $this->generatePasskeyOptionsAction->execute();
        
        if (! $customer || $customer->passkeys->isEmpty()) {
            return $optionsJson;
        }

        try {
            // 2. Use Spatie's Serializer to turn it into an object
            $serializer = \Spatie\LaravelPasskeys\Support\Serializer::make();
            $options = $serializer->fromJson($optionsJson, \Webauthn\PublicKeyCredentialRequestOptions::class);

            Log::debug("Passkey: Generating options for RP ID: " . $options->rpId);

            // 3. Populate allowCredentials with objects from the DB
            $allowCredentials = [];
            foreach ($customer->passkeys as $passkey) {
                // Loosen transports by passing empty array - allows any method to find the key
                $allowCredentials[] = new \Webauthn\PublicKeyCredentialDescriptor(
                    \Webauthn\PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
                    $passkey->data->publicKeyCredentialId,
                    [] // Empty transports = broad search
                );
            }

            $options->allowCredentials = $allowCredentials;

            $finalJson = $serializer->toJson($options);
            
            Log::debug("Passkey: Targeted options generated", [
                'count' => count($allowCredentials),
                'ids_lengths' => array_map('strlen', array_column($customer->passkeys->toArray(), 'credential_id'))
            ]);

            return $finalJson;
        } catch (\Exception $e) {
            Log::error("Passkey: Failed to generate targeted options", ['error' => $e->getMessage()]);
            return $optionsJson;
        }
    }
}
