<?php
// Simple debug script for Hostinger
echo "=== DDS Debug Information ===<br><br>";

// PHP Version
echo "PHP Version: " . PHP_VERSION . "<br>";

// Basic file checks
$files = [
    'index.php',
    'composer.json',
    'vendor/autoload.php',
    'bootstrap/app.php',
    '.env'
];

echo "<br>Laravel Files Check:<br>";
foreach ($files as $file) {
    $exists = file_exists($file) ? "EXISTS" : "MISSING";
    echo "- $file: $exists<br>";
}

// Check vendor directory
$vendorExists = is_dir('vendor') ? "EXISTS" : "MISSING";
echo "<br>Vendor Directory: $vendorExists<br>";

// Check storage
$storageExists = is_dir('storage') ? "EXISTS" : "MISSING";
echo "Storage Directory: $storageExists<br>";

// Check if storage is writable
if (is_dir('storage')) {
    $writable = is_writable('storage') ? "WRITABLE" : "NOT WRITABLE";
    echo "Storage Writable: $writable<br>";
}

// Check bootstrap cache
$bootstrapCacheExists = is_dir('bootstrap/cache') ? "EXISTS" : "MISSING";
echo "Bootstrap Cache: $bootstrapCacheExists<br>";

if (is_dir('bootstrap/cache')) {
    $writable = is_writable('bootstrap/cache') ? "WRITABLE" : "NOT WRITABLE";
    echo "Bootstrap Cache Writable: $writable<br>";
}

echo "<br>=== End Debug Information ===";
?>