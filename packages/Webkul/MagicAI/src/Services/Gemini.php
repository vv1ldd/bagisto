<?php

namespace Webkul\MagicAI\Services;

use GuzzleHttp\Client;

class Gemini
{
    /**
     * New service instance.
     */
    public function __construct(
        protected string $model,
        protected string $prompt,
        protected bool $stream,
        protected bool $raw,
        protected ?string $attachment = null,
        protected ?string $mimeType = null,
    ) {
    }

    /**
     * Send request to Gemini AI.
     */
    public function ask(): string
    {
        $httpClient = new Client;

        $apiKey = env('MAGIC_AI_API_KEY', config('magic_ai_settings.api_key'));

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$apiKey}";

        $parts = [
            ['text' => $this->prompt],
        ];

        if ($this->attachment && $this->mimeType) {
            $parts[] = [
                'inline_data' => [
                    'mime_type' => $this->mimeType,
                    'data' => $this->attachment,
                ],
            ];
        }

        $result = $httpClient->request('POST', $endpoint, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'contents' => [
                    ['parts' => $parts],
                ],
            ],
        ]);

        $result = json_decode($result->getBody()->getContents(), true);

        return $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }
}
