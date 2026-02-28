<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Bagisto Diagnostic Tool v5</h1>";

$loaderPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($loaderPath)) {
    require $loaderPath;
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->handle($request = Illuminate\Http\Request::capture());

    echo "<h2>1. Configuration Check</h2>";
    echo "<h3>Relying Party Config:</h3>";
    echo "RP Name: " . config('passkeys.relying_party.name') . "<br>";
    echo "RP ID: " . config('passkeys.relying_party.id') . "<br>";
    echo "RP Icon: " . config('passkeys.relying_party.icon') . "<br>";
    echo "App URL: " . config('app.url') . "<br>";

    echo "<h3>Directives:</h3>";
    $blade = $app->make('blade.compiler');
    $directives = array_keys($blade->getCustomDirectives());
    echo "bagistoVite present: " . (in_array('bagistoVite', $directives) ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO</span>") . "<br>";

    echo "<h2>1.1 Route & Class Check</h2>";
    try {
        $hasPasskeyRoute = Illuminate\Support\Facades\Route::has('shop.customers.account.passkeys.index');
        echo "Route 'shop.customers.account.passkeys.index' exists: " . ($hasPasskeyRoute ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO</span>") . "<br>";

        $hasAccountRoute = Illuminate\Support\Facades\Route::has('shop.customers.account.index');
        echo "Route 'shop.customers.account.index' exists: " . ($hasAccountRoute ? "<span style='color:green'>YES</span>" : "<span style='color:red'>NO</span>") . "<br>";

        echo "<h3>Registered Routes matching 'passkey':</h3><pre style='background:#f9f9f9; padding:10px; font-size:11px;'>";
        $routes = Illuminate\Support\Facades\Route::getRoutes();
        $found = false;
        foreach ($routes as $route) {
            if (strpos((string) $route->getName(), 'passkey') !== false) {
                echo $route->getName() . " [" . implode('|', $route->methods()) . "] -> " . $route->uri() . "\n";
                $found = true;
            }
        }
        if (!$found)
            echo "No 'passkey' routes found in registry.";
        echo "</pre>";

        $className = 'Webkul\Shop\Http\Controllers\Customer\PasskeyController';
        if (class_exists($className)) {
            echo "Controller '$className' exists: <span style='color:green'>YES</span><br>";
            $reflection = new ReflectionClass($className);
            echo "File Path (Reflection): " . $reflection->getFileName() . "<br>";
            echo "Methods present: " . implode(', ', array_map(fn($m) => $m->name, $reflection->getMethods())) . "<br>";
        } else {
            echo "Controller '$className' exists: <span style='color:red'>NO</span><br>";
        }
    } catch (Exception $e) {
        echo "<span style='color:red'>Error during check: " . $e->getMessage() . "</span><br>";
    }

    echo "<h2>1.2 File Content Check (Server Perspective)</h2>";
    $filesToCheck = [
        'Routes' => __DIR__ . '/../packages/Webkul/Shop/src/Routes/customer-routes.php',
        'Controller' => __DIR__ . '/../packages/Webkul/Shop/src/Http/Controllers/Customer/PasskeyController.php',
    ];

    foreach ($filesToCheck as $label => $file) {
        if (file_exists($file)) {
            echo "<h3>$label File: $file</h3>";
            $contentString = file_get_contents($file);
            $contentLines = explode("\n", $contentString);

            echo "<pre style='background:#f9f9f9; padding:10px; font-size:11px; overflow:auto; max-height:500px;'>";
            foreach ($contentLines as $index => $line) {
                echo ($index + 1) . ": " . htmlspecialchars($line) . "\n";
            }
            echo "</pre>";
        } else {
            echo "<h3>$label File NOT FOUND: $file</h3>";
        }
    }

    echo "<h2>1.3 Route Cache Check</h2>";
    $cacheFile = $app->basePath('bootstrap/cache/routes-v7.php');
    echo "Route cache file exists ($cacheFile): " . (file_exists($cacheFile) ? "<span style='color:red'>YES</span>" : "<span style='color:green'>NO</span>") . "<br>";

    echo "<h2>2. Problematic View Inspection</h2>";
    $viewPath = '/var/www/html/storage/framework/views/9ae9589a6637aa46eaa8f4a4061dbe88.php';
    if (file_exists($viewPath)) {
        echo "File: $viewPath<br>";
        $lines = file($viewPath);
        $totalLines = count($lines);
        echo "Total Lines: $totalLines<br>";

        $start = max(0, 387 - 10);
        $end = min($totalLines, 387 + 10);

        echo "<h3>Lines Around 387:</h3><pre style='background:#f9f9f9; padding:10px; font-size:11px;'>";
        for ($i = $start; $i < $end; $i++) {
            $lineNum = $i + 1;
            $style = ($lineNum == 387) ? "background: #ffe4e1; font-weight: bold;" : "";
            echo "<span style='$style'>" . str_pad($lineNum, 4, ' ', STR_PAD_LEFT) . ": " . htmlspecialchars($lines[$i]) . "</span>";
        }
        echo "</pre>";
    } else {
        echo "Problematic view file NOT FOUND. It might have been cleared.";
    }

    echo "<h2>3. Recent Log (Last 2000 chars)</h2>";
    $logFile = __DIR__ . '/../storage/logs/laravel.log';
    if (file_exists($logFile)) {
        $content = file_get_contents($logFile);
        echo "<pre style='font-size:11px; background:#f0f0f0; padding:10px;'>" . htmlspecialchars(substr($content, -2000)) . "</pre>";
    }
} else {
    echo "Autoloader missing.";
}
