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
    public function registerOptions(Request $request, GeneratePasskeyRegisterOptionsAction $generateOptionsAction, $mockUser = null)
    {
        // Always sync RP ID to current request host
        $currentHost = $request->getHost();
        config(['passkeys.relying_party.id' => $currentHost]);

        $user = Auth::guard('customer')->user() ?? $mockUser;
        
        $linkingFlow = false;
        // Handle linking flow where user is found via session or manual auth during landing
        if (!$user && session()->has('link_user_id')) {
            $user = app(\Webkul\Customer\Repositories\CustomerRepository::class)->find(session('link_user_id'));
            $linkingFlow = true;
        }

        if (!$user) {
            Log::warning('Passkey registration options failed: Unauthenticated.', [
                'session_has_link_user_id' => session()->has('link_user_id'),
                'session_id' => session()->getId(),
            ]);
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $optionsJson = $generateOptionsAction->execute($user, asJson: true);
        
        // Use Spatie's generated options as a base, but ensure we have mandatory params 
        // that some browsers (like Chrome/Mac) are picky about.
        $optionsArr = json_decode($optionsJson, true);
        if (isset($optionsArr['user'])) {
            // Ensure pubKeyCredParams is populated
            if (empty($optionsArr['pubKeyCredParams'])) {
                $optionsArr['pubKeyCredParams'] = [
                    ['type' => 'public-key', 'alg' => -7],   // ES256
                    ['type' => 'public-key', 'alg' => -257], // RS256
                ];
            }
            $optionsArr['attestation'] = 'none';
            $optionsArr['timeout'] = 60000;

            $optionsJson = json_encode($optionsArr);
        }

        Log::info('Passkey registration options generated', [
            'user_id' => $user->id,
            'user_id_in_options' => $optionsArr['user']['id'] ?? 'N/A',
            'rp_id' => $optionsArr['rp']['id'] ?? config('passkeys.relying_party.id'),
            'user_name' => $optionsArr['user']['name'] ?? 'N/A',
            'session_id' => session()->getId(),
        ]);

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
        // Always sync RP ID to current request host
        $currentHost = $request->getHost();
        config(['passkeys.relying_party.id' => $currentHost]);
        $user = Auth::guard('customer')->user();
        
        $linkingFlow = false;
        if (!$user && session()->has('link_user_id')) {
            $user = app(\Webkul\Customer\Repositories\CustomerRepository::class)->find(session('link_user_id'));
            $linkingFlow = true;
        }

        $pendingRegistrationData = session('pending_registration_data');
        if (!$user && !$pendingRegistrationData) {
            Log::warning('Passkey register failed: Unauthenticated and no pending registration.', [
                'session_has_link_user_id' => session()->has('link_user_id'),
                'session_id' => session()->getId(),
            ]);
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Security check: If we have pending registration data, but someone is ALREADY logged in,
        // we must not allow merging the new passkey into the existing account.
        if ($user && $pendingRegistrationData) {
             Log::error('Passkey registration conflict: User already logged in during new registration.', [
                'auth_user_id' => $user->id,
                'pending_username' => $pendingRegistrationData['username'] ?? 'unknown',
            ]);
            return response()->json(['message' => 'Вы уже вошли в другой аккаунт. Пожалуйста, выйдите перед регистрацией.'], 403);
        }

        $optionsJson = $request->session()->get('passkey-registration-options-json');

        Log::info('Passkey registration attempt', [
            'user_id' => $user->id ?? 'pending',
            'linking_flow' => $linkingFlow,
            'is_new_registration' => !empty($pendingRegistrationData),
            'has_options_in_session' => !empty($optionsJson),
            'session_id' => session()->getId(),
        ]);

        if (!$optionsJson) {
            return response()->json(['message' => 'No registration options found in session.'], 400);
        }

        try {
            $userAgent = $request->userAgent();
            $deviceName = $this->getDeviceName($userAgent);
            
            \Illuminate\Support\Facades\DB::beginTransaction();

            $isNewRegistration = false;
            if (!$user && $pendingRegistrationData) {
                // Verify the caching lock hasn't expired / is still ours
                $username = $pendingRegistrationData['username'];
                $lockKey = 'registration:username:' . strtolower($username);
                // Even if lock is missing, we try inserting (DB unique constraint will catch duplicates)
                
                // Fire before event
                \Illuminate\Support\Facades\Event::dispatch('customer.registration.before');

                $user = app(\Webkul\Customer\Repositories\CustomerRepository::class)->create($pendingRegistrationData);
                $isNewRegistration = true;

                \Illuminate\Support\Facades\Event::dispatch('customer.registration.after', $user);
                
                // Clear the cache lock
                \Illuminate\Support\Facades\Cache::forget($lockKey);
            }

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
            if ($isNewRegistration) {
                $request->session()->forget('pending_registration_data');
                $request->session()->forget('pending_registration_id');
            }

            // ---- TRUSTED DEVICE LOGIC ----
            $cookieToken = Str::random(64);
            $trustedDeviceRepository = app(\Webkul\Customer\Repositories\CustomerTrustedDeviceRepository::class);

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

            // Log activity
            app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->logEvent($user, 'passkey_registered', [
                'device_name' => $deviceName,
            ]);

            if ($isNewRegistration && session()->has('pending_recovery_key')) {
                session()->flash('recovery_key', session('pending_recovery_key'));
                session()->forget('pending_recovery_key');
            }

            if (!Auth::guard('customer')->check() && ($linkingFlow || $isNewRegistration)) {
                Auth::guard('customer')->login($user);

                session()->regenerate();

                session()->forget('link_user_id');
                
                // Track login activity
                app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($user);
            }

            session()->save();

            \Illuminate\Support\Facades\DB::commit();

            $redirectUrl = $isNewRegistration 
                ? route('shop.customers.account.onboarding.security')
                : redirect()->intended(route('shop.customers.account.onboarding.security'))->getTargetUrl();

            return response()->json([
                'message'      => 'Passkey registered successfully.',
                'redirect_url' => $redirectUrl,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            
            // If the user was just created in this transaction, clear lock as it failed
            if (isset($pendingRegistrationData['username'])) {
                 \Illuminate\Support\Facades\Cache::forget('registration:username:' . strtolower($pendingRegistrationData['username']));
            }
            
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
        $deviceName = $passkey->name;
        $passkey->delete();

        // Log activity
        app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->logEvent($user, 'passkey_deleted', [
            'device_name' => $deviceName,
        ]);

        session()->flash('success', 'Passkey deleted successfully.');

        return redirect()->back();
    }

    /**
     * Generate authentication options for passkey login.
     */
    public function loginOptions(GeneratePasskeyAuthenticationOptionsAction $generateOptionsAction)
    {
        // Always sync RP ID to current request host
        $currentHost = request()->getHost();
        config(['passkeys.relying_party.id' => $currentHost]);

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
        // Always sync RP ID to current request host
        $currentHost = $request->getHost();
        config(['passkeys.relying_party.id' => $currentHost]);

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
            $credentialResponse = $request->input('start_authentication_response');
            
            // Spatie action expects a JSON string. 
            // If it's already an array (decoded by Laravel), convert it back to string.
            if (is_array($credentialResponse)) {
                $credentialResponse = json_encode($credentialResponse);
            }

            Log::debug('Passkey login: Final credential response for validation', [
                'type' => gettype($credentialResponse),
                'is_string' => is_string($credentialResponse),
            ]);

            $passkey = $findPasskeyAction->execute(
                $credentialResponse,
                $optionsJson,
            );

            if (!$passkey) {
                $decodedResp = is_string($credentialResponse) ? json_decode($credentialResponse, true) : $credentialResponse;
                Log::warning('Passkey login failed: FindPasskeyAction returned null', [
                    'user_id' => auth()->guard('customer')->id() ?? 'guest',
                    'credential_id' => $decodedResp['id'] ?? 'N/A',
                ]);
                return response()->json(['message' => 'Invalid passkey. Device not found or validation failed.'], 401);
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

            session()->save();

            session()->forget('passkey-authentication-options-json');

            $redirectUrl = redirect()->intended(route('shop.home.index'))->getTargetUrl();

            return response()->json([
                'message' => 'Successfully authenticated.',
                'redirect_url' => $redirectUrl,
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
