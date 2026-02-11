<?php
/**
 * AJAX TROUBLESHOOTER
 * 
 * This script diagnoses AJAX/API issues for job acceptance
 */

define('DIAG_PASSWORD', 'Airport2026Diagnose');

$password = $_GET['password'] ?? '';
if ($password !== DIAG_PASSWORD) {
    die('Access denied. Add ?password=Airport2026Diagnose to URL');
}

echo "<!DOCTYPE html><html><head><title>AJAX Troubleshooter</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}.fix{color:#00ffff;}</style></head><body>";

echo "<h2>AJAX/API Issues Troubleshooter</h2>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<div class='info'>🔍 Diagnosing common AJAX issues...</div><br>";
    
    // 1. Check CSRF token generation
    echo "<div class='info'>1. CSRF Token Check:</div>";
    try {
        $token = csrf_token();
        echo "<div class='success'>✓ CSRF token generated: " . substr($token, 0, 20) . "...</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ CSRF token generation failed: " . $e->getMessage() . "</div>";
    }
    echo "<br>";
    
    // 2. Check session configuration
    echo "<div class='info'>2. Session Configuration:</div>";
    echo "<div class='info'>Driver: " . config('session.driver') . "</div>";
    echo "<div class='info'>Domain: " . (config('session.domain') ?: 'null') . "</div>";
    echo "<div class='info'>Secure: " . (config('session.secure') ? 'true' : 'false') . "</div>";
    echo "<div class='info'>SameSite: " . config('session.same_site') . "</div>";
    
    if (config('session.domain') && !str_contains(request()->getHost(), config('session.domain'))) {
        echo "<div class='error'>❌ Session domain mismatch!</div>";
        echo "<div class='fix'>Fix: Update SESSION_DOMAIN in .env to match your domain</div>";
    } else {
        echo "<div class='success'>✓ Session domain OK</div>";
    }
    echo "<br>";
    
    // 3. Check middleware
    echo "<div class='info'>3. Driver Authentication:</div>";
    
    // Simulate driver authentication check
    $driverGuard = config('auth.guards.driver');
    if (!$driverGuard) {
        echo "<div class='error'>❌ Driver guard not configured</div>";
    } else {
        echo "<div class='success'>✓ Driver guard configured: " . json_encode($driverGuard) . "</div>";
    }
    echo "<br>";
    
    // 4. Check route registration
    echo "<div class='info'>4. Route Registration:</div>";
    try {
        $route = route('driver.jobs.accept', 1);
        echo "<div class='success'>✓ driver.jobs.accept route: " . $route . "</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Route not found: " . $e->getMessage() . "</div>";
    }
    echo "<br>";
    
    // 5. Check database tables
    echo "<div class='info'>5. Database Schema Check:</div>";
    try {
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
        $tableNames = array_map(function($table) {
            return array_values((array)$table)[0];
        }, $tables);
        
        $requiredTables = ['bookings', 'drivers', 'users', 'sessions'];
        foreach ($requiredTables as $table) {
            if (in_array($table, $tableNames)) {
                echo "<div class='success'>✓ Table exists: {$table}</div>";
            } else {
                echo "<div class='error'>❌ Missing table: {$table}</div>";
            }
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Database check failed: " . $e->getMessage() . "</div>";
    }
    echo "<br>";
    
    // 6. Generate fix recommendations
    echo "<div class='fix'><h3>🔧 Recommended Fixes:</h3></div>";
    echo "<div class='fix'>1. Clear all caches and sessions:</div>";
    echo "<div class='info'>   - Visit: https://admin.executiveairportcars.com/optimize.php?password=Airport2026Optimize</div>";
    echo "<br>";
    
    echo "<div class='fix'>2. Update .env session settings:</div>";
    echo "<div class='info'>   SESSION_DOMAIN=admin.executiveairportcars.com</div>";
    echo "<div class='info'>   SESSION_SECURE=true (if using HTTPS)</div>";
    echo "<div class='info'>   SESSION_SAME_SITE=lax</div>";
    echo "<br>";
    
    echo "<div class='fix'>3. Test this CSRF endpoint:</div>";
    echo "<div class='info'>   curl -X GET https://admin.executiveairportcars.com/csrf-test.php?password=Airport2026CSRFTest</div>";
    echo "<br>";
    
    echo "<div class='error'><h3>⚠ DELETE THIS FILE AFTER DIAGNOSIS!</h3></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body></html>";
?>