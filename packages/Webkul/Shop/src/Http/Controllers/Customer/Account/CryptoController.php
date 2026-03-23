<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Illuminate\Http\Request;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Models\CryptoAddress;
use Webkul\Customer\Services\BlockchainSyncService;

class CryptoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected BlockchainSyncService $syncService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $customer = auth()->guard('customer')->user();

        // Trigger on-demand deposit sync (rate-limited internally to 5 min per address)
        $this->syncService->syncCustomerDeposits($customer);

        $addresses = $customer->crypto_addresses;

        return view('shop::customers.account.crypto.index', compact('addresses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'network' => 'required|in:bitcoin,ethereum,ton,usdt_ton,dash',
            'address' => 'required|string',
            'alias' => 'nullable|string|max:255',
        ]);

        $customer = auth()->guard('customer')->user();

        // Check if address already exists for this network
        $exists = CryptoAddress::where('network', $request->network)
            ->where('address', $request->address)
            ->exists();

        if ($exists) {
            session()->flash('error', 'Этот адрес уже зарегистрирован в системе.');
            return redirect()->back();
        }

        $cryptoAddress = $customer->crypto_addresses()->create([
            'network' => $request->network,
            'address' => $request->address,
            'alias' => $request->alias,
        ]);

        // Trigger immediate sync
        $this->syncService->syncBalance($cryptoAddress);

        session()->flash('show_verify_id', $cryptoAddress->id);
        session()->flash('success', 'Крипто-адрес успешно добавлен и синхронизирован.');

        return redirect()->route('shop.customers.account.crypto.index');
    }

    /**
     * Update alias for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAlias(Request $request, $id)
    {
        $request->validate([
            'alias' => 'nullable|string|max:255',
        ]);

        $cryptoAddress = auth()->guard('customer')->user()->crypto_addresses()->findOrFail($id);

        $cryptoAddress->update([
            'alias' => $request->alias,
        ]);

        session()->flash('success', 'Название кошелька обновлено.');

        return redirect()->route('shop.customers.account.crypto.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cryptoAddress = auth()->guard('customer')->user()->crypto_addresses()->findOrFail($id);

        $cryptoAddress->delete();

        session()->flash('success', 'Крипто-адрес успешно удален.');

        return redirect()->route('shop.customers.account.crypto.index');
    }

    /**
     * Sync balance for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sync($id)
    {
        $cryptoAddress = auth()->guard('customer')->user()->crypto_addresses()->findOrFail($id);

        if ($this->syncService->syncBalance($cryptoAddress)) {
            session()->flash('success', 'Баланс успешно обновлен.');
        } else {
            session()->flash('error', 'Не удалось обновить баланс. Попробуйте позже.');
        }

        return redirect()->route('shop.customers.account.crypto.index');
    }

    /**
     * Verify ownership for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function verify($id)
    {
        $cryptoAddress = auth()->guard('customer')->user()->crypto_addresses()->findOrFail($id);

        if ($this->syncService->verifyOwnership($cryptoAddress)) {
            session()->flash('success', 'Адрес успешно верифицирован!');
        } else {
            session()->flash('error', 'Транзакция верификации не обнаружена. Пожалуйста, убедитесь, что вы отправили точную сумму.');
        }

        return redirect()->route('shop.customers.account.crypto.index');
    }

    /**
     * Upgrade old M- format credits_id to a new 0x EVM address format.
     *
     * @return \Illuminate\Http\Response
     */
    public function upgradeCreditsId()
    {
        $user = auth()->guard('customer')->user();

        if (str_starts_with($user->credits_id, '0x')) {
            session()->flash('error', 'Ваш ID уже в формате крипто-адреса.');
            return redirect()->back();
        }

        // Generate a random 0x address
        $newAddress = '0x' . bin2hex(random_bytes(20));

        // Delete the old Arbitrum address if we created it previously with the M- format
        $user->crypto_addresses()
             ->where('network', 'arbitrum_one')
             ->where('address', 'LIKE', 'M-%')
             ->delete();

        // Update customer credits_id
        $user->credits_id = $newAddress;
        $user->save();

        // Update or create crypto address for the new 0x address
        $user->crypto_addresses()->updateOrCreate(
            ['network' => 'arbitrum_one', 'address' => $newAddress],
            [
                'is_active' => true,
                'verified_at' => now(), // Trusted since we generated it
            ]
        );

        session()->flash('success', 'Ваш ID успешно обновлен до крипто-адреса.');

        return redirect()->route('shop.customers.account.credits.index');
    }

    /**
     * Show the wallet upgrade form for existing users.
     */
    public function showUpgradeWallet()
    {
        $customer = auth()->guard('customer')->user();
        
        // If they already have an encrypted key, redirect back.
        if ($customer->encrypted_private_key) {
            return redirect()->route('shop.customers.account.credits.index');
        }

        return view('shop::customers.account.crypto.upgrade-wallet');
    }

    /**
     * Process the wallet upgrade.
     */
    public function upgradeWallet(Request $request, \Webkul\Customer\Services\MnemonicService $mnemonicService)
    {
        $request->validate([
            'phrase' => 'required|string',
        ]);

        $customer = auth()->guard('customer')->user();

        if ($customer->encrypted_private_key) {
            return redirect()->route('shop.customers.account.credits.index');
        }

        // Parse Phrase
        $phraseStr = preg_replace('/\s+/', ' ', trim($request->input('phrase')));
        $words = explode(' ', $phraseStr);

        // Verify Hash matches
        $hash = $mnemonicService->hashMnemonic($words);
        if ($hash !== $customer->mnemonic_hash) {
            return back()->withErrors(['phrase' => 'Секретная фраза не совпадает с сохраненной для вашего аккаунта.']);
        }

        // Derive and encrypt private key
        $addressService = app(\Webkul\Customer\Services\BlockchainAddressService::class);
        $wData = $addressService->deriveEthereumWallet($words);

        if (!$wData || !isset($wData['private_key'])) {
            return back()->withErrors(['phrase' => 'Не удалось получить ключи от кошелька. Обратитесь в поддержку.']);
        }

        $customer->encrypted_private_key = \Illuminate\Support\Facades\Crypt::encryptString($wData['private_key']);
        $customer->save();

        session()->flash('success', 'Ваш Web3-кошелек успешно активирован для NFT!');

        return redirect()->route('shop.customers.account.credits.index');
    }
}
