<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Webkul\Shop\Http\Controllers\Controller;

class WalletAccessController extends Controller
{
    /**
     * Show the setup screen for Wallet Access (Passkey).
     */
    public function setup(Request $request)
    {
        $customer = auth()->guard('customer')->user();

        // If user already has a passkey, redirect back to wallet, 
        // unless 'force' is passed (from settings)
        if (!$request->has('force')) {
            if ($customer->passkeys()->count() > 0) {
                return redirect()->route('shop.customers.account.credits.index');
            }
        }

        return view('shop::customers.account.wallet-access.setup');
    }
}
