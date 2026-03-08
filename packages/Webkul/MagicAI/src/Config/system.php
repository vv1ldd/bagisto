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
                'name' => 'settings_info',
                'title' => 'Настройки перенесены',
                'info' => 'Все настройки AI (ключи и адреса API) теперь управляются через переменные окружения (.env).',
                'type' => 'boolean',
                'default' => true,
            ]
        ],
    ],
];
