<?php
/**
 * ZONES ONLY SEEDER
 * 
 * This script runs ONLY the zones seeder
 * 
 * Usage: https://admin.executiveairportcars.com/zones-seed.php?password=Airport2026ZonesOnly
 */

define('ZONES_PASSWORD', 'Airport2026ZonesOnly');

// Check password
$password = $_GET['password'] ?? '';
if ($password !== ZONES_PASSWORD) {
    die('Access denied. Add ?password=Airport2026ZonesOnly to URL');
}

set_time_limit(300);

echo "<!DOCTYPE html><html><head><title>Zones Seeder</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}</style></head><body>";

echo "<h2>Zones Only Seeder</h2>";
echo "<div class='info'>Starting zones seeding process...</div><br>";
flush();

try {
    // Load Laravel
    require __DIR__.'/../vendor/autoload.php';
    
    echo "<div class='info'>Loading Laravel...</div><br>";
    flush();
    
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "<div class='info'>Getting kernel...</div><br>";
    flush();
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<div class='info'>Testing database connection...</div><br>";
    flush();
    
    // Test DB connection
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "<div class='success'>✓ Database connection successful!</div><br>";
    flush();
    
    // Check if zones.csv exists
    $zonesFile = __DIR__.'/../zones.csv';
    if (!file_exists($zonesFile)) {
        echo "<div class='error'>❌ zones.csv not found at project root!</div>";
        echo "<div class='info'>Please upload zones.csv to the root directory (same level as artisan file)</div>";
    } else {
        echo "<div class='success'>✓ zones.csv found!</div><br>";
        flush();
        
        // Run zones seeder only
        echo "<div class='info'>Running zones seeder...</div><br>";
        flush();
        
        $kernel->call('db:seed', ['--class' => 'Database\\Seeders\\ZoneCsvSeeder']);
        $seedOutput = $kernel->output();
        
        echo "<div class='success'>✓ Zones seeding completed!</div>";
        echo "<pre>" . htmlspecialchars($seedOutput) . "</pre>";
        flush();
        
        echo "<div class='success'><h3>✓ ZONES SEEDING COMPLETED!</h3></div>";
        echo "<div class='error'><h3>⚠ DELETE THIS FILE AFTER USE!</h3></div>";
        echo "<div class='info'>File to delete: " . __FILE__ . "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='error'>File: " . htmlspecialchars($e->getFile()) . "</div>";
    echo "<div class='error'>Line: " . $e->getLine() . "</div>";
    echo "<pre class='error'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>