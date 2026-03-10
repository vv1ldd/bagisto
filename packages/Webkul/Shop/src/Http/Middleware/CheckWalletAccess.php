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
        $hasPin = !empty($customer->wallet_pin);

        // 1. If user has no passkey and no PIN, redirect to Setup
        if (!$hasPasskey && !$hasPin) {
            // Check if we're already on setup routes to avoid infinite loop
            if ($request->routeIs('shop.customers.account.wallet.setup*')) {
                return $next($request);
            }
            return redirect()->route('shop.customers.account.wallet.setup');
        }

        // 2. If user is locked out, redirect to Unlock screen
        // We consider the wallet 'unlocked' if 'wallet_unlocked_at' is in the session
        // and it hasn't expired (timeout: 15 minutes = 900 seconds)
        // Also if they just logged in via passkey, the other middleware `CheckPasskeyTimeout` might handle it,
        // but let's strictly rely on a local `wallet_unlocked_at` session for wallet.

        $unlockedAt = session('wallet_unlocked_at');
        // If the user JUST logged in via passkey, we can consider the wallet unlocked implicitly.
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
            // Add intended url to session so we can return after unlock
            if (!$request->expectsJson()) {
                session()->put('url.intended', $request->url());
            }

            return redirect()->route('shop.customers.account.wallet.unlock');
        }

        // 3. Update the unlocked timestamp to extend the session
        session(['wallet_unlocked_at' => time()]);

        return $next($request);
    }
}
