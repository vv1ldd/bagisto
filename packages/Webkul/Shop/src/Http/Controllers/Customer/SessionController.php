<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Support\Facades\Event;
use Webkul\Shop\Http\Controllers\Controller;

class SessionController extends Controller
{
    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        if (auth()->guard('customer')->check()) {
            return redirect()->route('shop.home.index');
        }

        return view('shop::customers.sign-in');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $customer = auth()->guard('customer')->user();

        if ($customer) {
            $id = $customer->id;

            // Mark logout in our log
            try {
                if (class_exists(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)) {
                    app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->updateLogoutTime($customer);
                }
            } catch (\Exception $e) {
                // Ignore log errors on logout
            }

            auth()->guard('customer')->logout();

            Event::dispatch('customer.after.logout', $id);
        }

        return redirect()->route('shop.home.index');
    }
}
