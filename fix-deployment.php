<?php

echo "--- Deployment Diagnostic Tool ---\n";

// 1. Check Permissions
echo "\n[Permissions Check]\n";
$paths = [
    '/var/www/html/storage',
    '/var/www/html/bootstrap/cache',
];

foreach ($paths as $path) {
    if (is_writable($path)) {
        echo "OK: $path is writable.\n";
    } else {
        echo "ERROR: $path is NOT writable. Current permissions: " . substr(sprintf('%o', fileperms($path)), -4) . "\n";
    }
}

// 2. Check Extensions
echo "\n[Extensions Check]\n";
$required_extensions = [
    'gd',
    'pdo_mysql',
    'mbstring',
    'intl',
    'curl',
    'zip',
    'xml'
];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "OK: Extension '$ext' is loaded.\n";
    } else {
        echo "ERROR: Extension '$ext' is MISSING!\n";
    }
}

// 3. Database Check
echo "\n[Database Check]\n";
try {
    $pdo = new PDO(
        "mysql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    echo "OK: Database connection successful.\n";
} catch (PDOException $e) {
    echo "ERROR: Connection failed: " . $e->getMessage() . "\n";
}

// 4. Redis Check
echo "\n[Redis Check]\n";
if (extension_loaded('redis')) {
    echo "OK: Redis extension loaded.\n";
    try {
        $redis = new Redis();
        $auth = getenv('REDIS_PASSWORD');
        if (!$auth) {
            $auth = 'redispassword'; // Fallback check
            echo "NOTICE: REDIS_PASSWORD env is empty, testing with default 'redispassword'...\n";
        }
        $host = getenv('REDIS_HOST') ?: 'redis';
        $port = getenv('REDIS_PORT') ?: 6379;

        echo "Connecting to Redis at $host:$port...\n";
        $redis->connect($host, $port);

        if ($auth) {
            echo "Authenticating with password... (Password length: " . strlen($auth) . ")\n";
            if ($redis->auth($auth)) {
                echo "OK: Redis authentication successful.\n";
            } else {
                echo "ERROR: Redis authentication FAILED. Used password from env.\n";
            }
        } else {
            echo "WARNING: No Redis password set in environment, but one might be required.\n";
        }

        if ($redis->ping()) {
            echo "OK: Redis connection successful.\n";
        } else {
            echo "ERROR: Redis ping failed.\n";
        }
    } catch (Exception $e) {
        echo "ERROR: Redis connection failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "ERROR: Redis extension NOT loaded.\n";
}

echo "\n--- Last 50 lines of Laravel Log ---\n";
$logFile = '/var/www/html/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = array_slice(file($logFile), -50);
    foreach ($lines as $line) {
        echo $line;
    }
} else {
    echo "Log file not found at $logFile\n";
}
echo "\n------------------------------------\n";
