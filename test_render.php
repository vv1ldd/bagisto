<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // We mock the authenticated user since the views might rely on auth()->guard('customer')->user()
    // It's mostly rendering the view. Try to render the account view:
    $view = view('shop::customers.account.index')->render();
    echo "View rendered successfully.\n";
} catch (\Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
