<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

        // Check if exists in DB
        $exists = $this->customerRepository->where('username', $username)->exists();
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
        $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_\-\.]+$/'],
        ], [
            'username.required' => 'Укажите псевдоним',
            'username.min' => 'Псевдоним должен содержать от 3 до 30 символов',
            'username.max' => 'Псевдоним должен содержать от 3 до 30 символов',
            'username.regex' => 'Псевдоним может содержать только латиницу, цифры, минус, подчеркивание и точку',
        ]);

        $username = $request->input('username');
        
        // 1. Double check DB uniqueness
        if ($this->customerRepository->where('username', $username)->exists()) {
             return response()->json(['message' => 'Этот псевдоним уже занят', 'errors' => ['username' => ['Этот псевдоним уже занят']]], 422);
        }

        // 2. Concurrency Check via Cache Lock
        $lockKey = 'registration:username:' . strtolower($username);
        if (Cache::has($lockKey) && Cache::get($lockKey) !== session()->getId()) {
            return response()->json(['message' => 'Этот псевдоним регистрируется прямо сейчас другим пользователем. Попробуйте другой.', 'errors' => ['username' => ['Этот псевдоним регистрируется прямо сейчас.']]], 422);
        }

        // Lock for 5 minutes
        Cache::put($lockKey, session()->getId(), now()->addMinutes(5));

        $currentIp = request()->header('CF-Connecting-IP')
            ?? request()->header('X-Forwarded-For')
            ?? request()->header('X-Real-IP')
            ?? request()->ip();

        if (str_contains($currentIp, ',')) {
            $currentIp = trim(explode(',', $currentIp)[0]);
        }

        // Generate a random-length BIP39 mnemonic (12 words for passkey)
        $mnemonicWords = $this->mnemonicService->generateMnemonic(12);
        $recoveryKey = implode(' ', $mnemonicWords);
        $mnemonicHash = $this->mnemonicService->hashMnemonic($mnemonicWords);
        
        // Derive full wallet (address + private key)
        $walletData = $this->blockchainAddressService->deriveEthereumWallet($mnemonicWords);
        $blockchainAddress = $walletData['address'];
        $encryptedPrivateKey = \Illuminate\Support\Facades\Crypt::encryptString($walletData['private_key']);

        // Store in session — will be shown after passkey registration
        session(['pending_recovery_key' => $recoveryKey]);

        // Generate data for future insertion
        $customerGroup = core()->getConfigData('customer.settings.create_new_account_options.default_group');
        
        $data = [
            'first_name' => 'Пользователь',
            'last_name' => '',
            'username' => $username,
            'credits_alias' => $username,
            'credits_id' => $blockchainAddress,
            'email' => null, // Allowed per migration
            'password' => bcrypt(Str::random(40)),
            'mnemonic_hash' => $mnemonicHash,
            'encrypted_private_key' => $encryptedPrivateKey,
            'api_token' => Str::random(80),
            'is_verified' => 1,
            'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => $customerGroup])?->getKey(),
            'channel_id' => core()->getCurrentChannel()?->getKey(),
            'token' => md5(uniqid(rand(), true)),
            'registration_ip' => $currentIp,
            'last_login_ip' => $currentIp,
            'status' => 1,
            'subscribed_to_news_letter' => 1,
        ];

        // Ensure we don't accidentally reuse an existing placeholder (we want passkeys to correspond 1-to-1)
        
        // Generate credits_id and username early for stable Passkey ID
        $data['credits_id'] = \Webkul\Customer\Models\Customer::generateUniqueCreditsId();
        
        // Abstract a Mock Customer for options generation
        $mockCustomer = new \Webkul\Customer\Models\Customer($data);
        $mockCustomer->transient_passkey_id = $data['credits_id']; 
        
        // Store in session
        session([
            'pending_registration_data' => $data,
            'pending_registration_id' => $data['credits_id']
        ]);

        // Return passkey options via the PasskeyController logic
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

        // Generate a random-length BIP39 mnemonic (12, 15, 18, 21, 24 words)
        $counts = [12, 15, 18, 21, 24];
        $wordCount = $counts[array_rand($counts)];
        $mnemonicWords = $this->mnemonicService->generateMnemonic($wordCount);
        $recoveryKey = implode(' ', $mnemonicWords);
        $mnemonicHash = $this->mnemonicService->hashMnemonic($mnemonicWords);
        
        $walletData = $this->blockchainAddressService->deriveEthereumWallet($mnemonicWords);
        $blockchainAddress = $walletData['address'];
        $encryptedPrivateKey = \Illuminate\Support\Facades\Crypt::encryptString($walletData['private_key']);
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
            'encrypted_private_key' => $encryptedPrivateKey,
            'public_key' => $publicKeyData['public_key'] ?? null,
            'public_key_hash' => $publicKeyData['public_key_hash'] ?? null,
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
}
