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
        $model = $model ?: core()->getConfigData('general.magic_ai.settings.embedding_model') ?: 'nomic-embed-text';
        $apiDomain = core()->getConfigData('general.magic_ai.settings.api_domain');

        if (str_contains($model, 'text-embedding')) {
            return $this->getOpenAIEmbedding($text, $model);
        }

        return $this->getOllamaEmbedding($text, $model, $apiDomain);
    }

    /**
     * Get embedding from Ollama.
     */
    protected function getOllamaEmbedding(string $text, string $model, string $apiDomain): array
    {
        $httpClient = new Client();

        $endpoint = rtrim($apiDomain, '/') . '/api/embeddings';

        $response = $httpClient->request('POST', $endpoint, [
            'json' => [
                'model' => $model,
                'prompt' => $text,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['embedding'] ?? [];
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
