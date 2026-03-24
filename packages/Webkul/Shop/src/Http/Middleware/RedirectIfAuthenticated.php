<?php

namespace Webkul\Shop\Http\Middleware;

use Closure;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = 'customer')
    {
        if (auth()->guard($guard)->check()) {
            return redirect()->route('shop.customers.account.index');
        }

        return $next($request);
    }
}
