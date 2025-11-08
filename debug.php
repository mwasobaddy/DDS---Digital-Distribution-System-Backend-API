<?php
// Debug script for Hostinger server
echo "=== DDS Debug Information ===\n\n";

// PHP Version
echo "PHP Version: " . PHP_VERSION . "\n";

// Check if Laravel files exist
$laravelFiles = [
    'vendor/autoload.php',
    'bootstrap/app.php',
    'config/app.php',
    '.env'
];

echo "\nLaravel Files Check:\n";
foreach ($laravelFiles as $file) {
    echo "- $file: " . (file_exists($file) ? "EXISTS" : "MISSING") . "\n";
}

// Check vendor directory
echo "\nVendor Directory: " . (is_dir('vendor') ? "EXISTS" : "MISSING") . "\n";

// Check storage permissions
$storageDirs = [
    'storage',
    'storage/logs',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views'
];

echo "\nStorage Permissions:\n";
foreach ($storageDirs as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? "WRITABLE" : "NOT WRITABLE";
        echo "- $dir: $writable\n";
    } else {
        echo "- $dir: DIRECTORY MISSING\n";
    }
}

// Check database connection
echo "\nDatabase Connection Test:\n";
try {
    $host = getenv('DB_HOST') ?: 'localhost';
    $database = getenv('DB_DATABASE');
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');

    if ($database && $username) {
        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        echo "Database: CONNECTED\n";

        // Test a simple query
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "Database Query: SUCCESS\n";
    } else {
        echo "Database: CONFIG MISSING\n";
    }
} catch (Exception $e) {
    echo "Database: ERROR - " . $e->getMessage() . "\n";
}

// Check if we can load Laravel
echo "\nLaravel Bootstrap Test:\n";
try {
    if (file_exists('vendor/autoload.php')) {
        require 'vendor/autoload.php';

        if (file_exists('bootstrap/app.php')) {
            $app = require 'bootstrap/app.php';
            echo "Laravel: BOOTSTRAPPED SUCCESSFULLY\n";
        } else {
            echo "Laravel: bootstrap/app.php MISSING\n";
        }
    } else {
        echo "Laravel: vendor/autoload.php MISSING\n";
    }
} catch (Exception $e) {
    echo "Laravel: BOOTSTRAP ERROR - " . $e->getMessage() . "\n";
}

echo "\n=== End Debug Information ===\n";
?>