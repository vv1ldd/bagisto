<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
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
        protected \Webkul\Customer\Services\MnemonicService $mnemonicService
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
     * Prepare for passkey-only registration.
     * Creates a skeleton customer and returns passkey options.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function passkeyPrepare(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_\-\.]+$/', 'unique:customers,username'],
        ], [
            'username.required' => 'Укажите псевдоним',
            'username.min' => 'Псевдоним должен содержать от 3 до 30 символов',
            'username.max' => 'Псевдоним должен содержать от 3 до 30 символов',
            'username.regex' => 'Псевдоним может содержать только латиницу, цифры, минус, подчеркивание и точку',
            'username.unique' => 'Этот псевдоним уже занят',
        ]);

        $username = $request->input('username');

        $currentIp = request()->header('CF-Connecting-IP')
            ?? request()->header('X-Forwarded-For')
            ?? request()->header('X-Real-IP')
            ?? request()->ip();

        if (str_contains($currentIp, ',')) {
            $currentIp = trim(explode(',', $currentIp)[0]);
        }

        // Create skeleton customer (no seed phrase generated yet - user can do it from dashboard)
        $customerGroup = core()->getConfigData('customer.settings.create_new_account_options.default_group');
        
        $data = [
            'first_name' => 'Пользователь',
            'last_name' => '',
            'username' => $username,
            'email' => null, // Allowed per migration
            'password' => bcrypt(Str::random(40)), // Secure random password, not tied to seed phrase
            'mnemonic_hash' => null, // Will be set when user chooses to create backup
            'api_token' => Str::random(80),
            'is_verified' => 1,
            'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => $customerGroup])->id,
            'channel_id' => core()->getCurrentChannel()->id,
            'token' => md5(uniqid(rand(), true)),
            'registration_ip' => $currentIp,
            'last_login_ip' => $currentIp,
            'status' => 1,
            'subscribed_to_news_letter' => 1,
        ];

        // If already logged in as a placeholder (no email), reuse the account
        $customer = auth()->guard('customer')->user();
        
        if ($customer && is_null($customer->email)) {
            // Reuse existing placeholder
        } else {
            Event::dispatch('customer.registration.before');

            $customer = $this->customerRepository->create($data);

            Event::dispatch('customer.registration.after', $customer);

            // Log them in immediately so passkey registration can proceed
            auth()->guard('customer')->login($customer);
        }
        
        // Return passkey options via the PasskeyController logic
        return app(\Webkul\Shop\Http\Controllers\Customer\PasskeyController::class)->registerOptions(request(), app(\Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction::class));
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

        $subscription = $this->subscriptionRepository->findOneWhere(['email' => request()->input('email')]);

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

        // Store in session — will be shown after email link click
        session(['pending_recovery_key' => $recoveryKey]);

        $data = array_merge($registrationRequest->only([
            'email',
            'is_subscribed',
        ]), [
            'first_name' => 'Пользователь',
            'last_name' => '',
            'username' => 'user_' . Str::random(8),
            'password' => bcrypt($recoveryKey),
            'mnemonic_hash' => $mnemonicHash,
            'api_token' => Str::random(80),
            'is_verified' => !core()->getConfigData('customer.settings.email.verification'),
            'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => $customerGroup])->id,
            'channel_id' => core()->getCurrentChannel()->id,
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
        ) {
            Event::dispatch('customer.subscription.before');

            $subscription = $this->subscriptionRepository->create([
                'email' => $data['email'],
                'customer_id' => $customer->id,
                'channel_id' => core()->getCurrentChannel()->id,
                'is_subscribed' => 1,
                'token' => uniqid(),
            ]);

            Event::dispatch('customer.subscription.after', $subscription);
        }

        Event::dispatch('customer.create.after', $customer);

        Event::dispatch('customer.registration.after', $customer);

        if (core()->getConfigData('customer.settings.email.verification')) {
            // Show a "check your email for the link" screen in-place
            return redirect()->back()->with([
                'status' => 'verification-sent',
                'email' => $customer->email,
            ]);
        }

        // Verification disabled — log in immediately
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
        session()->forget('pending_recovery_key');

        return redirect()->route('shop.customers.account.profile.recovery_key');
    }

    /**
     * Show the 6-digit code entry page after registration.
     */
    public function showVerifyCode()
    {
        $email = session('verification_email');

        if (!$email) {
            return redirect()->route('shop.customers.register.index');
        }

        return view('shop::customers.verify-code', compact('email'));
    }

    /**
     * Verify account by the 6-digit code the user entered.
     */
    public function verifyByCode(Request $request)
    {
        $email = session('verification_email');

        if (!$email) {
            return redirect()->route('shop.customers.register.index');
        }

        // Assemble code from individual digit boxes (d0..d5), fall back to 'code' field
        $enteredCode = '';
        for ($i = 0; $i < 6; $i++) {
            $enteredCode .= $request->input('d' . $i, '');
        }
        $enteredCode = preg_replace('/\D/', '', $enteredCode);

        // Fallback: single 'code' field
        if (strlen($enteredCode) !== 6) {
            $enteredCode = preg_replace('/\D/', '', $request->input('code', ''));
        }

        if (strlen($enteredCode) !== 6) {
            return back()->withErrors(['code' => 'Введите 6-значный код из письма.']);
        }


        // Find customer by email first
        $customer = $this->customerRepository->findOneByField('email', $email);

        if (!$customer) {
            return back()->withErrors(['code' => 'Неверный код. Проверьте письмо и попробуйте снова.']);
        }

        // Validate code: session (most reliable) → DB column → cache
        $storedCode = session('verification_code_pending')
            ?? $customer->verification_code
            ?? \Illuminate\Support\Facades\Cache::get("verification_code_{$email}");

        if (!$storedCode || $storedCode !== $enteredCode) {
            return back()->withErrors(['code' => 'Неверный код. Проверьте письмо и попробуйте снова.']);
        }

        $this->customerRepository->update([
            'is_verified' => 1,
            'token' => null,
            'verification_code' => null,
        ], $customer->id);

        // Clear cache
        \Illuminate\Support\Facades\Cache::forget("verification_code_{$email}");

        if ((bool) core()->getConfigData('emails.general.notifications.registration')) {
            Mail::queue(new RegistrationNotification($customer));
        }

        $this->customerRepository->syncNewRegisteredCustomerInformation($customer);

        session()->forget('verification_email');

        auth()->guard('customer')->login($customer);

        $currentIp = request()->header('CF-Connecting-IP')
            ?? request()->header('X-Forwarded-For')
            ?? request()->header('X-Real-IP')
            ?? request()->ip();

        if (str_contains($currentIp, ',')) {
            $currentIp = trim(explode(',', $currentIp)[0]);
        }

        $this->customerRepository->update([
            'first_login_ip' => $currentIp,
            'last_login_ip' => $currentIp,
        ], $customer->id);

        // Log login activity
        app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($customer);

        Event::dispatch('customer.after.login', auth()->guard()->user());

        session()->flash('success', 'Регистрация прошла успешно! Пожалуйста, заполните данные вашего профиля.');

        return redirect()->route('shop.customers.account.profile.recovery_key');
    }

    /**
     * Method to verify account.
     *
     * @param  string  $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyAccount($token)
    {
        $customer = $this->customerRepository->findOneByField('token', $token);

        if (!$customer) {
            session()->flash('info', 'Ссылка уже была использована. Войдите в аккаунт или запросите новое письмо.');

            return redirect()->route('shop.customer.session.index');
        }

        $this->customerRepository->update([
            'is_verified' => 1,
            'token' => null,
            'verification_code' => null,
        ], $customer->id);

        if ((bool) core()->getConfigData('emails.general.notifications.registration')) {
            Mail::queue(new RegistrationNotification($customer));
        }

        $this->customerRepository->syncNewRegisteredCustomerInformation($customer);

        // Auto-login after clicking the verification link
        auth()->guard('customer')->login($customer);

        $currentIp = request()->header('CF-Connecting-IP')
            ?? request()->header('X-Forwarded-For')
            ?? request()->header('X-Real-IP')
            ?? request()->ip();

        if (str_contains($currentIp, ',')) {
            $currentIp = trim(explode(',', $currentIp)[0]);
        }

        $this->customerRepository->update([
            'first_login_ip' => $currentIp,
            'last_login_ip' => $currentIp,
        ], $customer->id);

        // Log login activity
        app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($customer);

        Event::dispatch('customer.after.login', $customer);

        // Flag to skip "Current Password" on first profile edit
        session(['onboarding_no_password' => true]);

        // Retrieve recovery key stored before email was sent and flash it for one-time display
        $recoveryKey = session('pending_recovery_key');
        if ($recoveryKey) {
            session()->flash('recovery_key', $recoveryKey);
            session()->forget('pending_recovery_key');
        }

        session()->flash('success', 'Почта подтверждена! Пожалуйста, заполните данные профиля.');

        return redirect()->route('shop.customers.account.profile.recovery_key');
    }

    /**
     * Resend verification email.
     *
     * @param  string  $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendVerificationEmail($email)
    {
        $verificationData = [
            'email' => $email,
            'token' => md5(uniqid(rand(), true)),
        ];

        $customer = $this->customerRepository->findOneByField('email', $email);

        $this->customerRepository->update(['token' => $verificationData['token']], $customer->id);
        $customer = $this->customerRepository->findOneByField('email', $email);

        try {
            Mail::queue(new EmailVerificationNotification($customer));

            if (Cookie::has('enable-resend')) {
                Cookie::queue(Cookie::forget('enable-resend'));
            }

            if (Cookie::has('email-for-resend')) {
                Cookie::queue(Cookie::forget('email-for-resend'));
            }
        } catch (\Exception $e) {
            report($e);

            session()->flash('error', trans('shop::app.customers.signup-form.verification-not-sent'));

            return redirect()->back();
        }

        session()->flash('success', trans('shop::app.customers.signup-form.verification-sent'));

        return redirect()->back();
    }
}
