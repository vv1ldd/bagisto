<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cashback Percent
    |--------------------------------------------------------------------------
    | Percentage of the order total to credit back to the customer's RUB
    | wallet balance after a successful non-wallet payment.
    | Set to 0 to disable cashback.
    |
    */
    'cashback_percent' => env('MEANLY_CASHBACK_PERCENT', 5),

    /*
    |--------------------------------------------------------------------------
    | Cashback Token Contract Address (ERC-20)
    |--------------------------------------------------------------------------
    | The address of the ERC-20 contract used for on-chain cashback.
    |
    */
    'cashback_token_contract_address' => env('CASHBACK_TOKEN_CONTRACT_ADDRESS', ''),

    /*
    |--------------------------------------------------------------------------
    | Cashback Coin Currency
    |--------------------------------------------------------------------------
    | The target currency for the minted coin. Usually 'RUB' or 'USD'.
    | If set to 'USD', the system will convert the RUB cashback amount 
    | using Bagisto's internal exchange rates before minting.
    |
    */
    'cashback_coin_currency' => env('CASHBACK_COIN_CURRENCY', 'RUB'),
];
