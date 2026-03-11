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

            // If it's an AJAX request (e.g. from navigation click or SPA internal load), 
            // the navigation helper `handleMeanlyWalletPasskey` handles it usually.
            // But if they navigate directly via browser or a link that didn't intercept, 
            // we show the unlock UI inline.
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Wallet locked',
                    'locked'  => true,
                    'redirect_url' => route('shop.customers.account.wallet.unlock'),
                ], 403);
            }

            // Return the unlock view DIRECTLY. 
            // This renders the lock UI on the same URL (/customer/account/credits)
            $hasPasskey = $customer->passkeys()->count() > 0;
            $hasPin = !empty($customer->wallet_pin);
            $pinLength = $customer->wallet_pin_length ?? 4;

            return response()->view('shop::customers.account.wallet-access.unlock', compact('hasPasskey', 'hasPin', 'pinLength'));
        }

        // 3. Update the unlocked timestamp to extend the session
        session(['wallet_unlocked_at' => time()]);

        return $next($request);
    }
}
