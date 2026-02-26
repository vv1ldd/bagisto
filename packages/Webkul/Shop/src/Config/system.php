<?php

return [
    [
        'key' => 'customer.login_page',
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
            // Placeholder for multiple images if needed in the future
            // [
            //     'name'          => 'background_images',
            //     'title'         => 'shop::app.admin.system.login-page.background-images',
            //     'type'          => 'gallery', // Assuming gallery type exists or can be handled
            //     'channel_based' => true,
            // ]
        ],
    ],
];
