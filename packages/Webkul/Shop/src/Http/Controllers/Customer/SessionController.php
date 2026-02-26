<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Webkul\Shop\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Webkul\Shop\Http\Requests\Customer\LoginRequest;
use Webkul\Shop\Mail\Customer\LoginLinkNotification;
use Webkul\Customer\Repositories\CustomerRepository;

class SessionController extends Controller
{
    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        if (auth()->guard('customer')->check()) {
            return redirect()->route('shop.home.index');
        }

        return view('shop::customers.sign-in');
    }

    /**
     * Send magic login link to customer.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendLoginEmail()
    {
        request()->validate([
            'email' => 'required|email|exists:customers,email',
        ], [
            'email.exists' => 'Пользователь с такой почтой не найден.',
        ]);

        $email = request()->get('email');
        $customerRepository = app(CustomerRepository::class);
        $customer = $customerRepository->findOneByField('email', $email);

        $token = md5(uniqid(rand(), true));
        $customerRepository->update(['token' => $token], $customer->id);

        // Refresh customer model with token
        $customer = $customerRepository->find($customer->id);

        Mail::queue(new LoginLinkNotification($customer));

        session()->flash('success', 'Ссылка для входа отправлена на вашу почту.');

        return redirect()->back();
    }

    /**
     * Login customer using magic link token.
     *
     * @param  string  $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginByLink($token)
    {
        $customerRepository = app(CustomerRepository::class);
        $customer = $customerRepository->findOneByField('token', $token);

        if (!$customer) {
            session()->flash('info', 'Ссылка для входа недействительна или уже использована.');
            return redirect()->route('shop.customer.session.index');
        }

        // Store customer ID and partial info in session for verification
        session()->put('pending_login_customer_id', $customer->id);
        session()->put('pending_login_token', $token);

        return redirect()->route('shop.customer.login.verify_identity');
    }

    /**
     * Show identity verification form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showVerifyIdentity()
    {
        if (!session()->has('pending_login_customer_id')) {
            return redirect()->route('shop.customer.session.index');
        }

        return view('shop::customers.verify-identity');
    }

    /**
     * Verify identity details and login.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyIdentity()
    {
        if (!session()->has('pending_login_customer_id')) {
            return redirect()->route('shop.customer.session.index');
        }

        $id = session()->get('pending_login_customer_id');
        $customerRepository = app(CustomerRepository::class);
        $customer = $customerRepository->find($id);

        request()->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|string',
            'date_of_birth' => 'required|string',
            'birth_city' => 'required|string',
        ], [], [
            'first_name' => trans('shop::app.customers.signup.first-name'),
            'last_name' => trans('shop::app.customers.signup.last-name'),
            'gender' => trans('shop::app.customers.account.profile.edit.gender'),
            'date_of_birth' => trans('shop::app.customers.account.profile.edit.dob'),
            'birth_city' => trans('shop::app.customers.account.profile.edit.birth-city'),
        ]);

        $data = request()->only(['first_name', 'last_name', 'gender', 'date_of_birth', 'birth_city']);

        // Normalize date format to YYYY-MM-DD
        try {
            $data['date_of_birth'] = Carbon::parse($data['date_of_birth'])->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('[IdentityVerification] Invalid date format provided', ['dob' => $data['date_of_birth']]);
        }

        // Diagnostic logging (PII-compliant)
        Log::info('[IdentityVerification] Attempt matching for customer ID ' . $customer->id, [
            'db_gender_set' => !empty($customer->gender),
            'db_dob_set' => !empty($customer->date_of_birth),
            'db_city_set' => !empty($customer->birth_city),
        ]);

        // Determine if this is an "unclaimed" identity (newly registered user)
        // We check if the hashed fields are actually empty.
        $isUnclaimed = empty($customer->gender)
            && empty($customer->date_of_birth)
            && empty($customer->birth_city);

        if (!$isUnclaimed) {
            Log::info('[IdentityVerification] User is NOT unclaimed', [
                'id' => $customer->id,
                'g' => $customer->gender ? 'SET' : 'EMPTY',
                'd' => $customer->date_of_birth ? 'SET' : 'EMPTY',
                'c' => $customer->birth_city ? 'SET' : 'EMPTY',
            ]);
        }

        if ($isUnclaimed) {
            Log::info('[IdentityVerification] First-time identity claim for customer ID ' . $customer->id);

            // First time verification: Save the provided details as the new identity
            $updateData = [
                'first_name' => trim($data['first_name']),
                'last_name' => trim($data['last_name']),
                'gender' => $data['gender'],
                'date_of_birth' => $data['date_of_birth'],
                'birth_city' => trim($data['birth_city']),
            ];

            $customerRepository->update($updateData, $customer->id);

            // Refresh customer model
            $customer = $customerRepository->find($customer->id);
        } else {
            // 1. Check exact matches for names (standard approach)
            if (
                mb_strtolower(trim($customer->first_name)) !== mb_strtolower(trim($data['first_name'])) ||
                mb_strtolower(trim($customer->last_name)) !== mb_strtolower(trim($data['last_name']))
            ) {
                Log::warning('[IdentityVerification] Name mismatch', [
                    'db' => ['fn' => $customer->first_name, 'ln' => $customer->last_name],
                    'input' => ['fn' => $data['first_name'], 'ln' => $data['last_name']]
                ]);
                return redirect()->back()->withErrors(['verification' => 'Введенные данные не совпадают с нашими записями. [N]'])->withInput();
            }

            // 2. Check fields (handle both plain text and hashed for backward compatibility)
            $genderMatch = ($data['gender'] === $customer->gender) || Hash::check($data['gender'], $customer->gender);
            $dobMatch = ($data['date_of_birth'] === $customer->date_of_birth) || Hash::check($data['date_of_birth'], $customer->date_of_birth);

            $inputCityNorm = mb_strtolower(trim($data['birth_city']));
            $dbCityNorm = mb_strtolower(trim($customer->birth_city));
            $cityMatch = ($inputCityNorm === $dbCityNorm) || Hash::check($inputCityNorm, $customer->birth_city);

            $errors = [];
            if (!$genderMatch)
                $errors[] = '[G]';
            if (!$dobMatch)
                $errors[] = '[D]';
            if (!$cityMatch)
                $errors[] = '[C]';

            if (!empty($errors)) {
                $isTruncated = (strlen($customer->gender) < 60) || (strlen($customer->date_of_birth) < 10); // bcrypt hashes are 60 chars

                Log::warning('[IdentityVerification] Hash mismatch details', [
                    'customer_id' => $customer->id,
                    'gender_match' => $genderMatch,
                    'dob_match' => $dobMatch,
                    'city_match' => $cityMatch,
                    'input_dob' => $data['date_of_birth'],
                    'is_truncated' => $isTruncated,
                ]);

                $msg = 'Введенные данные не совпадают. Проверьте правильность заполнения. ' . implode(' ', $errors);

                if ($isTruncated) {
                    $msg .= ' [HASH_CORRUPTED] Пожалуйста, свяжитесь с поддержкой для сброса данных.';
                }

                return redirect()->back()->withErrors(['verification' => $msg])->withInput();
            }
        }

        // Everything matched!
        // Clear token and session
        $customerRepository->update(['token' => null], $customer->id);
        session()->forget(['pending_login_customer_id', 'pending_login_token']);

        auth()->guard('customer')->login($customer);

        $currentIp = request()->header('CF-Connecting-IP')
            ?? request()->header('X-Forwarded-For')
            ?? request()->header('X-Real-IP')
            ?? request()->ip();

        if (str_contains($currentIp, ',')) {
            $currentIp = trim(explode(',', $currentIp)[0]);
        }

        if (!$customer->first_login_ip) {
            $customerRepository->update([
                'first_login_ip' => $currentIp,
                'last_login_ip' => $currentIp,
            ], $customer->id);
        } else {
            $customerRepository->update(['last_login_ip' => $currentIp], $customer->id);
        }

        Event::dispatch('customer.after.login', $customer);

        session()->flash('success', 'Личность подтверждена. С возвращением!');

        return redirect()->route('shop.customers.account.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $loginRequest)
    {
        if (!auth()->guard('customer')->attempt($loginRequest->only(['email', 'password']))) {
            session()->flash('error', trans('shop::app.customers.login-form.invalid-credentials'));

            return redirect()->back();
        }

        session()->forget('logged_in_via_passkey');

        if (!auth()->guard('customer')->user()->status) {
            auth()->guard('customer')->logout();

            session()->flash('warning', trans('shop::app.customers.login-form.not-activated'));

            return redirect()->back();
        }

        if (!auth()->guard('customer')->user()->is_verified) {
            session()->flash('info', trans('shop::app.customers.login-form.verify-first'));

            Cookie::queue(Cookie::make('enable-resend', 'true', 1));

            Cookie::queue(Cookie::make('email-for-resend', $loginRequest->get('email'), 1));

            auth()->guard('customer')->logout();

            return redirect()->back();
        }

        $customer = auth()->guard('customer')->user();

        $currentIp = request()->header('CF-Connecting-IP')
            ?? request()->header('X-Forwarded-For')
            ?? request()->header('X-Real-IP')
            ?? request()->ip();

        // If X-Forwarded-For has a comma-separated list, grab the first one (the original client)
        if (str_contains($currentIp, ',')) {
            $currentIp = trim(explode(',', $currentIp)[0]);
        }


        if (!$customer->first_login_ip) {
            app(\Webkul\Customer\Repositories\CustomerRepository::class)->update([
                'first_login_ip' => $currentIp,
                'last_login_ip' => $currentIp,
            ], $customer->id);
        } else {
            app(\Webkul\Customer\Repositories\CustomerRepository::class)->update(['last_login_ip' => $currentIp], $customer->id);
        }

        /**
         * Event passed to prepare cart after login.
         */
        Event::dispatch('customer.after.login', auth()->guard()->user());

        if (core()->getConfigData('customer.settings.login_options.redirected_to_page') == 'account') {
            return redirect()->route('shop.customers.account.passkeys.index');
        }

        return redirect()->route('shop.home.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $id = auth()->guard('customer')->user()->id;

        auth()->guard('customer')->logout();

        Event::dispatch('customer.after.logout', $id);

        return redirect()->route('shop.home.index');
    }
}
