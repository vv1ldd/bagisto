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
        protected SubscribersListRepository $subscriptionRepository
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
     * Method to store user's sign up form data to DB.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RegistrationRequest $registrationRequest)
    {
        $customerGroup = core()->getConfigData('customer.settings.create_new_account_options.default_group');

        $subscription = $this->subscriptionRepository->findOneWhere(['email' => request()->input('email')]);

        $currentIp = request()->header('CF-Connecting-IP')
            ?? request()->header('X-Forwarded-For')
            ?? request()->header('X-Real-IP')
            ?? request()->ip();

        if (str_contains($currentIp, ',')) {
            $currentIp = trim(explode(',', $currentIp)[0]);
        }

        // Generate a secure recovery key (format: xxxx-xxxx-xxxx-xxxx)
        $recoveryKey = implode('-', str_split(bin2hex(random_bytes(8)), 4));
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
            // Show a "check your email for the link" screen
            session(['verification_email' => $customer->email]);

            return redirect()->route('shop.customers.verify.code');
        }

        // Verification disabled — log in immediately
        auth()->guard('customer')->login($customer);
        Event::dispatch('customer.after.login', auth()->guard('customer')->user());
        session()->flash('recovery_key', $recoveryKey);
        session()->forget('pending_recovery_key');

        return redirect()->route('shop.customers.account.profile.edit');
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
        Event::dispatch('customer.after.login', auth()->guard()->user());

        session()->flash('success', 'Регистрация прошла успешно! Пожалуйста, заполните данные вашего профиля.');

        return redirect()->route('shop.customers.account.profile.edit');
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

        return redirect()->route('shop.customers.account.profile.edit');
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
