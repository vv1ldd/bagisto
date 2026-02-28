<?php

return [
    'verification_addresses' => [
        'bitcoin' => env('CRYPTO_VERIFICATION_BTC_ADDRESS', 'bc1qxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
        'ethereum' => env('CRYPTO_VERIFICATION_ETH_ADDRESS', '0x0000000000000000000000000000000000000000'),
    ],
];
