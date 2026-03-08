<?php

namespace Webkul\Admin\DataGrids\MagicAI;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class KnowledgeBaseDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('ai_knowledge_items')
            ->select(
                'id',
                'title',
                'content',
                'source',
                'created_at'
            );

        $this->addFilter('id', 'id');
        $this->addFilter('title', 'title');
        $this->addFilter('source', 'source');

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
            'index' => 'id',
            'label' => trans('magic_ai::app.knowledge_base.index.datagrid.id'),
            'type' => 'integer',
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'title',
            'label' => trans('magic_ai::app.knowledge_base.index.datagrid.title'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'content',
            'label' => trans('magic_ai::app.knowledge_base.index.datagrid.content'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => false,
            'sortable' => false,
            'closure' => function ($row) {
                return substr(strip_tags($row->content), 0, 100) . '...';
            },
        ]);

        $this->addColumn([
            'index' => 'source',
            'label' => trans('admin::app.magic_ai.knowledge_base.index.datagrid.source'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('admin::app.magic_ai.knowledge_base.index.datagrid.created-at'),
            'type' => 'datetime',
            'filterable' => true,
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
            'icon' => 'icon-edit',
            'title' => trans('admin::app.magic_ai.knowledge_base.index.datagrid.edit'),
            'method' => 'GET',
            'url' => function ($row) {
                return route('admin.magic_ai.knowledge_base.edit', $row->id);
            },
        ]);

        $this->addAction([
            'icon' => 'icon-delete',
            'title' => trans('admin::app.magic_ai.knowledge_base.index.datagrid.delete'),
            'method' => 'DELETE',
            'url' => function ($row) {
                return route('admin.magic_ai.knowledge_base.delete', $row->id);
            },
        ]);
    }
}
