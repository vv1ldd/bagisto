<?php

namespace Webkul\Shop\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cookie;
use Webkul\Customer\Repositories\CustomerTrustedDeviceRepository;
use Webkul\Shop\Mail\Customer\VerifyIpNotification;

class CheckCustomerIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip for routes that shouldn't trigger the check
        if ($this->shouldSkip($request)) {
            return $next($request);
        }


        if (Auth::guard('customer')->check()) {
            if (session('logged_in_via_passkey')) {
                return $next($request);
            }

            $customer = Auth::guard('customer')->user();

            $currentIp = $request->header('CF-Connecting-IP')
                ?? $request->header('X-Forwarded-For')
                ?? $request->header('X-Real-IP')
                ?? $request->ip();

            if (str_contains($currentIp, ',')) {
                $currentIp = trim(explode(',', $currentIp)[0]);
            }

            // 1. Check legacy trust (same as before)
            $isTrusted = ($customer->registration_ip && $currentIp === $customer->registration_ip)
                || ($customer->last_login_ip && $currentIp === $customer->last_login_ip);

            // 2. Check Advanced Trust System
            if (!$isTrusted) {
                $trustedDeviceRepository = app(CustomerTrustedDeviceRepository::class);

                // Is this IP already trusted?
                $ipTrusted = $trustedDeviceRepository->findOneWhere([
                    'customer_id' => $customer->id,
                    'ip_address' => $currentIp
                ]);

                // Or does this device have the trusted cookie token?
                $cookieToken = request()->cookie('device_trust_token');
                $cookieTrusted = $cookieToken ? $trustedDeviceRepository->findOneWhere([
                    'customer_id' => $customer->id,
                    'cookie_token' => $cookieToken
                ]) : null;

                if ($ipTrusted || $cookieTrusted) {
                    $isTrusted = true;

                    // If they are trusted via cookie but have a new IP, let's add this new IP to their trusted devices
                    if ($cookieTrusted && !$ipTrusted) {
                        $trustedDeviceRepository->create([
                            'customer_id' => $customer->id,
                            'ip_address' => $currentIp,
                            'user_agent' => request()->userAgent(),
                            'cookie_token' => \Illuminate\Support\Str::random(64), // Generate fresh token for the new IP record
                            'last_used_at' => now(),
                        ]);
                    }

                    // Touch the last_used_at so we know this device is active
                    if ($cookieTrusted) {
                        $trustedDeviceRepository->update(['last_used_at' => now()], $cookieTrusted->id);
                    } elseif ($ipTrusted) {
                        $trustedDeviceRepository->update(['last_used_at' => now()], $ipTrusted->id);
                    }
                }
            }

            if (($customer->registration_ip || $customer->last_login_ip) && !$isTrusted) {
                Log::info("IP Mismatch detected in middleware: Registration({$customer->registration_ip}) / Last({$customer->last_login_ip}) vs Current({$currentIp}) for {$customer->email}");

                $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $token = bin2hex(random_bytes(16));

                session([
                    'verification_code_pending' => $verificationCode,
                    'verification_email' => $customer->email,
                    'pending_ip' => $currentIp,
                    'verify_ip_token' => $token,
                ]);

                Cache::put("ip_verification_code_{$customer->email}", $verificationCode, now()->addHours(24));
                Cache::put("ip_verification_token_{$token}", $customer->email, now()->addHours(24));

                Mail::queue(new VerifyIpNotification($customer, $currentIp, $verificationCode, $token));

                Auth::guard('customer')->logout();

                return redirect()->route('shop.customers.verify_ip.code');
            }
        }

        return $next($request);
    }

    /**
     * Determine if the request should skip IP verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldSkip(Request $request)
    {
        $skippedRoutes = [
            'shop.customers.verify_ip.*',
            'shop.customer.session.*',
            'shop.customers.resend.verification_email',
            'passkeys.*',
            'shop.customers.account.passkeys.index',
        ];

        foreach ($skippedRoutes as $route) {
            if ($request->routeIs($route)) {
                return true;
            }
        }

        return false;
    }
}
