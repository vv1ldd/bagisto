<?php

namespace Webkul\MagicAI\Services;

use OpenAI\Laravel\Facades\OpenAI as BaseOpenAI;

class OpenAI
{
    /**
     * New service instance.
     */
    public function __construct(
        protected string $model,
        protected string $prompt,
        protected float $temperature,
        protected bool $stream = false,
        protected ?string $attachment = null,
        protected ?string $mimeType = null,
    ) {
        $this->setConfig();
    }

    /**
     * Sets OpenAI credentials.
     */
    public function setConfig(): void
    {
        config([
            'openai.api_key' => env('MAGIC_AI_API_KEY', config('magic_ai_settings.api_key')),
            'openai.organization' => env('MAGIC_AI_ORGANIZATION', config('magic_ai_settings.organization')),
        ]);
    }

    /**
     * Set LLM prompt text.
     */
    public function ask(): string
    {
        $content = [
            [
                'type' => 'text',
                'text' => $this->prompt,
            ],
        ];

        if ($this->attachment && $this->mimeType) {
            $content[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => "data:{$this->mimeType};base64,{$this->attachment}",
                ],
            ];
        }

        $result = BaseOpenAI::chat()->create([
            'model' => $this->model,
            'temperature' => $this->temperature,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ],
        ]);

        return $result->choices[0]->message->content;
    }

    /**
     * Generate image.
     */
    public function images(array $options): array
    {
        $result = BaseOpenAI::images()->create([
            'model' => $this->model,
            'prompt' => $this->prompt,
            'n' => intval($options['n'] ?? 1),
            'size' => $options['size'],
            'quality' => $options['quality'] ?? 'standard',
            'response_format' => 'b64_json',
        ]);

        $images = [];

        foreach ($result->data as $image) {
            $images[]['url'] = 'data:image/png;base64,' . $image->b64_json;
        }

        return $images;
    }
}
