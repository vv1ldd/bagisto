<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Repositories\CustomerTrustedDeviceRepository;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Shop\Mail\Customer\VerifyIpNotification;

class VerifyIpController extends Controller
{
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected CustomerTrustedDeviceRepository $trustedDeviceRepository
    ) {
    }

    public function showVerifyCode()
    {
        $email = session('verification_email');

        if (!$email) {
            return redirect()->route('shop.customer.session.index');
        }

        return view('shop::customers.verify-code', [
            'email' => $email,
            'submitRoute' => route('shop.customers.verify_ip.code.submit'),
            'resendRoute' => route('shop.customers.verify_ip.resend')
        ]);
    }

    public function verifyByCode(Request $request)
    {
        $email = session('verification_email');

        if (!$email) {
            return redirect()->route('shop.customer.session.index');
        }

        $enteredCode = preg_replace('/\D/', '', $request->input('code', ''));

        if (strlen($enteredCode) !== 6) {
            return back()->withErrors(['code' => 'Введите 6-значный код из письма.']);
        }

        $storedCode = session('verification_code_pending')
            ?? Cache::get("ip_verification_code_{$email}");

        if (!$storedCode || $storedCode !== $enteredCode) {
            return back()->withErrors(['code' => 'Неверный код. Проверьте письмо и попробуйте снова.']);
        }

        return $this->processVerification($email);
    }

    public function verifyByLink($token)
    {
        $email = Cache::get("ip_verification_token_{$token}");

        if (!$email) {
            session()->flash('error', 'Ссылка устарела или недействительна.');
            return redirect()->route('shop.customer.session.index');
        }

        return $this->processVerification($email);
    }

    public function resendCode()
    {
        $email = session('verification_email');

        if (!$email) {
            return redirect()->route('shop.customer.session.index');
        }

        $customer = $this->customerRepository->findOneByField('email', $email);
        $currentIp = session('pending_ip') ?? request()->ip();

        if ($customer) {
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $token = session('verify_ip_token') ?? bin2hex(random_bytes(16));

            session([
                'verification_code_pending' => $verificationCode,
                'verify_ip_token' => $token,
            ]);

            Cache::put("ip_verification_code_{$email}", $verificationCode, now()->addHours(24));
            Cache::put("ip_verification_token_{$token}", $email, now()->addHours(24));

            Mail::queue(new VerifyIpNotification($customer, $currentIp, $verificationCode, $token));

            session()->flash('success', 'Новый код подтверждения успешно отправлен.');
        }

        return back();
    }

    protected function processVerification($email)
    {
        $customer = $this->customerRepository->findOneByField('email', $email);
        $pendingIp = session('pending_ip');

        if ($customer && $pendingIp) {
            $this->customerRepository->update([
                'last_login_ip' => $pendingIp,
            ], $customer->id);

            auth()->guard('customer')->login($customer);

            Event::dispatch('customer.after.login', $customer);

            session()->forget(['verification_email', 'verification_code_pending', 'pending_ip', 'verify_ip_token']);
            Cache::forget("ip_verification_code_{$email}");

            session()->flash('success', 'Устройство подтверждено. Вы успешно вошли в систему.');

            // ---- TRUSTED DEVICE LOGIC ----
            $cookieToken = Str::random(64);
            $this->trustedDeviceRepository->create([
                'customer_id' => $customer->id,
                'ip_address' => $pendingIp,
                'user_agent' => request()->userAgent(),
                'cookie_token' => $cookieToken,
                'last_used_at' => now(),
            ]);

            // Set cookie for 1 year
            Cookie::queue('device_trust_token', $cookieToken, 60 * 24 * 365);
            // ------------------------------

            if (core()->getConfigData('customer.settings.login_options.redirected_to_page') == 'account') {
                return redirect()->route('shop.customers.account.passkeys.index');
            }

            return redirect()->route('shop.home.index');
        }

        return redirect()->route('shop.customer.session.index');
    }
}
