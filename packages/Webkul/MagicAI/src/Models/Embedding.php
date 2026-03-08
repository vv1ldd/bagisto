<?php

namespace Webkul\MagicAI\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\MagicAI\Contracts\Embedding as EmbeddingContract;

class Embedding extends Model implements EmbeddingContract
{
    protected $table = 'ai_embeddings';

    protected $fillable = [
        'ai_knowledge_item_id',
        'embedding',
        'model',
    ];

    /**
     * Get the knowledge item that owns the embedding.
     */
    public function knowledgeItem(): BelongsTo
    {
        return $this->belongsTo(KnowledgeItemProxy::modelClass(), 'ai_knowledge_item_id');
    }
}
