<?php

namespace Webkul\Admin\Http\Middleware;

use Closure;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = 'admin')
    {
        if (auth()->guard($guard)->check()) {
            return redirect()->route('admin.dashboard.index');
        }

        return $next($request);
    }
}
