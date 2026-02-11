<?php
/**
 * PERFORMANCE OPTIMIZER
 * 
 * This script optimizes Laravel for production performance
 * 
 * Usage: https://admin.executiveairportcars.com/optimize.php?password=Airport2026Optimize
 */

define('OPTIMIZE_PASSWORD', 'Airport2026Optimize');

// Check password
$password = $_GET['password'] ?? '';
if ($password !== OPTIMIZE_PASSWORD) {
    die('Access denied. Add ?password=Airport2026Optimize to URL');
}

set_time_limit(300);

echo "<!DOCTYPE html><html><head><title>Laravel Performance Optimizer</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}</style></head><body>";

echo "<h2>Laravel Performance Optimizer</h2>";
echo "<div class='info'>Starting optimization process...</div><br>";
flush();

try {
    // Load Laravel
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<div class='success'>✓ Laravel loaded successfully!</div><br>";
    flush();
    
    // 1. Clear all caches first
    echo "<div class='info'>Clearing all caches...</div><br>";
    flush();
    
    $kernel->call('cache:clear');
    echo "<div class='success'>✓ Application cache cleared</div>";
    
    $kernel->call('config:clear');
    echo "<div class='success'>✓ Config cache cleared</div>";
    
    $kernel->call('route:clear');
    echo "<div class='success'>✓ Route cache cleared</div>";
    
    $kernel->call('view:clear');
    echo "<div class='success'>✓ View cache cleared</div><br>";
    flush();
    
    // 2. Optimize for production
    echo "<div class='info'>Building optimized caches...</div><br>";
    flush();
    
    $kernel->call('config:cache');
    echo "<div class='success'>✓ Config cached</div>";
    
    $kernel->call('route:cache');
    echo "<div class='success'>✓ Routes cached</div>";
    
    $kernel->call('view:cache');
    echo "<div class='success'>✓ Views cached</div><br>";
    flush();
    
    // 3. Optimize Composer autoloader
    echo "<div class='info'>Optimizing Composer autoloader...</div><br>";
    flush();
    
    $composerPath = trim(shell_exec('which composer 2>/dev/null') ?: '');
    if (empty($composerPath)) {
        $possiblePaths = ['/usr/local/bin/composer', '/usr/bin/composer'];
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $composerPath = $path;
                break;
            }
        }
    }
    
    if (!empty($composerPath)) {
        chdir(__DIR__ . '/../');
        $output = [];
        exec("$composerPath dump-autoload --optimize --no-dev 2>&1", $output);
        echo "<div class='success'>✓ Composer autoloader optimized</div>";
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    } else {
        echo "<div class='info'>ℹ Composer not found - autoloader optimization skipped</div>";
    }
    
    // 4. Check OPcache status
    echo "<div class='info'>Checking PHP optimization...</div><br>";
    flush();
    
    if (function_exists('opcache_get_status')) {
        $opcache = opcache_get_status();
        if ($opcache && $opcache['opcache_enabled']) {
            echo "<div class='success'>✓ OPcache is enabled and working</div>";
            echo "<div class='info'>Hit rate: " . round($opcache['opcache_statistics']['opcache_hit_rate'], 2) . "%</div>";
        } else {
            echo "<div class='error'>⚠ OPcache is disabled - contact hosting provider to enable it</div>";
        }
    } else {
        echo "<div class='error'>⚠ OPcache not available - contact hosting provider</div>";
    }
    
    echo "<div class='success'><h3>✓ OPTIMIZATION COMPLETED!</h3></div>";
    echo "<div class='info'>Your website should now be significantly faster.</div>";
    echo "<div class='error'><h3>⚠ DELETE THIS FILE NOW!</h3></div>";
    echo "<div class='info'>File to delete: " . __FILE__ . "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre class='error'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>