<?php

return [
    'verification_addresses' => [
        'bitcoin' => env('CRYPTO_VERIFICATION_BTC_ADDRESS', 'bc1qxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
        'ethereum' => env('CRYPTO_VERIFICATION_ETH_ADDRESS', '0x0000000000000000000000000000000000000000'),
        'ton' => env('CRYPTO_VERIFICATION_TON_ADDRESS', 'EQxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
        'usdt_ton' => env('CRYPTO_VERIFICATION_USDT_TON_ADDRESS', 'EQxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
        'dash' => env('CRYPTO_VERIFICATION_DASH_ADDRESS', 'Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
    ],
];
