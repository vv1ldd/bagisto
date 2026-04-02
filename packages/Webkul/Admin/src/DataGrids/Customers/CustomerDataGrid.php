<?php

namespace Webkul\Admin\DataGrids\Customers;

use Illuminate\Support\Facades\DB;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\DataGrid\DataGrid;

class CustomerDataGrid extends DataGrid
{
    /**
     * Primary column.
     *
     * @var string
     */
    protected $primaryColumn = 'customer_id';

    /**
     * Constructor.
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
                'customers.status',
                'customers.is_call_enabled',
                'customers.is_matrix_enabled',
                'customer_groups.name as group',
                // Placeholder to fix "undefined" labels in UI templates
                DB::raw('0 as order_count'),
                DB::raw('0 as address_count'),
                DB::raw('0 as revenue'),
                // Real counters using DB::raw subqueries for maximum compatibility
                DB::raw('(SELECT COUNT(*) FROM customer_transactions ct WHERE ct.customer_id = customers.id AND ct.type = \'nft_gift\') as nft_count'),
                DB::raw('(SELECT COUNT(*) FROM customer_crypto_transactions cct WHERE cct.customer_id = customers.id) as tx_count'),
                DB::raw('(SELECT COALESCE(SUM(cb.amount), 0) FROM customer_transactions cb WHERE cb.customer_id = customers.id AND cb.type = \'credit\') as volume')
            );

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
            'index'      => 'customer_id',
            'label'      => '#ID',
            'type'       => 'integer',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'nickname',
            'label'      => 'Никнейм',
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                return $row->nickname ?: '<span style="color:#aaa;font-style:italic">GUEST</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'wallet_address',
            'label'      => 'Arbitrum Адрес',
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'closure'    => function ($row) {
                if (!$row->wallet_address) return '—';
                $addr = $row->wallet_address;
                return '<code style="background:#f4f4f4;padding:2px 4px;border-radius:4px;font-size:11px;">' . substr($addr, 0, 8) . '...' . substr($addr, -6) . '</code>';
            },
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => 'Статус',
            'type'       => 'boolean',
            'filterable' => true,
            'filterable_options' => [
                ['label' => 'Активен', 'value' => 1],
                ['label' => 'Неактивен', 'value' => 0],
            ],
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'is_call_enabled',
            'label'      => 'Звонки',
            'type'       => 'boolean',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'is_matrix_enabled',
            'label'      => 'Матрикс',
            'type'       => 'boolean',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'nft_count',
            'label'      => 'NFT',
            'type'       => 'integer',
            'sortable'   => true,
            'closure'    => function ($row) {
                if ($row->nft_count > 0) {
                    return '<span style="background:#D6FF00;color:black;padding:2px 6px;border-radius:4px;font-weight:bold;font-size:11px;">' . $row->nft_count . ' NFT</span>';
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

        // These columns MUST exist with these indices to fix "undefined" in some UI templates
        $this->addColumn([
            'index'      => 'order_count',
            'label'      => 'Заказы',
            'type'       => 'integer',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index'      => 'address_count',
            'label'      => 'Адреса',
            'type'       => 'integer',
            'visibility' => false,
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
            'icon'   => 'icon-view',
            'title'  => trans('admin::app.customers.customers.index.datagrid.view'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.customers.customers.view', $row->customer_id);
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
                'title'  => trans('admin::app.customers.customers.index.datagrid.delete'),
                'method' => 'POST',
                'url'    => route('admin.customers.customers.mass_delete'),
            ]);
        }

        if (bouncer()->hasPermission('customers.customers.edit')) {
            $this->addMassAction([
                'title'   => trans('admin::app.customers.customers.index.datagrid.update-status'),
                'method'  => 'POST',
                'url'     => route('admin.customers.customers.mass_update'),
                'options' => [
                    ['label' => 'Активен',   'value' => 1],
                    ['label' => 'Неактивен', 'value' => 0],
                ],
            ]);
        }
    }
}
