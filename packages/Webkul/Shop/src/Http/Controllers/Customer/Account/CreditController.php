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

        return view('shop::customers.account.credits.index', compact('transactions'));
    }
}
