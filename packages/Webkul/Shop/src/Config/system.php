<?php

return [
    [
        'key' => 'customer.settings.login_page',
        'name' => 'shop::app.admin.system.login-page.title',
        'info' => 'shop::app.admin.system.login-page.info',
        'icon' => 'settings/login.svg',
        'sort' => 4,
        'fields' => [
            [
                'name' => 'background_image',
                'title' => 'shop::app.admin.system.login-page.background-image',
                'type' => 'image',
                'channel_based' => true,
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ],
            [
                'name' => 'scheduled_image_1',
                'title' => 'shop::app.admin.system.login-page.scheduled-image-1',
                'type' => 'image',
                'channel_based' => true,
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ],
            [
                'name' => 'scheduled_image_1_start',
                'title' => 'shop::app.admin.system.login-page.start-date',
                'type' => 'date',
                'channel_based' => true,
            ],
            [
                'name' => 'scheduled_image_1_end',
                'title' => 'shop::app.admin.system.login-page.end-date',
                'type' => 'date',
                'channel_based' => true,
            ],
            [
                'name' => 'scheduled_image_2',
                'title' => 'shop::app.admin.system.login-page.scheduled-image-2',
                'type' => 'image',
                'channel_based' => true,
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ],
            [
                'name' => 'scheduled_image_2_start',
                'title' => 'shop::app.admin.system.login-page.start-date',
                'type' => 'date',
                'channel_based' => true,
            ],
            [
                'name' => 'scheduled_image_2_end',
                'title' => 'shop::app.admin.system.login-page.end-date',
                'type' => 'date',
                'channel_based' => true,
            ],
            [
                'name' => 'scheduled_image_3',
                'title' => 'shop::app.admin.system.login-page.scheduled-image-3',
                'type' => 'image',
                'channel_based' => true,
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ],
            [
                'name' => 'scheduled_image_3_start',
                'title' => 'shop::app.admin.system.login-page.start-date',
                'type' => 'date',
                'channel_based' => true,
            ],
            [
                'name' => 'scheduled_image_3_end',
                'title' => 'shop::app.admin.system.login-page.end-date',
                'type' => 'date',
                'channel_based' => true,
            ],
        ],
    ],
];
