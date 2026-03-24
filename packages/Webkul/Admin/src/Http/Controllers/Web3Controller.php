<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;
use Webkul\Customer\Services\HotWalletService;

class Web3Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::web3.index');
    }

    /**
     * Send a custom ETH transaction securely using Passkey authentication.
     */
    public function sendTransaction(
        Request $request,
        HotWalletService $hotWalletService,
        FindPasskeyToAuthenticateAction $findPasskeyAction
    ) {
        $request->validate([
            'address' => 'required|string|regex:/^0x[a-fA-F0-9]{40}$/',
            'amount'  => 'required|numeric|min:0.000001',
            'start_authentication_response' => 'required',
        ]);

        // 1. Always sync RP ID to current request host for WebAuthn validation
        $currentHost = $request->getHost();
        config(['passkeys.relying_party.id' => $currentHost]);

        $optionsJson = session()->get('admin-passkey-authentication-options-json');

        if (!$optionsJson) {
            return response()->json(['message' => 'No authentication options found in session. Please refresh the page.'], 400);
        }

        try {
            $credentialResponse = $request->input('start_authentication_response');
            
            if (is_array($credentialResponse)) {
                $credentialResponse = json_encode($credentialResponse);
            }

            // 2. Verify Passkey (Throws exception or returns null if invalid)
            $passkey = $findPasskeyAction->execute(
                $credentialResponse,
                $optionsJson,
            );

            // 3. Ensure the Passkey belongs to the currently authenticated Admin
            $admin = auth()->guard('admin')->user();
            if (!$passkey || !($passkey->authenticatable instanceof \Webkul\User\Models\Admin) || $passkey->authenticatable->id !== $admin->id) {
                return response()->json(['message' => 'Passkey verification failed or does not belong to the current admin.'], 401);
            }

            // Passkey is valid! We can securely clear the session challenge.
            session()->forget('admin-passkey-authentication-options-json');

            // 4. Send the ETH transaction
            $address = $request->input('address');
            $amount  = (float) $request->input('amount');

            $txHash = $hotWalletService->sendEth($address, $amount);

            if ($txHash) {
                Log::info("Admin [{$admin->id}] securely sent {$amount} ETH to {$address} using Passkey auth. Tx: {$txHash}");
                
                return response()->json([
                    'message' => "Successfully sent {$amount} ETH to {$address}.",
                    'tx_hash' => $txHash,
                    'explorer_url' => "https://arbiscan.io/tx/{$txHash}"
                ]);
            }

            return response()->json(['message' => 'Failed to serialize or broadcast the transaction. Check logs for details.'], 500);

        } catch (\Exception $e) {
            Log::error('Admin Web3 Secure Transaction Error: ' . $e->getMessage());
            return response()->json(['message' => 'Authentication or transaction failed: ' . $e->getMessage()], 400);
        }
    }
}
