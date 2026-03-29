<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Webkul\Shop\Http\Controllers\Controller;

class RedeemController extends Controller
{
    /**
     * Display the redeem page.
     */
    public function index()
    {
        if (! auth()->guard('customer')->check()) {
            session(['url.intended' => request()->fullUrl()]);

            if (! session()->has('registration_intended_url')) {
                session(['registration_intended_url' => request()->fullUrl()]);
            }
        }

        $customer = auth()->guard('customer')->user();
        $hasPasskeys = $customer ? $customer->passkeys()->exists() : false;

        return view('shop::customers.account.redeem.index', compact('hasPasskeys'));
    }

    /**
     * Verify the voucher code via the external API.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|regex:/^W1C-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/',
        ]);

        $servicesUrl = config('services.redeem.url');
        $token       = config('services.redeem.token');

        if (!$servicesUrl || !$token) {
            return response()->json(['message' => 'API configuration missing'], 500);
        }

        try {
            $response = Http::withToken($token)
                ->timeout(60)
                ->post($servicesUrl . '/verify-code', [
                    'code' => $request->code,
                ]);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Не удалось связаться с сервером активации',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Request verification code email via the external API.
     */
    public function sendVerification(Request $request)
    {
        $request->validate([
            'code'  => 'required|string',
            'email' => 'required|email',
        ]);

        $servicesUrl = config('services.redeem.url');
        $token       = config('services.redeem.token');

        try {
            $response = Http::withToken($token)
                ->timeout(60)
                ->post($servicesUrl . '/send-verification', [
                    'code'  => $request->code,
                    'email' => $request->email,
                ]);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка при отправке кода подтверждения'], 500);
        }
    }

    /**
     * Finalize the activation via the external API.
     */
    public function activate(Request $request)
    {
        $request->validate([
            'code'              => 'required|string',
            'verification_code' => 'required|string',
            'email'             => 'required|email',
            'first_name'        => 'nullable|string',
            'last_name'         => 'nullable|string',
            'phone'             => 'nullable|string',
            'option'            => 'nullable|array',
        ]);

        $servicesUrl = config('services.redeem.url');
        $token       = config('services.redeem.token');

        if (!$servicesUrl || !$token) {
            return response()->json(['message' => 'API configuration missing'], 500);
        }

        try {
            // Pass the entire validated data + nested options
            $payload = $request->all();

            $response = Http::withToken($token)
                ->timeout(60)
                ->post($servicesUrl . '/activate', $payload);

                // Success - voucher activated on the side of the activation server
                // Profile update removed as per latest requirements for frictionless activation

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка при финальной активации'], 500);
        }
    }
}
