<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$addresses = \Webkul\Customer\Models\CryptoAddress::where('network', 'usdt_ton')->get();
foreach ($addresses as $addr) {
    echo "ID: " . $addr->id . " | Address: [" . $addr->address . "]\n";
}
