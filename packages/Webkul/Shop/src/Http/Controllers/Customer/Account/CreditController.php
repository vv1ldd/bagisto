<?php

namespace Webkul\Shop\Http\Controllers\Customer\Account;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Customer\Services\BlockchainSyncService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CreditController extends Controller
{
    public function __construct(
        protected BlockchainSyncService $syncService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        \Illuminate\Support\Facades\Log::debug('CreditController: Entering index.', [
            'customer_id' => auth()->guard('customer')->id(),
            'session_id' => session()->getId(),
        ]);

        $customer = auth()->guard('customer')->user();

        // Trigger on-demand deposit sync (rate-limited internally)
        $this->syncService->syncCustomerDeposits($customer);

        // Trigger on-demand Web3 transaction sync (checks hashes like registration/cashback)
        $this->syncService->syncPendingWeb3Transactions($customer);

        $allAddresses = $customer
            ->crypto_addresses()
            ->orderBy('network')
            ->get();

        // Combine Credits (Transactions) and Orders
        $credits = $customer->credits()->get()->map(function ($item) {
            $item->merged_type = 'transaction';
            return $item;
        });

        $orders = $customer->orders()->get()->map(function ($item) {
            $item->merged_type = 'order';
            return $item;
        });

        $mergedCollection = $credits->concat($orders)->sortByDesc('created_at');

        // Manual Pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentItems = $mergedCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $transactions = new LengthAwarePaginator($currentItems, $mergedCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);



        $allAssets = [
            'bitcoin' => ['icon' => '₿'],
            'ethereum' => ['icon' => 'Ξ'],
            'ton' => ['icon' => '💎'],
            'usdt_ton' => ['icon' => '₮'],
            'dash' => ['icon' => 'Đ']
        ];

        // Fetch successful orders for NFT display
        $nftOrders = $customer->orders()
            ->whereIn('status', ['processing', 'completed', 'closed'])
            ->orderBy('id', 'desc')
            ->get();

        return view('shop::customers.account.credits.index', compact('allAddresses', 'transactions', 'allAssets', 'nftOrders'));
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
