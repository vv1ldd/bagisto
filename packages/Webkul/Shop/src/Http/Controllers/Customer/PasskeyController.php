<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Webkul\Shop\Http\Controllers\Controller;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyAuthenticationOptionsAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;
use Spatie\LaravelPasskeys\Http\Requests\AuthenticateUsingPasskeysRequest;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Webkul\Customer\Repositories\CustomerTrustedDeviceRepository;

class PasskeyController extends Controller
{
    /**
     * Show passkeys page.
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        return view('shop::customers.account.passkeys.index', compact('customer'));
    }

    /**
     * Generate registration options for a new passkey.
     */
    public function registerOptions(Request $request, GeneratePasskeyRegisterOptionsAction $generateOptionsAction)
    {
        $user = Auth::guard('customer')->user();

        $optionsJson = $generateOptionsAction->execute($user, asJson: true);

        $request->session()->put('passkey-registration-options-json', $optionsJson);

        return response($optionsJson)->header('Content-Type', 'application/json');
    }

    /**
     * Store the newly registered passkey.
     */
    public function register(Request $request, StorePasskeyAction $storePasskeyAction)
    {
        $user = Auth::guard('customer')->user();
        $optionsJson = $request->session()->get('passkey-registration-options-json');

        if (!$optionsJson) {
            return response()->json(['message' => 'No registration options found in session.'], 400);
        }

        try {
            $userAgent = $request->userAgent();
            $deviceName = $this->getDeviceName($userAgent);

            $storePasskeyAction->execute(
                authenticatable: $user,
                passkeyJson: json_encode($request->all()),
                passkeyOptionsJson: $optionsJson,
                hostName: $request->getHost(),
            );

            // Fetch the latest passkey and update its details (since StorePasskeyAction might not support custom name/UA)
            $passkey = $user->passkeys()->latest()->first();
            if ($passkey) {
                $passkey->update([
                    'name' => $deviceName,
                    'user_agent' => $userAgent,
                ]);
            }

            $request->session()->forget('passkey-registration-options-json');

            // ---- TRUSTED DEVICE LOGIC ----
            $cookieToken = Str::random(64);
            $trustedDeviceRepository = app(CustomerTrustedDeviceRepository::class);

            $trustedDeviceRepository->create([
                'customer_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $userAgent,
                'cookie_token' => $cookieToken,
                'last_used_at' => now(),
            ]);

            // Set cookie for 1 year
            Cookie::queue('device_trust_token', $cookieToken, 60 * 24 * 365);
            // ------------------------------

            return response()->json(['message' => 'Passkey registered successfully.']);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if ($e->getPrevious()) {
                $message .= ' (Prev: ' . $e->getPrevious()->getMessage() . ')';
            }
            return response()->json(['message' => 'Registration failed: ' . $message], 400);
        }
    }

    /**
     * Get human-readable device name from User-Agent.
     */
    protected function getDeviceName($userAgent)
    {
        if (empty($userAgent)) {
            return 'Passkey Device';
        }

        if (stripos($userAgent, 'iPhone') !== false) {
            return 'iPhone';
        } elseif (stripos($userAgent, 'iPad') !== false) {
            return 'iPad';
        } elseif (stripos($userAgent, 'Android') !== false) {
            return 'Android Device';
        } elseif (stripos($userAgent, 'Macintosh') !== false) {
            return 'Mac';
        } elseif (stripos($userAgent, 'Windows') !== false) {
            return 'Windows PC';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            return 'Linux PC';
        }

        return 'Passkey Device';
    }

    /**
     * Delete an existing passkey.
     */
    public function destroy($id)
    {
        $user = Auth::guard('customer')->user();

        $passkey = $user->passkeys()->findOrFail($id);
        $passkey->delete();

        session()->flash('success', 'Passkey deleted successfully.');

        return redirect()->back();
    }

    /**
     * Generate authentication options for passkey login.
     */
    public function loginOptions(GeneratePasskeyAuthenticationOptionsAction $generateOptionsAction)
    {
        $optionsJson = $generateOptionsAction->execute();

        Log::info('Passkey login options generated', ['options' => $optionsJson]);

        session()->put('passkey-authentication-options-json', $optionsJson);

        return response($optionsJson)->header('Content-Type', 'application/json');
    }

    /**
     * Authenticate a user using a passkey.
     */
    public function login(AuthenticateUsingPasskeysRequest $request, FindPasskeyToAuthenticateAction $findPasskeyAction)
    {
        $optionsJson = session()->get('passkey-authentication-options-json');

        Log::info('Passkey login attempt', [
            'has_options_in_session' => !empty($optionsJson),
            'request_payload' => $request->all()
        ]);

        if (!$optionsJson) {
            Log::warning('Passkey login failed: No options in session');
            return response()->json(['message' => 'No authentication options found in session. Please refresh the page.'], 400);
        }

        try {
            $passkey = $findPasskeyAction->execute(
                $request->input('start_authentication_response'),
                $optionsJson,
            );

            if (!$passkey) {
                Log::warning('Passkey login failed: FindPasskeyAction returned null');
                return response()->json(['message' => 'Invalid passkey. Device not found or validation failed.'], 400);
            }

            $user = $passkey->authenticatable;
            Log::info('Passkey login successful for user', ['user_id' => $user->id]);

            Auth::guard('customer')->login($user, $request->boolean('remember'));

            session()->regenerate();
            session()->put('logged_in_via_passkey', true);
            session()->forget('passkey-authentication-options-json');

            return response()->json([
                'message' => 'Successfully logged in.',
                'redirect_url' => route('shop.customers.account.index'),
            ]);
        } catch (\Exception $e) {
            Log::error('Passkey login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Authentication failed: ' . $e->getMessage()], 400);
        }
    }
}
