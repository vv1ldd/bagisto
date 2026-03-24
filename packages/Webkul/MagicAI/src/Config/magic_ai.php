<?php

return [
    'enabled' => env('MAGIC_AI_ENABLED', false),
    'api_key' => env('MAGIC_AI_API_KEY'),
    'organization' => env('MAGIC_AI_ORGANIZATION'),
    'api_domain' => env('MAGIC_AI_API_DOMAIN'),

    'knowledge_base' => [
        'embedding_model' => env('MAGIC_AI_EMBEDDING_MODEL', 'text-embedding-3-small'),
    ],
];
