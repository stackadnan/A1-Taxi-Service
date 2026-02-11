<?php
/**
 * CACHE CLEARER
 * Clears all Laravel caches to apply new session configuration
 */

define('CACHE_PASSWORD', 'Airport2026ClearCache');

$password = $_GET['password'] ?? '';
if ($password !== CACHE_PASSWORD) {
    die('Access denied. Add ?password=Airport2026ClearCache to URL');
}

echo "<!DOCTYPE html><html><head><title>Cache Clearer</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}</style></head><body>";

echo "<h2>Clearing All Caches for Session Fix</h2>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<div class='info'>Clearing configuration cache...</div>";
    $kernel->call('config:clear');
    echo "<div class='success'>✓ Configuration cache cleared</div><br>";
    
    echo "<div class='info'>Clearing route cache...</div>";
    $kernel->call('route:clear');
    echo "<div class='success'>✓ Route cache cleared</div><br>";
    
    echo "<div class='info'>Clearing view cache...</div>";
    $kernel->call('view:clear');
    echo "<div class='success'>✓ View cache cleared</div><br>";
    
    echo "<div class='info'>Clearing application cache...</div>";
    $kernel->call('cache:clear');
    echo "<div class='success'>✓ Application cache cleared</div><br>";
    
    // Clear session files if file driver was used before
    echo "<div class='info'>Clearing old session data...</div>";
    try {
        $sessionPath = storage_path('framework/sessions');
        if (is_dir($sessionPath)) {
            $files = glob($sessionPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            echo "<div class='success'>✓ Session files cleared</div>";
        }
        
        // Clear database sessions too
        \Illuminate\Support\Facades\DB::table('sessions')->truncate();
        echo "<div class='success'>✓ Database sessions cleared</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>Session clear warning: " . $e->getMessage() . "</div>";
    }
    
    echo "<br><div class='success'><h3>🚀 Cache Clear Complete!</h3></div>";
    echo "<div class='info'>New session settings are now active:</div>";
    echo "<div class='success'>• SESSION_HTTP_ONLY = false (AJAX enabled)</div>";
    echo "<div class='success'>• SESSION_SAME_SITE = lax (cross-request enabled)</div>";
    echo "<br>";
    echo "<div class='info'><h3>📋 Next Steps:</h3></div>";
    echo "<div class='success'>1. ✓ Session settings applied</div>";
    echo "<div class='success'>2. ✓ Caches cleared</div>";
    echo "<div class='error'>3. Driver must logout and login again</div>";
    echo "<div class='error'>4. Test job acceptance</div>";
    echo "<br>";
    echo "<div class='info'>Go to driver login and have them logout/login to get new session.</div>";
    
    echo "<br><div class='error'><h3>⚠ DELETE THIS FILE AFTER USE!</h3></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='error'>Check that .env settings are correct</div>";
}

echo "</body></html>";
?>