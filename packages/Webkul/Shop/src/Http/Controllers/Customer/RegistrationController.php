<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Webkul\Core\Repositories\SubscribersListRepository;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Shop\Http\Requests\Customer\RegistrationRequest;
use Webkul\Shop\Mail\Customer\EmailVerificationNotification;
use Webkul\Shop\Mail\Customer\RegistrationNotification;

class RegistrationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected CustomerGroupRepository $customerGroupRepository,
        protected SubscribersListRepository $subscriptionRepository,
        protected \Webkul\Customer\Services\MnemonicService $mnemonicService,
        protected \Webkul\Customer\Services\BlockchainAddressService $blockchainAddressService
    ) {
    }

    /**
     * Opens up the user's sign up form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (auth()->guard('customer')->check()) {
            return redirect()->route('shop.customers.account.index');
        }

        return view('shop::customers.sign-up');
    }

    /**
     * Check if username is available (live validation).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUsernameAvailability(Request $request)
    {
        $username = $request->input('username');

        if (!$username || !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $username) || strlen($username) < 3 || strlen($username) > 30) {
            return response()->json(['available' => false, 'message' => 'Некорректный псевдоним']);
        }

        // Check if exists in DB (both username and alias)
        $exists = $this->customerRepository->where(function($q) use ($username) {
            $q->where('username', $username)
              ->orWhere('credits_alias', $username);
        })->exists();

        if ($exists) {
            return response()->json(['available' => false, 'message' => 'Никнейм занят']);
        }

        // Check if currently locked in cache by another session
        $lockKey = 'registration:username:' . strtolower($username);
        if (Cache::has($lockKey) && Cache::get($lockKey) !== session()->getId()) {
            return response()->json(['available' => false, 'message' => 'Никнейм регистрируется прямо сейчас']);
        }

        return response()->json(['available' => true, 'message' => 'Никнейм свободен']);
    }

    /**
     * Prepare for passkey-only registration.
     * Creates a skeleton customer and returns passkey options.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function passkeyPrepare(Request $request)
    {
        if (auth()->guard('customer')->check()) {
             return response()->json(['message' => 'Вы уже вошли в аккаунт'], 403);
        }

        $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_\-\.]+$/'],
        ], [
            'username.required' => 'Укажите псевдоним',
            'username.min' => 'Псевдоним должен содержать от 3 до 30 символов',
            'username.max' => 'Псевдоним должен содержать от 3 до 30 символов',
            'username.regex' => 'Псевдоним может содержать только латиницу, цифры, минус, подчеркивание и точку',
        ]);

        $username = $request->input('username');
        $deviceName = $request->input('device_name', 'Default Device');

        // 1. Double check DB uniqueness
        if ($this->customerRepository->where('username', $username)->exists()) {
             return response()->json(['message' => 'Этот псевдоним уже занят', 'errors' => ['username' => ['Этот псевдоним уже занят']]], 422);
        }

        // Global reservation key to prevent address desynchronization across devices
        $reservationKey = 'registration_reservation:' . strtolower($username);
        $reservedData = Cache::get($reservationKey);

        if ($reservedData) {
            $mnemonicWords = $reservedData['mnemonic'];
            $recoveryKey = implode(' ', $mnemonicWords);
            $creditsId = $reservedData['credits_id'];
        } else {
            // Generate new BIP39 mnemonic with random length for better security
            $counts = [12, 15, 18, 21, 24];
            $wordCount = $counts[array_rand($counts)];
            $mnemonicWords = $this->mnemonicService->generateMnemonic($wordCount);
            $recoveryKey = implode(' ', $mnemonicWords);
            
            $wData = $this->blockchainAddressService->deriveEthereumWallet($mnemonicWords);
            $creditsId = $wData['address'] ?? null;

            // Reserve for 1 hour
            Cache::put($reservationKey, [
                'mnemonic'   => $mnemonicWords,
                'credits_id' => $creditsId,
            ], now()->addHour());
        }

        // Store all necessary data in session for the final store() call
        session([
            'pending_registration_data' => [
                'username'    => $username,
                'device_name' => $deviceName,
                'credits_id'  => $creditsId,
            ],
            'pending_recovery_key' => $recoveryKey
        ]);

        $mockCustomer = new \Webkul\Customer\Models\Customer([
            'username' => $username,
            'credits_id' => $creditsId
        ]);
        $mockCustomer->transient_passkey_id = $creditsId;

        return app(\Webkul\Shop\Http\Controllers\Customer\PasskeyController::class)->registerOptions(request(), app(\Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction::class), $mockCustomer);
    }


    /**
     * Method to store user's sign up form data to DB.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $registrationRequest = app(\Webkul\Shop\Http\Requests\Customer\RegistrationRequest::class);
            $registrationRequest->validateResolved();
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', $e->validator->errors()->first());
            return redirect()->back()->withInput();
        }
        $customerGroup = core()->getConfigData('customer.settings.create_new_account_options.default_group');

        $email = request()->input('email');
        $subscription = $email ? $this->subscriptionRepository->findOneWhere(['email' => $email]) : null;

        $currentIp = request()->header('CF-Connecting-IP')
            ?? request()->header('X-Forwarded-For')
            ?? request()->header('X-Real-IP')
            ?? request()->ip();

        if (str_contains($currentIp, ',')) {
            $currentIp = trim(explode(',', $currentIp)[0]);
        }

        // CRITICAL: Use the reserved/session mnemonic if available to ensure address consistency
        $recoveryKey = session('pending_recovery_key');
        if ($recoveryKey) {
            $mnemonicWords = explode(' ', $recoveryKey);
            $mnemonicHash = $this->mnemonicService->hashMnemonic($mnemonicWords);
        } else {
            // Fallback generation (standard BIP39)
            $counts = [12, 15, 18, 21, 24];
            $wordCount = $counts[array_rand($counts)];
            $mnemonicWords = $this->mnemonicService->generateMnemonic($wordCount);
            $recoveryKey = implode(' ', $mnemonicWords);
            $mnemonicHash = $this->mnemonicService->hashMnemonic($mnemonicWords);
            session(['pending_recovery_key' => $recoveryKey]);
        }
        
        $walletData = $this->blockchainAddressService->deriveEthereumWallet($mnemonicWords);
        $blockchainAddress = $walletData['address'];
        $privateKeyHex = $walletData['private_key'];
        $publicKeyData = $this->blockchainAddressService->derivePublicKeyData($mnemonicWords);

        // Store in session — will be shown after email link click
        session(['pending_recovery_key' => $recoveryKey]);

        $data = array_merge($registrationRequest->only([
            'email',
            'is_subscribed',
        ]), [
            'first_name' => 'Пользователь',
            'last_name' => '',
            'username' => $username = 'user_' . Str::random(8),
            'credits_alias' => $username,
            'password' => bcrypt($recoveryKey),
            'mnemonic_hash' => $mnemonicHash,
            'credits_id' => $blockchainAddress,
            'encrypted_private_key' => $privateKeyHex ? Crypt::encryptString($privateKeyHex) : null,
            'encrypted_mnemonic'    => Crypt::encryptString($recoveryKey),
            'public_key' => $publicKeyData['public_key'] ?? null,
            'public_key_hash' => $publicKeyData['public_key_hash'] ?? null,
            'is_matrix_enabled'     => 1,
            'api_token' => Str::random(80),
            'is_verified' => 1,
            'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => $customerGroup])?->getKey(),
            'channel_id' => core()->getCurrentChannel()?->getKey(),
            'token' => md5(uniqid(rand(), true)),
            'registration_ip' => $currentIp,
            'last_login_ip' => $currentIp,
            'subscribed_to_news_letter' => (bool) (request()->input('is_subscribed') ?? $subscription?->is_subscribed),
        ]);

        Event::dispatch('customer.registration.before');

        $customer = $this->customerRepository->create($data);

        if ($subscription) {
            $this->subscriptionRepository->update([
                'customer_id' => $customer->id,
            ], $subscription->id);
        }

        if (
            !empty($data['is_subscribed'])
            && !$subscription
            && !empty($data['email'])
        ) {
            Event::dispatch('customer.subscription.before');

            $subscription = $this->subscriptionRepository->create([
                'email' => $data['email'],
                'customer_id' => $customer->id,
                'channel_id' => data_get(core()->getCurrentChannel(), 'id'),
                'is_subscribed' => 1,
                'token' => uniqid(),
            ]);

            Event::dispatch('customer.subscription.after', $subscription);
        }

        Event::dispatch('customer.create.after', $customer);

        Event::dispatch('customer.registration.after', $customer);

        // No email verification needed - account is verified by default
        
        // Welcome Bonus Minting is handled via WelcomeBonusListener on customer.registration.after

        // log in immediately
        auth()->guard('customer')->login($customer);

        session()->regenerate();

        $customerRepo = app(\Webkul\Customer\Repositories\CustomerRepository::class);
        $customerRepo->update([
            'first_login_ip' => $currentIp,
            'last_login_ip' => $currentIp,
        ], $customer->id);

        // Log login activity
        app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($customer);

        Event::dispatch('customer.after.login', auth()->guard('customer')->user());
        session()->flash('recovery_key', $recoveryKey);
        
        // Capture intended URL for after-registration flow (multi-step seed backup)
        if ($intended = session()->get('url.intended')) {
            session(['registration_intended_url' => $intended]);
        }
        
        session()->forget('pending_recovery_key');

        // Force session save to ensure the next request sees the authentication state
        session()->save();

        // If the user came specifically to redeem a voucher, skip onboarding and go straight there
        return redirect()->route('shop.customers.account.onboarding.security');
    }

    /**
     * Prepare for "Other Device" registration (QR code).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function passkeyPrepareOtherDevice(Request $request)
    {
        $username = $request->input('username');
        
        // This actually re-uses the preparation logic but instead of returning options, 
        // it returns a signed URL for the phone.
        $prepareResponse = $this->passkeyPrepare($request);
        
        if ($prepareResponse->getStatusCode() !== 200) {
            return $prepareResponse;
        }

        $token = Str::random(64);
        $pendingData = session('pending_registration_data');
        $recoveryKey = session('pending_recovery_key');

        // Store registration state in cache for 10 minutes (to be picked up by the phone)
        Cache::put('reg_token:' . $token, [
            'data'         => $pendingData,
            'recovery_key' => $recoveryKey,
        ], now()->addMinutes(10));

        // Create the signed URL for the QR code
        $url = URL::signedRoute('shop.customers.register.phone.landing', [
            'token' => $token,
        ]);

        // Create the signed URL for marking continuation (for desktop to call)
        $markContinuingUrl = URL::signedRoute('shop.customers.register.phone.mark_continuing', [
            'token' => $token,
        ]);

        return response()->json([
            'url'                 => $url,
            'token'               => $token,
            'mark_continuing_url' => $markContinuingUrl
        ]);
    }

    /**
     * Landing page for the phone to finish registration.
     */
    public function registrationPhoneLanding(Request $request, $token)
    {
        $cached = Cache::get('reg_token:' . $token);

        if (!$cached) {
            abort(404, 'Сессия регистрации истекла или недействительна.');
        }

        // IMPORTANT: If a user is already logged in on this phone (stale session), 
        // we must logout before starting a NEW registration.
        if (auth()->guard('customer')->check()) {
            auth()->guard('customer')->logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        // Transfer cache data to the phone's session
        session([
            'pending_registration_data' => $cached['data'],
            'pending_recovery_key'      => $cached['recovery_key'],
            'pending_registration_id'   => $cached['data']['credits_id'],
            'registration_token'        => $token,
        ]);
        
        $username = $cached['data']['username'];

        $markContinuingUrl = URL::signedRoute('shop.customers.register.phone.mark_continuing', [
            'token' => $token,
        ]);

        $checkStatusUrl = route('shop.customers.register.check_status');

        return view('shop::customers.registration-phone-landing', compact('username', 'token', 'markContinuingUrl', 'checkStatusUrl'));
    }

    /**
     * Poll endpoint for desktop to check if registration is complete.
     */
    public function checkRegistrationStatus(Request $request)
    {
        $username = $request->input('username');
        
        if (!$username) {
            return response()->json(['complete' => false]);
        }

        $user = $this->customerRepository->findOneByField('username', $username);

        if ($user) {
            // Log in the user on the desktop session if not already logged in
            if (!auth()->guard('customer')->check()) {
                auth()->guard('customer')->login($user, true);
                
                // Track login activity
                app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($user);

                // Important: Persist session immediately for AJAX response
                // We skip regenerate() here to avoid race conditions with AJAX headers
                session()->save();
            }

            return response()->json([
                'complete'                => true,
                'redirect_url'            => route('shop.customers.account.onboarding.security'),
                'continuing_device'       => Cache::get('reg_continuing:' . $request->input('token')),
                'is_continuing_elsewhere' => Cache::get('reg_continuing:' . $request->input('token')) && Cache::get('reg_continuing:' . $request->input('token')) !== $request->input('device'),
            ]);
        }

        return response()->json(['complete' => false]);
    }

    /**
     * Mark the registration as being continued on the current device (phone or pc).
     */
    public function markAsContinuing(Request $request, $token)
    {
        Cache::put('reg_continuing:' . $token, $request->input('device', 'phone'), now()->addMinutes(10));

        return response()->json(['success' => true]);
    }
}
