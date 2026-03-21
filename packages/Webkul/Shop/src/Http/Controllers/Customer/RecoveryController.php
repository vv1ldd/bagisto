<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Services\MnemonicService;
use Webkul\Shop\Http\Controllers\Controller;

class RecoveryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected MnemonicService $mnemonicService
    ) {
    }

    /**
     * Show the mnemonic recovery form.
     *
     * @return \Illuminate\View\View
     */
    public function showSeedForm()
    {
        return view('shop::customers.recover-seed');
    }

    /**
     * Recover account using the seed phrase.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recoverBySeed(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'words' => 'required|array|size:12',
        ]);

        $email = $request->input('email');
        $words = $request->input('words');
        
        // Clean words
        $cleanWords = array_map(function($word) {
            return strtolower(trim($word));
        }, $words);

        $mnemonic = implode(' ', $cleanWords);

        if (!$this->mnemonicService->isValidMnemonic($cleanWords)) {
            return back()->withErrors(['mnemonic' => 'Введенная фраза содержит некорректные слова.']);
        }

        // Try to authenticate. Since the mnemonic IS the initial password (bcrypt-ed).
        // If the user changed their password, this will fail.
        // But for "onboarding" users who only have passkeys, this is their password.
        if (Auth::guard('customer')->attempt(['email' => $email, 'password' => $mnemonic])) {
            $customer = Auth::guard('customer')->user();
            
            // Log activity
            app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($customer);
            
            session()->flash('success', 'Доступ восстановлен! Пожалуйста, настройте новый Passkey.');

            // Redirect to wallet setup or profile
            return redirect()->route('shop.customers.account.wallet.setup');
        }

        return back()->withErrors(['mnemonic' => 'Неверная секретная фраза или email.']);
    }
}
