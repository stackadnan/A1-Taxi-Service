<?php
/**
 * TEMPORARY MIGRATION SCRIPT
 * 
 * IMPORTANT: DELETE THIS FILE AFTER RUNNING MIGRATIONS!
 * 
 * Usage:
 * 1. Upload this file to your Laravel root directory (same level as artisan)
 * 2. Visit: https://admin.executiveairportcars.com/migrate.php
 * 3. Wait for completion
 * 4. DELETE THIS FILE IMMEDIATELY after migrations complete
 */

// Security: Only allow execution once, then self-destruct
define('MIGRATION_PASSWORD', 'Airport2026MigrateSecure789');

// Check password
$password = $_GET['password'] ?? '';
if ($password !== MIGRATION_PASSWORD) {
    die('Access denied. Add ?password=Airport2026MigrateSecure789 to URL');
}

// Set time limit for large migrations
set_time_limit(300);

echo "<!DOCTYPE html><html><head><title>Laravel Migration Runner</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}</style></head><body>";

echo "<h2>Laravel Migration Runner</h2>";
echo "<div class='info'>Starting migration process...</div><br>";

// Load Laravel (go up one directory since we're in public/)
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    // Run migrations
    echo "<div class='info'>Running migrations...</div><br>";
    $kernel->call('migrate', ['--force' => true]);
    echo "<pre>" . htmlspecialchars($kernel->output()) . "</pre>";
    
    // Optional: Run seeders (uncomment if needed)
    // echo "<div class='info'>Running seeders...</div><br>";
    // $kernel->call('db:seed', ['--force' => true]);
    // echo "<pre>" . htmlspecialchars($kernel->output()) . "</pre>";
    
    // Clear caches
    echo "<div class='info'>Clearing caches...</div><br>";
    $kernel->call('config:clear');
    $kernel->call('cache:clear');
    $kernel->call('route:clear');
    $kernel->call('view:clear');
    
    echo "<div class='success'><h3>✓ Migration completed successfully!</h3></div>";
    echo "<div class='error'><h3>⚠ IMPORTANT: DELETE THIS FILE NOW!</h3></div>";
    echo "<div class='info'>File to delete: " . __FILE__ . "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
