<?php
/**
 * TEMPORARY MIGRATION SCRIPT
 * 
 * IMPORTANT: DELETE THIS FILE AFTER RUNNING MIGRATIONS!
 * 
 * Usage:
 * 1. Upload this file to your Laravel root directory (same level as artisan)
 * 2. Visit: https://admin.executiveairportcars.com/amigrate.php?password=Airport2026MigrateSecure789
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

// Set time limit and memory for large migrations
set_time_limit(600);
ini_set('memory_limit', '256M');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Laravel Migration Runner</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}</style></head><body>";

echo "<h2>Laravel Migration Runner</h2>";
echo "<div class='info'>Starting migration process...</div><br>";

// Flush output immediately
flush();
ob_flush();

echo "<div class='info'>Loading Laravel...</div><br>";
flush();

try {
    // Load Laravel (go up one directory since we're in public/)
    require __DIR__.'/../vendor/autoload.php';
    
    echo "<div class='info'>Bootstrap Laravel...</div><br>";
    flush();
    
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "<div class='info'>Bootstrapping HTTP Kernel...</div><br>";
    flush();
    
    // Clear any cached config first
    $configPath = __DIR__.'/../bootstrap/cache/config.php';
    if (file_exists($configPath)) {
        unlink($configPath);
        echo "<div class='info'>✓ Cleared config cache</div><br>";
        flush();
    }
    
    $servicesPath = __DIR__.'/../bootstrap/cache/services.php';
    if (file_exists($servicesPath)) {
        unlink($servicesPath);
        echo "<div class='info'>✓ Cleared services cache</div><br>";
        flush();
    }
    
    $packagesPath = __DIR__.'/../bootstrap/cache/packages.php';
    if (file_exists($packagesPath)) {
        unlink($packagesPath);
        echo "<div class='info'>✓ Cleared packages cache</div><br>";
        flush();
    }
    
    echo "<div class='info'>Bootstrapping application...</div><br>";
    flush();
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<div class='info'>Testing database connection...</div><br>";
    flush();
    
    // Test DB connection
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "<div class='success'>✓ Database connection successful!</div><br>";
    flush();

    // Run migrations
    echo "<div class='info'>Running migrations...</div><br>";
    flush();
    
    $kernel->call('migrate', ['--force' => true]);
    $migrationOutput = $kernel->output();
    
    echo "<div class='success'>✓ Migrations completed!</div>";
    echo "<pre>" . htmlspecialchars($migrationOutput) . "</pre>";
    flush();
    
    // Run seeders
    echo "<div class='info'>Running seeders...</div><br>";
    flush();
    
    $kernel->call('db:seed', ['--force' => true]);
    $seedOutput = $kernel->output();
    
    echo "<div class='success'>✓ Seeders completed!</div>";
    echo "<pre>" . htmlspecialchars($seedOutput) . "</pre>";
    flush();
    
    // Clear caches
    echo "<div class='info'>Clearing caches...</div><br>";
    flush();
    
    $kernel->call('config:clear');
    $kernel->call('cache:clear');
    $kernel->call('route:clear');
    $kernel->call('view:clear');
    
    echo "<div class='success'>✓ Caches cleared!</div><br>";
    flush();
    
    echo "<div class='success'><h3>✓ ALL OPERATIONS COMPLETED SUCCESSFULLY!</h3></div>";
    echo "<div class='error'><h3>⚠ IMPORTANT: DELETE THIS FILE NOW!</h3></div>";
    echo "<div class='info'>File to delete: " . __FILE__ . "</div>";

} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='error'>File: " . htmlspecialchars($e->getFile()) . "</div>";
    echo "<div class='error'>Line: " . $e->getLine() . "</div>";
    echo "<pre class='error'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    if (strpos($e->getMessage(), 'database') !== false || strpos($e->getMessage(), 'connection') !== false) {
        echo "<div class='info'>💡 This looks like a database issue. Check:</div>";
        echo "<div class='info'>- Database credentials in .env file</div>";
        echo "<div class='info'>- Database server is running</div>";
        echo "<div class='info'>- Database name exists</div>";
    }
} catch (Error $e) {
    echo "<div class='error'>FATAL ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='error'>File: " . htmlspecialchars($e->getFile()) . "</div>";
    echo "<div class='error'>Line: " . $e->getLine() . "</div>";
    echo "<pre class='error'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
