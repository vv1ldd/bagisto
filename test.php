<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$customer = Webkul\Customer\Models\Customer::first();
if (!$customer) {
    echo "No customer found\n";
    exit;
}

auth()->guard('customer')->login($customer);

$request = Illuminate\Http\Request::create('/checkout');
$request->setLaravelSession($app['session']->driver());
$app['auth']->setRequest($request);

$response = $kernel->handle($request);

if ($response instanceof \Illuminate\Http\Response || $response instanceof \Illuminate\Http\JsonResponse) {
    file_put_contents('/tmp/checkout_auth.html', $response->getContent());
    echo "Saved to /tmp/checkout_auth.html\n";
} else {
    echo "Redirect or something else.\n";
}
