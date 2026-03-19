<?php

namespace Webkul\Shop\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckWalletAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $customer = auth()->guard('customer')->user();

        if (!$customer) {
            return redirect()->route('shop.customer.session.index');
        }

        $hasPasskey = $customer->passkeys()->count() > 0;

        // 1. If user has no passkey, show Setup inline
        if (!$hasPasskey) {
            if ($request->routeIs('shop.customers.account.wallet.setup*')) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Wallet setup required',
                    'locked'  => true,
                    'redirect_url' => route('shop.customers.account.wallet.setup'),
                ], 403);
            }

            return response()->view('shop::customers.account.wallet-access.setup');
        }

        // 2. If user is locked out, redirect to Unlock screen
        // We consider the wallet 'unlocked' if 'wallet_unlocked_at' is in the session
        // and it hasn't expired (timeout: 15 minutes = 900 seconds)
        $unlockedAt = session('wallet_unlocked_at');
        $recentPasskeyLogin = session('logged_in_via_passkey') && session('passkey_unlocked_at');

        if ($recentPasskeyLogin) {
            // Mirror the unlock time to the wallet
            session(['wallet_unlocked_at' => session('passkey_unlocked_at')]);
            $unlockedAt = session('passkey_unlocked_at');
        }

        $timeout = 900; // 15 minutes

        if (!$unlockedAt || (time() - $unlockedAt > $timeout)) {
            // Expired or not unlocked
            session()->forget('wallet_unlocked_at');

            if ($request->routeIs('shop.customers.account.wallet.unlock*')) {
                return $next($request);
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Wallet locked',
                    'locked'  => true,
                    'redirect_url' => route('shop.customers.account.wallet.unlock'),
                ], 403);
            }

            // Return the unlock view DIRECTLY. 
            return response()->view('shop::customers.account.wallet-access.unlock', [
                'hasPasskey' => $hasPasskey,
            ]);
        }

        // 3. Update the unlocked timestamp to extend the session
        session(['wallet_unlocked_at' => time()]);

        return $next($request);
    }
}
