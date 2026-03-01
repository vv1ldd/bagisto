<?php

return [
    [
        'key' => 'account',
        'name' => 'shop::app.layouts.my-account',
        'route' => 'shop.customers.account.index',
        'icon' => '',
        'sort' => 1,
    ],
    [
        'key' => 'account.profile',
        'name' => 'shop::app.layouts.header.mobile.profile',
        'route' => 'shop.customers.account.profile.index',
        'sort' => 1,
    ],
    [
        'key' => 'account.crypto',
        'name' => 'shop::app.layouts.crypto-wallets',
        'route' => 'shop.customers.account.crypto.index',
        'sort' => 2,
    ],
    [
        'key' => 'account.passkeys',
        'name' => 'shop::app.layouts.passkeys',
        'route' => 'shop.customers.account.passkeys.index',
        'icon' => 'icon-security',
        'sort' => 2,
    ],
    [
        'key' => 'account.login_activity',
        'name' => 'shop::app.layouts.login-activity',
        'route' => 'shop.customers.account.login_activity.index',
        'icon' => 'icon-activity',
        'sort' => 3,
    ],
    [
        'key' => 'account.orders',
        'name' => 'shop::app.layouts.orders',
        'route' => 'shop.customers.account.orders.index',
        'icon' => 'icon-orders',
        'sort' => 4,
    ],

    [
        'key' => 'account.reviews',
        'name' => 'shop::app.layouts.reviews',
        'route' => 'shop.customers.account.reviews.index',
        'icon' => 'icon-star',
        'sort' => 6,
    ],
    [
        'key' => 'account.wishlist',
        'name' => 'shop::app.layouts.wishlist',
        'route' => 'shop.customers.account.wishlist.index',
        'icon' => 'icon-heart',
        'sort' => 7,
    ],
    [
        'key' => 'account.gdpr_data_request',
        'name' => 'shop::app.layouts.gdpr-request',
        'route' => 'shop.customers.account.gdpr.index',
        'icon' => 'icon-gdpr-safe',
        'sort' => 8,
    ],
];
