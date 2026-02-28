<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$address = 'UQDolrO5cIlq-RSkftro3HF3ZsvCI9qBHeiWgcgSOoLeCHB5';
$url = "https://tonapi.io/v2/accounts/{$address}/jettons/history?limit=10";

$response = Illuminate\Support\Facades\Http::get($url);
print_r($response->json());
