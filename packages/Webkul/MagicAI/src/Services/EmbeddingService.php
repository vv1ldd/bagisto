<?php

namespace Webkul\MagicAI\Services;

use GuzzleHttp\Client;

class EmbeddingService
{
    /**
     * Get embedding for a given text.
     */
    public function getEmbedding(string $text, string $model = null): array
    {
        $model = $model ?: config('magic_ai_settings.knowledge_base.embedding_model') ?: 'text-embedding-3-small';

        return $this->getOpenAIEmbedding($text, $model);
    }

    /**
     * Get embedding from OpenAI.
     */
    protected function getOpenAIEmbedding(string $text, string $model): array
    {
        // Placeholder for OpenAI embeddings if needed
        // Bagisto might already have an OpenAI facade or client
        return [];
    }

    /**
     * Calculate cosine similarity between two vectors.
     */
    public function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        foreach ($vec1 as $i => $val) {
            $dotProduct += $val * ($vec2[$i] ?? 0);
            $normA += $val * $val;
            $normB += ($vec2[$i] ?? 0) * ($vec2[$i] ?? 0);
        }

        if ($normA == 0 || $normB == 0) {
            return 0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
