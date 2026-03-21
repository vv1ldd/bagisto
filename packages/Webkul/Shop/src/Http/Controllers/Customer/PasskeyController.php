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
        
        // Handle linking flow where user is found via session or manual auth during landing
        if (!$user && session()->has('link_user_id')) {
            $user = app(\Webkul\Customer\Repositories\CustomerRepository::class)->find(session('link_user_id'));
        }

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $optionsJson = $generateOptionsAction->execute($user, asJson: true);

        $request->session()->put('passkey-registration-options-json', $optionsJson);

        return response($optionsJson)->header('Content-Type', 'application/json');
    }

    /**
     * Generate a signed link for adding a second device (via QR).
     */
    public function generateLink()
    {
        $user = Auth::guard('customer')->user();

        $url = \Illuminate\Support\Facades\URL::signedRoute('shop.customers.account.passkeys.link', [
            'user_id' => $user->id,
        ], now()->addMinutes(15));

        return response()->json(['url' => $url]);
    }

    /**
     * Landing page for the second device via QR.
     */
    public function linkLanding(Request $request)
    {
        $userId = $request->input('user_id');
        $user = app(\Webkul\Customer\Repositories\CustomerRepository::class)->find($userId);

        if (!$user) {
            abort(404);
        }

        // Store user ID in session so registerOptions/register can find it
        session(['link_user_id' => $user->id]);

        return view('shop::customers.account.passkeys.link-register', compact('user'));
    }

    /**
     * Store the newly registered passkey.
     */
    public function register(Request $request, StorePasskeyAction $storePasskeyAction)
    {
        $user = Auth::guard('customer')->user();
        
        if (!$user && session()->has('link_user_id')) {
            $user = app(\Webkul\Customer\Repositories\CustomerRepository::class)->find(session('link_user_id'));
        }

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

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
                $currentIp = $request->header('CF-Connecting-IP')
                    ?? $request->header('X-Forwarded-For')
                    ?? $request->header('X-Real-IP')
                    ?? $request->ip();

                if (str_contains($currentIp, ',')) {
                    $currentIp = trim(explode(',', $currentIp)[0]);
                }

                $passkey->update([
                    'name' => $deviceName,
                    'user_agent' => $userAgent,
                    'ip_address' => $currentIp,
                ]);

                // Track this passkey as the current device's passkey
                session()->put('current_session_passkey_id', $passkey->id);
                Cookie::queue('current_device_passkey_id', $passkey->id, 60 * 24 * 365); // 1 year cookie
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
            Log::info('Passkey validation successful for user', ['user_id' => $user->id]);

            $currentUser = Auth::guard('customer')->user();

            if ($currentUser && $currentUser->id === $user->id) {
                // User is already logged in, this is likely a step-up authentication (e.g. for Meanly Wallet)
                Log::info('Passkey step-up authentication successful, skipping redundant login log', ['user_id' => $user->id]);
            } else {
                // Initial login or switching users
                Auth::guard('customer')->login($user, $request->boolean('remember'));

                // Log login activity
                app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($user);

                session()->regenerate();
            }

            session()->put('logged_in_via_passkey', true);
            session()->put('passkey_unlocked_at', now()->timestamp);
            session()->put('current_session_passkey_id', $passkey->id); // Track current passkey
            Cookie::queue('current_device_passkey_id', $passkey->id, 60 * 24 * 365); // Track device via cookie

            session()->forget('passkey-authentication-options-json');

            return response()->json([
                'message' => 'Successfully authenticated.',
                'redirect_url' => redirect()->intended(route('shop.customers.account.index'))->getTargetUrl(),
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
