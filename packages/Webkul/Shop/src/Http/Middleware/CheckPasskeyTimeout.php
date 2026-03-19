<?php

namespace Webkul\Shop\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckPasskeyTimeout
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
        if (session('logged_in_via_passkey')) {
            $lastUnlock = session('passkey_unlocked_at');
            $timeout = config('auth.passkey_timeout', 900); // 15 minutes default

            if ($lastUnlock && (time() - $lastUnlock) > $timeout) {
                session()->forget(['logged_in_via_passkey', 'passkey_unlocked_at']);

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Wallet session expired.',
                        'session_expired' => true,
                    ], 403);
                }

                // For web: redirect back to wallet page with a flag so JS can re-trigger passkey
                session()->flash('wallet_session_expired', true);

                return redirect()->route('shop.customers.account.credits.index');
            }

            // Update timestamp for sliding window (activity based)
            session()->put('passkey_unlocked_at', time());
        }

        return $next($request);
    }
}
