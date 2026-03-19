<?php

namespace Webkul\MagicAI\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\MagicAI\Contracts\KnowledgeItem as KnowledgeItemContract;

class KnowledgeItem extends Model implements KnowledgeItemContract
{
    protected $table = 'ai_knowledge_items';

    protected $fillable = [
        'title',
        'content',
        'source',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the embeddings for the knowledge item.
     */
    public function embeddings(): HasMany
    {
        return $this->hasMany(EmbeddingProxy::modelClass(), 'ai_knowledge_item_id');
    }
}
