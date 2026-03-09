<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class BillingEntityDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('billing_entities')
            ->select('id', 'name', 'inn', 'bank_name', 'bic', 'is_default')
            ->orderBy('is_default', 'desc')
            ->orderBy('id', 'desc');

        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('admin::app.settings.billing-entities.index.datagrid.id'),
            'type' => 'integer',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'name',
            'label' => trans('admin::app.settings.billing-entities.index.datagrid.name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'inn',
            'label' => trans('admin::app.settings.billing-entities.index.datagrid.inn'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'bank_name',
            'label' => trans('admin::app.settings.billing-entities.index.datagrid.bank-name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'is_default',
            'label' => trans('admin::app.settings.billing-entities.index.datagrid.is-default'),
            'type' => 'boolean',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                if ($row->is_default) {
                    return '<span class="label-active">' . trans('admin::app.settings.billing-entities.index.datagrid.active') . '</span>';
                }

                return '<span class="label-info">' . trans('admin::app.settings.billing-entities.index.datagrid.inactive') . '</span>';
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
            'icon' => 'icon-edit',
            'title' => trans('admin::app.settings.billing-entities.index.datagrid.edit'),
            'method' => 'GET',
            'url' => function ($row) {
                return route('admin.settings.billing_entities.edit', $row->id);
            },
        ]);

        $this->addAction([
            'icon' => 'icon-settings',
            'title' => trans('admin::app.settings.billing-entities.index.datagrid.set-default'),
            'method' => 'POST',
            'action' => function ($row) {
                return route('admin.settings.billing_entities.default', $row->id);
            },
            'url' => function ($row) {
                return route('admin.settings.billing_entities.default', $row->id);
            },
        ]);

        $this->addAction([
            'icon' => 'icon-delete',
            'title' => trans('admin::app.settings.billing-entities.index.datagrid.delete'),
            'method' => 'DELETE',
            'url' => function ($row) {
                return route('admin.settings.billing_entities.delete', $row->id);
            },
        ]);
    }
}
