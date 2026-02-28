<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Webkul\Customer\Models\CryptoAddress;
use Webkul\Customer\Services\BlockchainSyncService;

$addressStr = 'UQDolrO5cIlq-RSkftro3HF3ZsvCI9qBHeiWgcgSOoLeCHB5';
$cryptoAddress = CryptoAddress::where('address', $addressStr)->first();

if (!$cryptoAddress) {
    die("Address not found in database: $addressStr\n");
}

echo "Found address for customer ID: " . $cryptoAddress->customer_id . "\n";
echo "Current verified_at: " . ($cryptoAddress->verified_at ?: 'NULL') . "\n";

$service = app(BlockchainSyncService::class);

// Force verification check
echo "Checking verification...\n";
$isVerified = $service->verifyOwnership($cryptoAddress);
echo "Verification result: " . ($isVerified ? 'VERIFIED' : 'NOT VERIFIED') . "\n";

// Refresh from DB
$cryptoAddress->refresh();

if ($cryptoAddress->isVerified()) {
    echo "Syncing deposits...\n";
    $newTxs = $service->syncDeposits($cryptoAddress);
    echo "Found " . count($newTxs) . " new transactions.\n";
    foreach ($newTxs as $tx) {
        echo " - Processed TX: " . $tx->tx_id . " Amount: " . $tx->amount . "\n";
    }
} else {
    echo "Still not verified. Checking TON transactions manually to see if logic is working...\n";
    // We can manually call fetchTonTransactions if we want to debug
}
