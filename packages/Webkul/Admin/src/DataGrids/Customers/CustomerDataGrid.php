<?php

namespace Webkul\Admin\DataGrids\Customers;

use Illuminate\Support\Facades\DB;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\DataGrid\DataGrid;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Repositories\OrderRepository;

class CustomerDataGrid extends DataGrid
{
    /**
     * Index.
     *
     * @var string
     */
    protected $primaryColumn = 'customer_id';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected CustomerGroupRepository $customerGroupRepository) {}

    /**
     * Prepare query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('customers')
            ->leftJoin('customer_groups', 'customers.customer_group_id', '=', 'customer_groups.id')
            ->select(
                'customers.id as customer_id',
                'customers.username as nickname',
                'customers.credits_id as wallet_address',
                'customers.phone',
                'customers.status',
                'customers.is_suspended',
                'customer_groups.name as group',
                'customers.channel_id'
            )
            ->addSelect([
                // Subquery for NFT count (Type 'nft_gift' in customer_transactions)
                'nft_count' => DB::table('customer_transactions')
                    ->selectRaw('count(*)')
                    ->whereColumn('customer_id', 'customers.id')
                    ->where('type', 'nft_gift'),

                // Subquery for Crypto transactions count (from customer_crypto_transactions)
                'tx_count' => DB::table('customer_crypto_transactions')
                    ->selectRaw('count(*)')
                    ->whereColumn('customer_id', 'customers.id'),

                // Total Balance Volume
                'volume' => DB::table('customer_balances')
                    ->selectRaw('COALESCE(sum(balance), 0)')
                    ->whereColumn('customer_id', 'customers.id')
            ]);

        $this->addFilter('customer_id', 'customers.id');
        $this->addFilter('nickname', 'customers.username');
        $this->addFilter('wallet_address', 'customers.credits_id');
        $this->addFilter('group', 'customer_groups.name');
        $this->addFilter('status', 'customers.status');

        return $queryBuilder;
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index' => 'channel_id',
            'label' => trans('admin::app.customers.customers.index.datagrid.channel'),
            'type' => 'string',
            'filterable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => collect(core()->getAllChannels())
                ->map(fn ($channel) => ['label' => $channel->name, 'value' => $channel->id])
                ->values()
                ->toArray(),
            'sortable' => true,
            'visibility' => false,
        ]);

        $this->addColumn([
            'index'      => 'customer_id',
            'label'      => trans('admin::app.customers.customers.index.datagrid.id'),
            'type'       => 'integer',
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'nickname',
            'label'      => 'Никнейм',
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                return $row->nickname ?: '<span class="text-gray-400 italic">No Nickname</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'wallet_address',
            'label'      => 'Arbitrum Адрес',
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                if (!$row->wallet_address) return '---';
                return '<code class="bg-gray-100 px-2 py-1 rounded text-xs">' . substr($row->wallet_address, 0, 10) . '...' . substr($row->wallet_address, -6) . '</code>';
            },
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('admin::app.customers.customers.index.datagrid.status'),
            'type'       => 'boolean',
            'filterable' => true,
            'filterable_options' => [
                [
                    'label' => trans('admin::app.customers.customers.index.datagrid.active'),
                    'value' => 1,
                ],
                [
                    'label' => trans('admin::app.customers.customers.index.datagrid.inactive'),
                    'value' => 0,
                ],
            ],
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'nft_count',
            'label'      => 'NFT',
            'type'       => 'integer',
            'sortable'   => true,
            'closure'    => function ($row) {
                if ($row->nft_count > 0) {
                    return '<span class="bg-lime-400 text-black px-2 py-0.5 rounded font-bold text-xs">' . $row->nft_count . ' NFT</span>';
                }
                return '0';
            },
        ]);

        $this->addColumn([
            'index'      => 'tx_count',
            'label'      => 'Транзакции',
            'type'       => 'integer',
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'volume',
            'label'      => 'Баланс (MGF)',
            'type'       => 'integer',
            'sortable'   => true,
            'closure'    => function ($row) {
                return number_format($row->volume, 2) . ' MGF';
            },
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'icon' => 'icon-view',
            'title' => trans('admin::app.customers.customers.index.datagrid.view'),
            'method' => 'GET',
            'url' => function ($row) {
                return route('admin.customers.customers.view', $row->customer_id);
            },
        ]);

        $this->addAction([
            'icon' => 'icon-exit',
            'title' => trans('admin::app.customers.customers.index.datagrid.login-as-customer'),
            'method' => 'GET',
            'target' => 'blank',
            'url' => function ($row) {
                return route('admin.customers.customers.login_as_customer', $row->customer_id);
            },
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        if (bouncer()->hasPermission('customers.customers.delete')) {
            $this->addMassAction([
                'title' => trans('admin::app.customers.customers.index.datagrid.delete'),
                'method' => 'POST',
                'url' => route('admin.customers.customers.mass_delete'),
            ]);
        }

        if (bouncer()->hasPermission('customers.customers.edit')) {
            $this->addMassAction([
                'title' => trans('admin::app.customers.customers.index.datagrid.update-status'),
                'method' => 'POST',
                'url' => route('admin.customers.customers.mass_update'),
                'options' => [
                    [
                        'label' => trans('admin::app.customers.customers.index.datagrid.active'),
                        'value' => 1,
                    ],
                    [
                        'label' => trans('admin::app.customers.customers.index.datagrid.inactive'),
                        'value' => 0,
                    ],
                ],
            ]);
        }
    }
}
