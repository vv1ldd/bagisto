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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recoverBySeed(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'words' => 'required|array|min:12|max:24',
        ]);

        $email = $request->input('email');
        $words = array_filter($request->input('words', [])); // Remove empty slots
        
        $validLengths = [12, 15, 18, 21, 24];
        if (! in_array(count($words), $validLengths)) {
            session()->flash('error', 'Секретная фраза должна содержать 12, 15, 18, 21 или 24 слова.');
            return redirect()->back()->withInput();
        }

        // Clean words
        $cleanWords = array_map(function($word) {
            return strtolower(trim($word));
        }, $words);

        if (!$this->mnemonicService->isValidMnemonic($cleanWords)) {
            session()->flash('error', 'Ошибка в словах или порядке слов. Пожалуйста, проверьте контрольную сумму.');
            return redirect()->back()->withInput();
        }

        $mnemonic = implode(' ', $cleanWords);

        // Try to authenticate. Since the mnemonic IS the initial password (bcrypt-ed).
        if (Auth::guard('customer')->attempt(['email' => $email, 'password' => $mnemonic])) {
            $customer = Auth::guard('customer')->user();
            
            // Log activity if repository exists
            if (class_exists(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)) {
                app(\Webkul\Customer\Repositories\CustomerLoginLogRepository::class)->log($customer);
            }
            
            session()->flash('success', 'Доступ восстановлен! Пожалуйста, настройте новый Passkey.');

            // Redirect to profile
            return redirect()->route('shop.customers.account.profile.index');
        }

        session()->flash('error', 'Неверная секретная фраза или email.');
        return redirect()->back()->withInput();
    }
}
