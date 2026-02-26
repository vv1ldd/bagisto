<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Passkey Route Check</h1>";

$loaderPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($loaderPath)) {
    die("Autoloader not found at $loaderPath");
}

require $loaderPath;
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); // Bootstrap console for routes

echo "<h2>1. Checking Route existence</h2>";
try {
    $url = route('shop.customers.account.passkeys.index');
    echo "Route 'shop.customers.account.passkeys.index' exists: <span style='color:green'>YES</span> - URL: $url<br>";
} catch (\Exception $e) {
    echo "Route 'shop.customers.account.passkeys.index' error: <span style='color:red'>" . $e->getMessage() . "</span><br>";
}

echo "<h2>2. Checking Controller availability</h2>";
try {
    $controllerClass = \Webkul\Shop\Http\Controllers\Customer\PasskeyController::class;
    echo "Controller class string: $controllerClass<br>";

    if (class_exists($controllerClass)) {
        echo "Class exists: <span style='color:green'>YES</span><br>";

        // Try to instantiate via container to check dependencies
        $controller = $app->make($controllerClass);
        echo "Instantiation via Container: <span style='color:green'>SUCCESS</span><br>";
    } else {
        echo "Class exists: <span style='color:red'>NO</span><br>";
    }
} catch (\Exception $e) {
    echo "Controller check error: <span style='color:red'>" . $e->getMessage() . "</span><br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
