<?php

namespace Webkul\MagicAI;

use Webkul\MagicAI\Services\Gemini;
use Webkul\MagicAI\Services\GroqAI;
use Webkul\MagicAI\Services\OpenAI;

class MagicAI
{
    /**
     * LLM model.
     */
    protected string $model;

    /**
     * LLM agent.
     */
    protected string $agent;

    /**
     * Stream Response.
     */
    protected bool $stream = false;

    /**
     * Raw Response.
     */
    protected bool $raw = true;

    /**
     * Raw Response.
     */
    protected float $temperature = 0.7;

    /**
     * LLM prompt text.
     */
    protected string $prompt;

    /**
     * Attachment (base64).
     */
    protected ?string $attachment = null;

    /**
     * Attachment Mime Type.
     */
    protected ?string $mimeType = null;

    /**
     * Set LLM model
     */
    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set LLM agent
     */
    public function setAgent(string $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Set stream response.
     */
    public function setStream(bool $stream): self
    {
        $this->stream = $stream;

        return $this;
    }

    /**
     * Set response raw.
     */
    public function setRaw(bool $raw): self
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * Set LLM prompt text.
     */
    public function setTemperature(float $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * Set LLM prompt text.
     */
    public function setPrompt(string $prompt): self
    {
        $this->prompt = $prompt;

        return $this;
    }

    /**
     * Set Attachment.
     */
    public function setAttachment(string $attachment, string $mimeType): self
    {
        $this->attachment = $attachment;

        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Set LLM prompt text with context from Knowledge Base.
     */
    public function withContext(): self
    {
        $context = $this->retrieveContext();

        if ($context) {
            $this->prompt = "Context information:\n" . $context . "\n\nUser Question: " . $this->prompt;
        }

        return $this;
    }

    /**
     * Retrieve relevant context from Knowledge Base.
     */
    protected function retrieveContext(): string
    {
        $repository = app(\Webkul\MagicAI\Repositories\KnowledgeItemRepository::class);
        $items = $repository->findRelevant($this->prompt);

        $context = "";
        foreach ($items as $item) {
            $context .= $item->content . "\n---\n";
        }

        return trim($context);
    }

    /**
     * Set LLM prompt text.
     */
    public function ask(): string
    {
        return $this->getModelInstance()->ask();
    }

    /**
     * Generate Images
     */
    public function images(array $options): array
    {
        return $this->getModelInstance()->images($options);
    }

    /**
     * Get LLM model instance.
     */
    public function getModelInstance(): OpenAI|Gemini|GroqAI
    {
        if (in_array($this->model, ['gpt-4-turbo', 'gpt-4o', 'gpt-4o-mini', 'dall-e-2', 'dall-e-3'])) {
            return new OpenAI(
                $this->model,
                $this->prompt,
                $this->temperature,
                $this->stream,
                $this->attachment,
                $this->mimeType,
            );
        }

        if (in_array($this->model, ['llama3-8b-8192'])) {
            return new GroqAI(
                $this->model,
                $this->prompt,
                $this->temperature,
                $this->stream,
            );
        }

        // Default to Gemini if no other model explicitly matched
        return new Gemini(
            $this->model,
            $this->prompt,
            $this->stream,
            $this->raw,
            $this->attachment,
            $this->mimeType,
        );
    }
}
