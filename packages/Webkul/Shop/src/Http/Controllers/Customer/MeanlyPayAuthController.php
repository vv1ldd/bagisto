<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Webkul\Shop\Http\Controllers\Controller;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyAuthenticationOptionsAction;
use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;
use Spatie\LaravelPasskeys\Http\Requests\AuthenticateUsingPasskeysRequest;

class MeanlyPayAuthController extends Controller
{
    /**
     * Show the unlock screen for Meanly Pay.
     */
    public function unlock()
    {
        $customer = Auth::guard('customer')->user();

        // If no PIN and no Passkey, user shouldn't be here (Middleware handles this, but just in case)
        if (!$customer->pin_code && !$customer->hasPasskeys()) {
            return redirect()->route('shop.customers.account.passkeys.index');
        }

        // If already unlocked, go straight to credits
        if (session('meanly_pay_unlocked') === true) {
            return redirect()->route('shop.customers.account.credits.index');
        }

        return view('shop::customers.account.meanly_pay.unlock', compact('customer'));
    }

    /**
     * Verify the PIN code and unlock Meanly Pay session.
     */
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin_code' => ['required', 'string'],
        ]);

        $customer = Auth::guard('customer')->user();

        if (!$customer->pin_code) {
            return back()->with('error', 'У вас не установлен ПИН-код.');
        }

        if (Hash::check($request->pin_code, $customer->pin_code)) {
            session()->put('meanly_pay_unlocked', true);
            return redirect()->route('shop.customers.account.credits.index');
        }

        return back()->with('error', 'Неверный ПИН-код. Попробуйте еще раз.');
    }

    /**
     * Generate authentication options for passkey verification.
     */
    public function passkeyOptions(GeneratePasskeyAuthenticationOptionsAction $generateOptionsAction)
    {
        $optionsJson = $generateOptionsAction->execute();
        session()->put('meanly-pay-passkey-auth-options', $optionsJson);

        return response($optionsJson)->header('Content-Type', 'application/json');
    }

    /**
     * Verify the passkey and unlock Meanly Pay session.
     */
    public function verifyPasskey(AuthenticateUsingPasskeysRequest $request, FindPasskeyToAuthenticateAction $findPasskeyAction)
    {
        $optionsJson = session()->get('meanly-pay-passkey-auth-options');

        if (!$optionsJson) {
            return response()->json(['message' => 'Сессия устарела. Обновите страницу.'], 400);
        }

        try {
            $passkey = $findPasskeyAction->execute(
                $request->input('start_authentication_response'),
                $optionsJson,
            );

            if (!$passkey) {
                return response()->json(['message' => 'Не удалось распознать Passkey.'], 400);
            }

            $customer = Auth::guard('customer')->user();

            // Ensure the passkey belongs to the currently logged in customer
            if ($passkey->authenticatable_id !== $customer->id) {
                return response()->json(['message' => 'Passkey не принадлежит вашему аккаунту.'], 403);
            }

            session()->put('meanly_pay_unlocked', true);
            session()->forget('meanly-pay-passkey-auth-options');

            return response()->json([
                'message' => 'Успешно разблокировано',
            ]);
        } catch (\Exception $e) {
            Log::error('Meanly Pay Passkey unlock error: ' . $e->getMessage());
            return response()->json(['message' => 'Ошибка аутентификации: ' . $e->getMessage()], 400);
        }
    }
}
