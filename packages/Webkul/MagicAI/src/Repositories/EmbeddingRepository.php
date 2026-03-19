<?php

namespace Webkul\MagicAI\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\MagicAI\Contracts\Embedding;

class EmbeddingRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return Embedding::class;
    }
}
