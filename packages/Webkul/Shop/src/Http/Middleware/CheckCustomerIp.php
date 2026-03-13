<?php

namespace Webkul\Shop\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * IP verification has been disabled.
 * Authentication uses magic links and passkeys — no IP or email OTP check needed.
 */
class CheckCustomerIp
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
