<?php

return [
    [
        'key' => 'general.magic_ai',
        'name' => 'magic_ai::app.knowledge_base.title',
        'info' => 'magic_ai::app.knowledge_base.title',
        'icon' => 'settings/magic-ai.svg',
        'sort' => 1,
    ],
    [
        'key' => 'general.magic_ai.knowledge_base',
        'name' => 'magic_ai::app.knowledge_base.title',
        'info' => 'magic_ai::app.knowledge_base.title',
        'sort' => 6,
        'fields' => [
            [
                'name' => 'embedding_model',
                'title' => 'Embedding Model',
                'type' => 'text',
                'info' => 'Model name for embeddings, e.g., nomic-embed-text (default).',
                'validation' => 'required',
                'channel_based' => true,
            ],
            [
                'name' => 'ollama_base_url',
                'title' => 'Ollama Base URL',
                'type' => 'text',
                'info' => 'Internal Docker URL for Ollama, e.g., http://ollama-api:11434',
                'validation' => 'required',
                'channel_based' => true,
            ],
        ],
    ],
];
