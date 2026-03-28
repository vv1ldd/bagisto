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
        return view('shop::customers.account.redeem.index');
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
            'verification_code' => 'required|numeric',
            'first_name'        => 'required|string|min:2',
            'last_name'         => 'required|string|min:2',
            'email'             => 'required|email',
            'phone'             => 'required|string',
            'option'            => 'nullable|array',
        ]);

        $servicesUrl = config('services.redeem.url');
        $token       = config('services.redeem.token');

        try {
            // Pass the entire validated data + nested options
            $payload = $request->all();

            $response = Http::withToken($token)
                ->post($servicesUrl . '/activate', $payload);

            if ($response->ok() && $response->json()['status'] === 'success') {
                $customer = auth()->guard('customer')->user();
                
                // Update customer profile with the data used for redemption
                $customer->update([
                    'first_name' => $request->first_name,
                    'last_name'  => $request->last_name,
                    'phone'      => $request->phone,
                ]);
            }

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка при финальной активации'], 500);
        }
    }
}
