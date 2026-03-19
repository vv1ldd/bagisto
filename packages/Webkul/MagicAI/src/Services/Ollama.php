<?php

namespace Webkul\MagicAI\Services;

use GuzzleHttp\Client;

class Ollama
{
    /**
     * New service instance.
     */
    public function __construct(
        protected string $model,
        protected string $prompt,
        protected float $temperature,
        protected bool $stream,
        protected bool $raw,
        protected ?string $attachment = null,
        protected ?string $mimeType = null,
    ) {
    }

    /**
     * Set LLM prompt text.
     */
    public function ask(): string
    {
        $httpClient = new Client;

        $endpoint = rtrim(env('MAGIC_AI_API_DOMAIN', config('magic_ai_settings.api_domain') ?? ''), '/') . '/api/generate';

        $body = [
            'model' => $this->model,
            'prompt' => $this->prompt,
            'raw' => $this->raw,
            'stream' => $this->stream,
        ];

        if ($this->attachment) {
            $body['images'] = [$this->attachment];
        }

        $result = $httpClient->request('POST', $endpoint, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => $body,
        ]);

        $result = json_decode($result->getBody()->getContents(), true);

        return $result['response'];
    }
}
