<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "QUEUE_CONNECTION: " . config('queue.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
