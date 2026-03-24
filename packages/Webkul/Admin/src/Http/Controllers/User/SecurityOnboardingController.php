<?php

namespace Webkul\Admin\Http\Controllers\User;

use Webkul\Admin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webkul\Customer\Services\MnemonicService;
use Webkul\Customer\Services\BlockchainAddressService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class SecurityOnboardingController extends Controller
{
    /**
     * Show the onboarding page.
     */
    public function index()
    {
        $user = auth()->guard('admin')->user();

        if ($user->passkeys->count() && $user->mnemonic_verified_at) {
            return redirect()->route('admin.dashboard.index');
        }

        return view('admin::users.security.onboarding');
    }

    /**
     * Generate mnemonic for admin.
     */
    public function generateMnemonic()
    {
        $user = auth()->guard('admin')->user();

        if ($user->mnemonic_hash) {
            return response()->json(['error' => 'Mnemonic already exists'], 400);
        }

        $mnemonicService = app(MnemonicService::class);
        $blockchainService = app(BlockchainAddressService::class);

        // Generate mnemonic array and convert to space-separated string
        $mnemonicWords = $mnemonicService->generateMnemonic();
        $mnemonic = implode(' ', $mnemonicWords);
        
        // Derive keys and address
        $wallet = $blockchainService->deriveEthereumWallet($mnemonic);
        $pubData = $blockchainService->derivePublicKeyData($mnemonic);

        if (!$wallet || !$pubData) {
            return response()->json(['error' => 'Key derivation failed'], 500);
        }

        $user->update([
            'mnemonic_hash'         => Hash::make($mnemonic),
            'credits_id'            => $wallet['address'],
            'encrypted_private_key' => Crypt::encrypt($wallet['private_key']),
            'public_key'            => $pubData['public_key'],
            'public_key_hash'       => $pubData['public_key_hash'],
        ]);

        return response()->json([
            'mnemonic' => $mnemonic,
        ]);
    }

    /**
     * Verify mnemonic.
     */
    public function verifyMnemonic(Request $request)
    {
        $request->validate([
            'mnemonic' => 'required|string',
        ]);

        $user = auth()->guard('admin')->user();

        if (Hash::check($request->mnemonic, $user->mnemonic_hash)) {
            $user->update(['mnemonic_verified_at' => now()]);

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Invalid mnemonic'], 400);
    }
}
