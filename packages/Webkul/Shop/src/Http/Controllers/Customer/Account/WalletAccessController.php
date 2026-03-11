<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Webkul\Shop\Http\Controllers\Controller;

class WalletAccessController extends Controller
{
    /**
     * Show the setup screen for Wallet Access (Passkey / PIN).
     */
    public function setup(Request $request)
    {
        $customer = auth()->guard('customer')->user();

        // If user already has a passkey or PIN, redirect back to wallet, 
        // unless 'force' is passed (from settings)
        if (!$request->has('force')) {
            if ($customer->passkeys()->count() > 0 || !empty($customer->wallet_pin)) {
                return redirect()->route('shop.customers.account.credits.index');
            }
        }

        return view('shop::customers.account.wallet-access.setup');
    }

    /**
     * Store the chosen PIN code.
     */
    public function storePin(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'string', 'regex:/^(\d{4}|\d{6})$/'],
        ]);

        $customer = auth()->guard('customer')->user();
        $pin = $request->input('pin');

        // Hash the PIN (same as a password, though for a 4/6 digit pin it's fast)
        $customer->wallet_pin = Hash::make($pin);
        $customer->wallet_pin_length = strlen($pin);
        $customer->save();

        // Automatically unlock the wallet for this session
        session(['wallet_unlocked_at' => time()]);

        session()->flash('success', 'PIN-код для кошелька успешно установлен.');

        $intended = session()->pull('url.intended', route('shop.customers.account.credits.index'));
        return redirect()->to($intended);
    }
}
