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

        $user = auth()->guard('customer')->user();
        $isInvestor = (bool) $user->is_investor;

        // Trigger syncs only if relevant
        if ($isInvestor) {
            $this->syncService->syncCustomerDeposits($user);
            $this->syncService->syncPendingWeb3Transactions($user);
        }

        $allAddresses = $isInvestor 
            ? $user->crypto_addresses()->orderBy('network')->get() 
            : collect([]);

        $balances = $isInvestor 
            ? $user->balances 
            : collect([]);

        // Combine Credits (Transactions) and Orders for everyone
        $credits = $user->credits()->get()->map(function ($item) {
            $item->merged_type = 'transaction';
            return $item;
        });

        $orders = $user->orders()->get()->map(function ($item) {
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
        $nftOrders = $user->orders()
            ->whereIn('status', ['processing', 'completed', 'closed'])
            ->orderBy('id', 'desc')
            ->get();

        return view('shop::customers.account.credits.index', compact('allAddresses', 'transactions', 'allAssets', 'nftOrders', 'balances'));
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
