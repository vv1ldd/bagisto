<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Webkul\Admin\Http\Controllers\Controller;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyAuthenticationOptionsAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;
use Spatie\LaravelPasskeys\Http\Requests\AuthenticateUsingPasskeysRequest;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Webkul\User\Repositories\AdminRepository;

class PasskeyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected AdminRepository $adminRepository)
    {
    }

    /**
     * Generate registration options for a new passkey.
     */
    public function registerOptions(Request $request, GeneratePasskeyRegisterOptionsAction $generateOptionsAction)
    {
        // Always sync RP ID to current request host
        $currentHost = $request->getHost();
        config(['passkeys.relying_party.id' => $currentHost]);

        $user = Auth::guard('admin')->user();
        
        if (!$user || !($user instanceof \Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys)) {
            return response()->json(['message' => 'Unauthenticated or Passkeys not supported.'], 401);
        }

        $optionsJson = $generateOptionsAction->execute($user, asJson: true);
        
        // Patch options for Google Password Manager compatibility (similar to shop)
        $optionsArr = json_decode($optionsJson, true);
        if (isset($optionsArr['user'])) {
            $asciiName = $user->email; // Use email as it's definitely unique and likely ASCII
            $optionsArr['user']['name'] = $asciiName;
            $optionsArr['user']['displayName'] = $user->name;

            // user.id: must be stable random bytes (hash of DB id)
            $stableKey = hash_hmac('sha256', 'passkey-admin-id:' . $user->id, config('app.key'), true);
            $optionsArr['user']['id'] = rtrim(strtr(base64_encode(substr($stableKey, 0, 32)), '+/', '-_'), '=');

            $optionsArr['attestation'] = 'none';
            $optionsArr['timeout'] = 60000;

            $optionsJson = json_encode($optionsArr);
        }

        $request->session()->put('admin-passkey-registration-options-json', $optionsJson);

        return response($optionsJson)->header('Content-Type', 'application/json');
    }

    /**
     * Store the newly registered passkey.
     */
    public function register(Request $request, StorePasskeyAction $storePasskeyAction)
    {
        // Always sync RP ID to current request host
        $currentHost = $request->getHost();
        config(['passkeys.relying_party.id' => $currentHost]);
        
        $user = Auth::guard('admin')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $optionsJson = session('admin-passkey-registration-options-json');

        if (!$optionsJson) {
            return response()->json(['message' => 'No registration options found in session.'], 400);
        }

        try {
            $userAgent = $request->userAgent();
            $deviceName = $this->getDeviceName($userAgent);
            
            \Illuminate\Support\Facades\DB::beginTransaction();

            $storePasskeyAction->execute(
                authenticatable: $user,
                passkeyJson: json_encode($request->all()),
                passkeyOptionsJson: $optionsJson,
                hostName: $currentHost,
            );

            // Update passkey details
            $passkey = $user->passkeys()->latest()->first();
            if ($passkey) {
                $passkey->update([
                    'name' => $deviceName,
                    'user_agent' => $userAgent,
                    'ip_address' => $request->ip(),
                ]);
            }

            $request->session()->forget('admin-passkey-registration-options-json');

            \Illuminate\Support\Facades\DB::commit();

            return response()->json(['message' => 'Passkey registered successfully.']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['message' => 'Registration failed: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Delete an existing passkey.
     */
    public function destroy($id)
    {
        $user = Auth::guard('admin')->user();

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
        // Always sync RP ID to current request host
        $currentHost = request()->getHost();
        config(['passkeys.relying_party.id' => $currentHost]);

        $optionsJson = $generateOptionsAction->execute();

        session()->put('admin-passkey-authentication-options-json', $optionsJson);

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

        $optionsJson = session()->get('admin-passkey-authentication-options-json');

        if (!$optionsJson) {
            return response()->json(['message' => 'No authentication options found in session.'], 400);
        }

        try {
            $credentialResponse = $request->input('start_authentication_response');
            
            if (is_array($credentialResponse)) {
                $credentialResponse = json_encode($credentialResponse);
            }

            $passkey = $findPasskeyAction->execute(
                $credentialResponse,
                $optionsJson,
            );

            if (!$passkey || !($passkey->authenticatable instanceof \Webkul\User\Models\Admin)) {
                return response()->json(['message' => 'Invalid passkey or not an admin account.'], 401);
            }

            $user = $passkey->authenticatable;
            
            Auth::guard('admin')->login($user, $request->boolean('remember'));

            session()->regenerate();
            session()->forget('admin-passkey-authentication-options-json');

            return response()->json([
                'message' => 'Successfully authenticated.',
                'redirect_url' => route('admin.dashboard.index'),
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Passkey login error: ' . $e->getMessage());
            return response()->json(['message' => 'Authentication failed.'], 400);
        }
    }

    /**
     * Get human-readable device name from User-Agent.
     */
    protected function getDeviceName($userAgent)
    {
        if (empty($userAgent)) return 'Passkey Device';

        if (stripos($userAgent, 'iPhone') !== false) return 'iPhone';
        if (stripos($userAgent, 'iPad') !== false) return 'iPad';
        if (stripos($userAgent, 'Android') !== false) return 'Android Device';
        if (stripos($userAgent, 'Macintosh') !== false) return 'Mac';
        if (stripos($userAgent, 'Windows') !== false) return 'Windows PC';
        if (stripos($userAgent, 'Linux') !== false) return 'Linux PC';

        return 'Passkey Device';
    }
}
