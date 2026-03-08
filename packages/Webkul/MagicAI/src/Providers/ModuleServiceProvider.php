<?php

namespace Webkul\MagicAI\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        \Webkul\MagicAI\Models\KnowledgeItem::class,
        \Webkul\MagicAI\Models\Embedding::class,
    ];
}
