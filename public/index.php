<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

try {
    // Register the Composer autoloader...
    require __DIR__.'/../vendor/autoload.php';

    // Bootstrap Laravel and handle the request...
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    // Log the error
    $logFile = __DIR__.'/../storage/logs/laravel.log';
    $errorMessage = sprintf(
        "[%s] Bootstrap Error: %s in %s:%d\nStack trace:\n%s\n\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    
    // Try to write to log
    if (is_writable(dirname($logFile))) {
        file_put_contents($logFile, $errorMessage, FILE_APPEND);
    }
    
    // Return error response
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Laravel bootstrap failed',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => explode("\n", $e->getTraceAsString())
    ]);
    exit;
}
