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
        $response = $next($request);

        if (auth()->guard('customer')->check()) {
            if (!app(CustomerLoginLogRepository::class)->trackActivity(auth()->guard('customer')->user())) {
                auth()->guard('customer')->logout();

                session()->invalidate();
                session()->regenerateToken();

                return redirect()->route('shop.customer.session.index');
            }
        }

        return $response;
    }
}
