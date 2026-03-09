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

        // Trigger on-demand deposit sync (rate-limited internally)
        $this->syncService->syncCustomerDeposits($customer);

        $verifiedAddresses = $customer
            ->crypto_addresses()
            ->whereNotNull('verified_at')
            ->orderBy('network')
            ->get();

        $allAddresses = $customer
            ->crypto_addresses()
            ->orderBy('network')
            ->get();

        $transactions = $customer
            ->credits()
            ->orderBy('id', 'desc')
            ->paginate(20);

        $organizations = $customer->organizations;

        return view('shop::customers.account.credits.index', compact('verifiedAddresses', 'allAddresses', 'transactions', 'organizations'));
    }

    /**
     * Redirect to unified index with transactions step.
     */
    public function transactions()
    {
        return redirect()->route('shop.customers.account.credits.index', ['step' => 'transactions']);
    }

    /**
     * Redirect to unified index with deposit step.
     */
    public function deposit()
    {
        return redirect()->route('shop.customers.account.credits.index', ['step' => 'deposit']);
    }
}
