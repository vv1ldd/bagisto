<?php

namespace Webkul\Shop\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequireMeanlyPayAuth
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
        $customer = Auth::guard('customer')->user();

        // If the route belongs to setup or unlock, we allow it.
        $allowedRoutes = [
            'shop.customers.account.meanly_pay.unlock',
            'shop.customers.account.meanly_pay.verify_pin',
            'shop.customers.account.passkeys.index',
        ];

        if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        // If not unlocked, redirect to unlock screen if they have PIN or Passkey
        // Otherwise, redirect to passkeys index to set one up
        if (!$request->session()->get('meanly_pay_unlocked', false)) {
            if ($customer->pin_code || $customer->hasPasskeys()) {
                return redirect()->route('shop.customers.account.meanly_pay.unlock');
            } else {
                session()->flash('warning', 'Для доступа к Meanly Pay необходимо настроить PIN-код или Passkey.');
                return redirect()->route('shop.customers.account.passkeys.index');
            }
        }

        return $next($request);
    }
}
