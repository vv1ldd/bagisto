<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Services\BlockchainSyncService;

class CreditController extends Controller
{
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

        $transactions = $customer
            ->credits()
            ->orderBy('id', 'desc')
            ->paginate(10);

        $addresses = $customer->crypto_addresses()->orderBy('created_at', 'asc')->get();

        return view('shop::customers.account.credits.index', compact('transactions', 'addresses'));
    }

    /**
     * Dedicated deposit page.
     *
     * @return \Illuminate\View\View
     */
    public function deposit()
    {
        $customer = auth()->guard('customer')->user();

        $verifiedAddresses = $customer
            ->crypto_addresses()
            ->where('verified', true)
            ->orderBy('network')
            ->get();

        return view('shop::customers.account.credits.deposit', compact('verifiedAddresses'));
    }
}
