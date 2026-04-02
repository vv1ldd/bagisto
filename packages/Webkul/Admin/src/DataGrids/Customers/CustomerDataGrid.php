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
                'customer_groups.name as group',
                DB::raw('0 as nft_count'),
                DB::raw('0 as tx_count'),
                DB::raw('0 as volume')
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
                return $row->nickname ?: '<span style="color:#aaa;font-style:italic">—</span>';
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
                return substr($addr, 0, 8) . '...' . substr($addr, -6);
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
            'index'   => 'nft_count',
            'label'   => 'NFT',
            'type'    => 'integer',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'   => 'tx_count',
            'label'   => 'Транзакции',
            'type'    => 'integer',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'   => 'volume',
            'label'   => 'Баланс',
            'type'    => 'integer',
            'sortable' => true,
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

        $this->addAction([
            'icon'   => 'icon-exit',
            'title'  => trans('admin::app.customers.customers.index.datagrid.login-as-customer'),
            'method' => 'GET',
            'target' => 'blank',
            'url'    => function ($row) {
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
