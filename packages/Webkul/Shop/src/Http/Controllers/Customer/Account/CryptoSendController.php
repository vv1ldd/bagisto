<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Webkul\Shop\Http\Controllers\Controller;
use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Models\CustomerTransaction;
use Webkul\Customer\Services\InternalTransferService;
use Illuminate\Support\Str;

class CryptoSendController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  InternalTransferService  $internalTransferService
     * @return void
     */
    public function __construct(
        protected InternalTransferService $internalTransferService
    ) {
    }

    /**
     * Handle the Send request with Passkey authorization.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  FindPasskeyToAuthenticateAction  $findPasskeyAction
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, FindPasskeyToAuthenticateAction $findPasskeyAction)
    {
        $request->validate([
            'wallet_id' => 'required',
            'recipient' => 'required',
            'amount' => 'required|numeric|min:0',
            'assertion' => 'required|string',
        ]);

        $currentUser = Auth::guard('customer')->user();
        if (! $currentUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Ensure we have a Customer model for the transfer service
        if (! $currentUser instanceof Customer) {
            $currentUser = Customer::find($currentUser->getAuthIdentifier());
        }

        // 1. Verify Passkey Assertion
        $optionsJson = session()->get('passkey-authentication-options-json');
        if (! $optionsJson) {
            return response()->json(['message' => 'Passkey validation failed: Missing options in session.'], 400);
        }

        try {
            $credentialResponse = $request->input('assertion');
            
            $passkey = $findPasskeyAction->execute(
                $credentialResponse,
                $optionsJson,
            );

            if (! $passkey || $passkey->authenticatable_id !== $currentUser->id) {
                return response()->json(['message' => 'Passkey validation failed or unauthorized device.'], 401);
            }

            // 2. Resolve Recipient
            $recipientAddr = $request->recipient;
            $recipient = Customer::where('credits_id', $recipientAddr)->first();

            if (! $recipient) {
                // If recipient not found by address, but it's an @alias, it should have been resolved front-side.
                // We only support internal transfers to registered Ethereum addresses in this version.
                return response()->json(['message' => 'Recipient address not found in system. External withdrawals are currently disabled.'], 400);
            }

            // 3. Process Transfer
            $amount = (float) $request->amount;
            $notes = "Authorized via Passkey: (" . substr($passkey->credential_id, 0, 8) . "...)";

            $this->internalTransferService->transfer($currentUser, $recipient, $amount, $notes);
            
            // Clear Passkey options from session once used successfully
            session()->forget('passkey-authentication-options-json');

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Crypto Send Error: ' . $e->getMessage());
            return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 400);
        }
    }
}
