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
        $user = auth()->guard('customer')->user();
        $isInvestor = (bool) $user->is_investor;

        if ($isInvestor) {
            $this->syncService->syncCustomerDeposits($user);
            $this->syncService->syncPendingWeb3Transactions($user);
        }

        $allAddresses = $isInvestor 
            ? $user->crypto_addresses()->orderBy('network')->get() 
            : collect([]);

        // Formatted balances for investor grid
        $balances = $isInvestor 
            ? $user->balances->keyBy('currency_code')->map(fn($b) => (float)$b->balance)
            : collect([]);

        // [DECENTRALIZATION] Fetch real blockchain balance for Meanly Coin (MC)
        // This ensures the dashboard always reflects what is on-chain.
        $arbitrumAddress = $user->crypto_addresses()->where('network', 'arbitrum_one')->first();
        if ($arbitrumAddress) {
            try {
                // Synchronize the ERC20 balance from Arbiscan
                $this->syncService->syncMeanlyCoin($arbitrumAddress);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Blockchain Sync Failed for {$user->id}: " . $e->getMessage());
            }
        }

        // Get the updated MC balance from customer_balances table
        $meanlyCoinBalance = (float) ($user->balances()->where('currency_code', 'meanly_coin')->first()?->amount ?? 0);

        // Transactions & Orders combined
        $credits = $user->credits()
            ->get()
            ->filter(function ($item) {
                // Hide technical SBP minting/withdrawals as they are redundant with bank apps
                $notes = $item->notes ?: '';
                if ($item->type === 'deposit' && (mb_stripos($notes, 'минтинг') !== false || mb_stripos($notes, 'сбп') !== false)) {
                    return false;
                }

                // Hide order payments from credits to avoid duplication with the orders list
                if (in_array($item->type, ['order_payment', 'purchase'])) {
                    return false;
                }

                return true;
            })
            ->map(function ($item) {
                $description = $item->notes ?: 'Транзакция';

                if ($item->type === 'registration_minting') {
                    $description = 'Приветственные бонусы';
                } elseif ($item->type === 'cashback') {
                    preg_match('/#(\d+)/', $item->notes, $matches);
                    $orderId = $matches[1] ?? '';
                    $description = 'Кэшбэк за покупку' . ($orderId ? ' #' . $orderId : '');
                } elseif ($item->type === 'order_refund') {
                    $description = 'Возврат за заказ';
                }

                return [
                    'id' => 'tx-' . $item->id,
                    'type' => 'credit',
                    'amount' => (float)$item->amount,
                    'currency' => 'MC',
                    'status' => $item->status,
                    'created_at' => $item->created_at->toIso8601String(),
                    'formatted_date' => $item->created_at->format('d M Y, H:i'),
                    'description' => $description,
                ];
            });

        $orders = $user->orders()->get()->map(function ($item) {
            return [
                'id' => 'ord-' . $item->id,
                'increment_id' => $item->increment_id,
                'type' => 'order',
                'amount' => (float)$item->grand_total,
                'currency' => $item->order_currency_code,
                'status' => $item->status,
                'created_at' => $item->created_at->toIso8601String(),
                'formatted_date' => $item->created_at->format('d M Y, H:i'),
                'description' => 'Оплата заказа #' . $item->increment_id,
            ];
        });

        $transactions = $credits->concat($orders)->sortByDesc('created_at')->values();

        $allAssets = [
            'bitcoin' => ['icon' => '₿', 'name' => 'Bitcoin'],
            'ethereum' => ['icon' => 'Ξ', 'name' => 'Ethereum'],
            'ton' => ['icon' => '💎', 'name' => 'TON'],
            'usdt_ton' => ['icon' => '₮', 'name' => 'USDT (TON)'],
            'dash' => ['icon' => 'Đ', 'name' => 'Dash']
        ];

        $nftOrders = $user->orders()
            ->whereIn('status', ['processing', 'completed', 'closed'])
            ->with(['items.product'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn($o) => [
                'id' => $o->id,
                'increment_id' => $o->increment_id,
                'status' => $o->status,
                'image' => $o->items->first()?->product?->base_image_url,
                'date' => $o->created_at->format('d M Y'),
                'is_verified' => true,
            ]);

        $walletData = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'credits_id' => $user->credits_id ?? '',
                'credits_alias' => $user->credits_alias ?? '',
                'is_investor' => $isInvestor,
                'meanly_coin_balance' => $meanlyCoinBalance,
                'pending_activation' => ($user->credits_id && str_starts_with($user->credits_id, 'M-')) || 
                                       ($user->credits_id && str_starts_with($user->credits_id, '0x') && is_null($user->mnemonic_verified_at)),
            ],
            'balances' => $balances,
            'addresses' => $allAddresses,
            'transactions' => $transactions,
            'assets_config' => $allAssets,
            'nfts' => $nftOrders,
            'current_step' => request('step', 'dashboard'),
        ];

        return view('shop::customers.account.credits.index', compact('walletData'));
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
