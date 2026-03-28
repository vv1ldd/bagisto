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
            'code' => 'required|string',
        ]);

        $apiUrl = config('services.redeem.url') . '/verify-code';
        $token  = config('services.redeem.token');

        try {
            $response = Http::withToken($token)
                ->post($apiUrl, [
                    'code' => $request->code,
                ]);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'API Connection Error',
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

        $apiUrl = config('services.redeem.url') . '/send-verification';
        $token  = config('services.redeem.token');

        try {
            $response = Http::withToken($token)
                ->post($apiUrl, [
                    'code'  => $request->code,
                    'email' => $request->email,
                ]);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'API Connection Error',
            ], 500);
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
            'first_name'        => 'required|string',
            'last_name'         => 'required|string',
            'email'             => 'required|email',
            'phone'             => 'required|string',
        ]);

        $apiUrl = config('services.redeem.url') . '/activate';
        $token  = config('services.redeem.token');

        try {
            $response = Http::withToken($token)
                ->post($apiUrl, $request->only([
                    'code',
                    'verification_code',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                ]));

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'API Connection Error',
            ], 500);
        }
    }
}
