<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Bagisto Cache Cleaner & Optimizer</h1>";

$loaderPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($loaderPath)) {
    require $loaderPath;
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle($request = Illuminate\Http\Request::capture());

    echo "<h2>Clearing Caches...</h2>";

    try {
        echo "1. Clearing View Cache: ";
        Illuminate\Support\Facades\Artisan::call('view:clear');
        echo "<span style='color:green'>DONE</span><br>";

        echo "2. Aggressive Config Cache Clear: ";
        $configCache = $app->basePath('bootstrap/cache/config.php');
        if (file_exists($configCache)) {
            unlink($configCache);
            echo "<span style='color:orange'>FILE DELETED</span> ";
        }
        Illuminate\Support\Facades\Artisan::call('config:clear');
        echo "<span style='color:green'>DONE</span><br>";

        echo "3. Aggressive Route Cache Clear: ";
        $routeCache = $app->basePath('bootstrap/cache/routes-v7.php');
        if (file_exists($routeCache)) {
            if (unlink($routeCache)) {
                echo "<span style='color:orange'>FILE DELETED</span> ";
            } else {
                echo "<span style='color:red'>UNLINK FAILED</span> ";
            }
        }
        Illuminate\Support\Facades\Artisan::call('route:clear');
        echo "<span style='color:green'>DONE</span><br>";

        echo "4. Running Package Discover: ";
        Illuminate\Support\Facades\Artisan::call('package:discover');
        echo "<span style='color:green'>DONE</span><br>";

        echo "5. Final Cleanup: ";
        Illuminate\Support\Facades\Artisan::call('cache:clear');
        echo "<span style='color:green'>DONE</span><br>";

        echo "<h2>Verification after clear:</h2>";
        $hasRoute = Illuminate\Support\Facades\Route::has('shop.customers.account.passkeys.index');
        echo "Route 'shop.customers.account.passkeys.index' exists: " . ($hasRoute ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO</span>") . "<br>";

        $blade = app('blade.compiler');
        $directives = array_keys($blade->getCustomDirectives());
        echo "Registered Custom Directives: " . (in_array('bagistoVite', $directives) ? "<span style='color:green'>FOUND</span>" : "<span style='color:red'>MISSING</span>") . "<br>";

    } catch (Exception $e) {
        echo "<span style='color:red'>ERROR: " . $e->getMessage() . "</span><br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }

} else {
    echo "Autoloader missing.";
}
