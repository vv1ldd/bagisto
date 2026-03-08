<?php

return [
    [
        'key' => 'general.magic_ai',
        'name' => 'admin::app.magic-ai.knowledge-base.title',
        'info' => 'MagicAI Global Settings',
        'icon' => 'settings/magic-ai.svg',
        'sort' => 1,
    ],
    [
        'key' => 'general.magic_ai.settings',
        'name' => 'admin::app.magic-ai.knowledge-base.create.general',
        'sort' => 1,
        'fields' => [
            [
                'name' => 'embedding_model',
                'title' => 'Embedding Model',
                'type' => 'text',
                'info' => 'Model name for embeddings, e.g., nomic-embed-text (default).',
                'validation' => 'required',
                'channel_based' => true,
            ],
        ],
    ],
];
