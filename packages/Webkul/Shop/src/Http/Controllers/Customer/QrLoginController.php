<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Webkul\Shop\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class QrLoginController extends Controller
{
    /**
     * Prepare QR Login token and URL.
     */
    public function prepare()
    {
        $token = Str::random(32);
        
        \Log::info('QR Login Prepare', ['token' => $token]);

        // Mark as pending in cache for 120 seconds
        Cache::put('qr_login:' . $token, ['status' => 'pending', 'ua' => request()->header('User-Agent')], 120);

        $url = URL::signedRoute('shop.customer.login.qr.landing', ['token' => $token]);

        return response()->json([
            'token' => $token,
            'url'   => $url
        ]);
    }

    /**
     * Check status of the QR login session (Polling).
     */
    public function checkStatus(Request $request)
    {
        $token = $request->input('token');
        if (!$token) return response()->json(['error' => 'No token'], 400);

        $data = Cache::get('qr_login:' . $token);
        
        \Log::debug('QR Login Poll', ['token' => $token, 'status' => $data['status'] ?? 'null']);

        if (!$data) {
            return response()->json(['status' => 'expired']);
        }

        if ($data['status'] === 'authorized' && isset($data['customer_id'])) {
            $customer = app(\Webkul\Customer\Repositories\CustomerRepository::class)->find($data['customer_id']);
            
            if ($customer) {
                // Perform Login
                Auth::guard('customer')->login($customer, true);

                // Log login activity
                try {
                    app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($customer);
                } catch (\Exception $e) {}

                session()->regenerate();
                
                // Clear cache
                Cache::forget('qr_login:' . $token);

                return response()->json([
                    'status'       => 'success',
                    'redirect_url' => route('shop.customers.account.index')
                ]);
            }
        }

        return response()->json(['status' => $data['status']]);
    }

    /**
     * Mobile landing page for QR Scan.
     */
    public function landing($token)
    {
        $customer = Auth::guard('customer')->user();
        
        \Log::info('QR Login Landing Hit', [
            'token'   => $token,
            'is_auth' => (bool)$customer,
            'url'     => request()->fullUrl()
        ]);

        if (!Cache::has('qr_login:' . $token)) {
             \Log::warning('QR Login Landing: Token not in cache', ['token' => $token]);
             return view('shop::customers.qr-login-landing', ['token' => $token, 'error' => 'Срок действия QR-кода истек. Пожалуйста, обновите страницу на компьютере.']);
        }

        // If not logged in, redirect to login page and save this URL as intended
        if (!$customer) {
            \Log::info('QR Login Landing: Redirecting to login (unauthenticated)');
            session()->put('url.intended', URL::full());

            return redirect()->route('shop.customer.session.index');
        }

        \Log::info('QR Login Landing: Showing authorization view');
        return view('shop::customers.qr-login-landing', compact('token', 'customer'));
    }

    /**
     * Authorize the login from the smartphone.
     */
    public function authorizeLogin($token)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            \Log::warning('QR Login Auth Failed: Unauthenticated phone', ['token' => $token]);
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = Cache::get('qr_login:' . $token);
        if (!$data) {
            \Log::warning('QR Login Auth Failed: Expired/Missing token in cache', ['token' => $token]);
            return response()->json(['error' => 'Expired'], 400);
        }

        $data['status'] = 'authorized';
        $data['customer_id'] = $customer->id;

        Cache::put('qr_login:' . $token, $data, 60);
        
        \Log::info('QR Login Authorized Successfully', ['token' => $token, 'customer_id' => $customer->id]);

        return response()->json(['status' => 'success']);
    }
}
