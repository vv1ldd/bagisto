<?php

return [
    'cashondelivery' => [
        'code' => 'cashondelivery',
        'title' => 'Банковский перевод для физических лиц',
        'description' => 'Банковский перевод для физических лиц',
        'class' => 'Webkul\Payment\Payment\CashOnDelivery',
        'active' => false,
        'sort' => 1,
    ],

    'moneytransfer' => [
        'code' => 'moneytransfer',
        'title' => 'Банковский перевод для юридических лиц',
        'description' => 'Банковский перевод для юридических лиц',
        'class' => 'Webkul\Payment\Payment\MoneyTransfer',
        'active' => false,
        'sort' => 2,
    ],

    'credits' => [
        'code' => 'credits',
        'title' => 'Meanly Wallet',
        'description' => 'Оплата кошельком',
        'class' => 'Webkul\Payment\Payment\Credits',
        'active' => true,
        'sort' => 1,
    ],
];
