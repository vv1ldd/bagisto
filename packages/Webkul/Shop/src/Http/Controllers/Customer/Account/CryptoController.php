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
        $addresses = auth()->guard('customer')->user()->crypto_addresses;

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
        ]);

        // Trigger immediate sync
        $this->syncService->syncBalance($cryptoAddress);

        session()->flash('success', 'Крипто-адрес успешно добавлен и синхронизирован.');

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
}
