<?php

namespace Webkul\MagicAI\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\MagicAI\Contracts\KnowledgeItem;
use Webkul\MagicAI\Services\EmbeddingService;

class KnowledgeItemRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return KnowledgeItem::class;
    }

    /**
     * Find relevant knowledge items based on a prompt.
     */
    public function findRelevant(string $prompt, int $limit = 3): array
    {
        $embeddingService = app(EmbeddingService::class);
        $promptVector = $embeddingService->getEmbedding($prompt);

        if (empty($promptVector)) {
            return [];
        }

        // Get all embeddings for the current model
        $model = core()->getConfigData('general.magic_ai.settings.embedding_model') ?: 'nomic-embed-text';
        $embeddings = app(EmbeddingRepository::class)->findWhere(['model' => $model]);

        $results = [];
        foreach ($embeddings as $embedding) {
            $vector = json_decode($embedding->embedding, true);
            $similarity = $embeddingService->cosineSimilarity($promptVector, $vector);

            $results[] = [
                'item_id' => $embedding->ai_knowledge_item_id,
                'similarity' => $similarity,
            ];
        }

        // Sort by similarity descending
        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        // Take top N
        $topItems = array_slice($results, 0, $limit);

        $items = [];
        foreach ($topItems as $result) {
            if ($result['similarity'] > 0.5) { // Similarity threshold
                $items[] = $this->find($result['item_id']);
            }
        }

        return $items;
    }
}
