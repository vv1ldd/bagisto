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

    /**
     * Show the unlock screen for Wallet Access.
     */
    public function unlock()
    {
        $customer = auth()->guard('customer')->user();
        $hasPasskey = $customer->passkeys()->count() > 0;
        $hasPin = !empty($customer->wallet_pin);
        $pinLength = $customer->wallet_pin_length ?? 4;

        // If they have neither, they somehow bypassed the setup Middleware. Send to setup.
        if (!$hasPasskey && !$hasPin) {
            return redirect()->route('shop.customers.account.wallet.setup');
        }

        // Output view. Frontend will decide whether to auto-trigger passkey or show numpad based on what user has.
        return view('shop::customers.account.wallet-access.unlock', compact('hasPasskey', 'hasPin', 'pinLength'));
    }

    /**
     * Verify the entered PIN.
     */
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'string'],
        ]);

        $customer = auth()->guard('customer')->user();

        if (empty($customer->wallet_pin) || !Hash::check($request->input('pin'), $customer->wallet_pin)) {
            return back()->with('error', 'Неверный PIN-код. Попробуйте еще раз.');
        }

        // Success: unlock the wallet
        session(['wallet_unlocked_at' => time()]);

        $intended = session()->pull('url.intended', route('shop.customers.account.credits.index'));

        // If it was an AJAX request, return JSON redirect
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'redirect_url' => $intended]);
        }

        return redirect()->to($intended);
    }
}
