<?php

namespace Webkul\Shop\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Customer\Repositories\CustomerLoginLogRepository;

class TrackCustomerSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->guard('customer')->check()) {
            $customer = auth()->guard('customer')->user();

            if (!app(CustomerLoginLogRepository::class)->trackActivity($customer)) {
                \Log::info('[TrackCustomerSession] Revoking session for customer: ' . $customer->email, [
                    'session_id' => session()->getId(),
                    'log_id' => session('customer_login_log_id')
                ]);

                auth()->guard('customer')->logout();
                session()->invalidate();
                session()->regenerateToken();

                return redirect()->route('shop.customer.session.index');
            }
        }

        return $next($request);
    }
}
