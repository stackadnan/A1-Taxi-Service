<?php
/**
 * SESSION & AJAX FIXER
 * 
 * Fixes driver authentication persistence in AJAX requests
 */

define('SESSION_AJAX_PASSWORD', 'Airport2026SessionAjax');

$password = $_GET['password'] ?? '';
if ($password !== SESSION_AJAX_PASSWORD) {
    die('Access denied. Add ?password=Airport2026SessionAjax to URL');
}

echo "<!DOCTYPE html><html><head><title>Session & AJAX Fixer</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}.fix{color:#00ffff;}</style></head><body>";

echo "<h2>Session & AJAX Authentication Fixer</h2>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<div class='fix'><h3>🔧 Fixing Driver Authentication in AJAX</h3></div>";
    
    // Check current session configuration
    echo "<div class='info'>1. Current Session Configuration:</div>";
    echo "<div class='info'>  Driver: " . config('session.driver') . "</div>";
    echo "<div class='info'>  Domain: " . (config('session.domain') ?: 'null') . "</div>";
    echo "<div class='info'>  Secure: " . (config('session.secure') ? 'true' : 'false') . "</div>";
    echo "<div class='info'>  SameSite: " . config('session.same_site') . "</div>";
    echo "<div class='info'>  HttpOnly: " . (config('session.http_only') ? 'true' : 'false') . "</div>";
    echo "<br>";
    
    // Check if session is working
    session()->start();
    $sessionId = session()->getId();
    echo "<div class='info'>2. Session Status:</div>";
    echo "<div class='success'>  Session ID: {$sessionId}</div>";
    
    // Test driver authentication
    echo "<div class='info'>3. Driver Authentication Test:</div>";
    $driver = \Illuminate\Support\Facades\Auth::guard('driver')->user();
    if ($driver) {
        echo "<div class='success'>  ✓ Driver authenticated: {$driver->name} (ID: {$driver->id})</div>";
    } else {
        echo "<div class='error'>  ❌ No driver authenticated</div>";
        
        // Try to find any active driver sessions
        echo "<div class='info'>  Checking for driver sessions in database...</div>";
        try {
            $sessions = \Illuminate\Support\Facades\DB::table('sessions')
                ->where('last_activity', '>', time() - 3600)
                ->get();
            
            echo "<div class='info'>  Found " . $sessions->count() . " recent sessions</div>";
            
            foreach ($sessions as $session) {
                $payload = base64_decode($session->payload);
                if (strpos($payload, '"driver"') !== false) {
                    echo "<div class='warning'>  Found driver session: " . substr($session->id, 0, 10) . "...</div>";
                }
            }
        } catch (Exception $e) {
            echo "<div class='error'>  Session check error: " . $e->getMessage() . "</div>";
        }
    }
    echo "<br>";
    
    echo "<div class='fix'><h3>🔧 Updated .env Settings for AJAX Fix:</h3></div>";
    echo "<textarea style='width:100%;height:250px;background:#333;color:#0f0;border:1px solid #555;'>";
    echo "# AJAX-Compatible Session Settings\n";
    echo "SESSION_DRIVER=database\n";
    echo "SESSION_LIFETIME=1200\n";
    echo "SESSION_ENCRYPT=false\n";
    echo "SESSION_PATH=/\n";
    echo "SESSION_DOMAIN=" . $_SERVER['HTTP_HOST'] . "\n";
    echo "SESSION_SECURE=" . (isset($_SERVER['HTTPS']) ? 'true' : 'false') . "\n";
    echo "SESSION_SAME_SITE=lax\n";
    echo "SESSION_HTTP_ONLY=false\n\n";
    echo "# Important: Set HttpOnly to false for AJAX access\n";
    echo "# Important: Use 'lax' SameSite for cross-request compatibility\n";
    echo "</textarea><br>";
    
    echo "<div class='fix'><h3>🚀 JavaScript AJAX Fix:</h3></div>";
    echo "<div class='info'>Copy this improved AJAX code for job acceptance:</div>";
    echo "<textarea style='width:100%;height:300px;background:#333;color:#0f0;border:1px solid #555;'>";
    echo "// IMPROVED AJAX JOB ACCEPTANCE\n";
    echo "function acceptJob(bookingId) {\n";
    echo "    console.log('Accepting job:', bookingId);\n";
    echo "    \n";
    echo "    // Get fresh CSRF token\n";
    echo "    const metaToken = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content');\n";
    echo "    const csrfToken = metaToken || window.Laravel.csrfToken;\n";
    echo "    \n";
    echo "    console.log('Using CSRF token:', csrfToken);\n";
    echo "    \n";
    echo "    const url = window.Laravel.driverAcceptUrl.replace(':id', bookingId);\n";
    echo "    console.log('Request URL:', url);\n";
    echo "    \n";
    echo "    fetch(url, {\n";
    echo "        method: 'POST',\n";
    echo "        headers: {\n";
    echo "            'Content-Type': 'application/json',\n";
    echo "            'X-CSRF-TOKEN': csrfToken,\n";
    echo "            'Accept': 'application/json',\n";
    echo "            'X-Requested-With': 'XMLHttpRequest'\n";
    echo "        },\n";
    echo "        credentials: 'same-origin',  // Important: Include cookies\n";
    echo "        mode: 'cors'\n";
    echo "    })\n";
    echo "    .then(response => {\n";
    echo "        console.log('Response status:', response.status);\n";
    echo "        console.log('Response headers:', response.headers);\n";
    echo "        \n";
    echo "        if (response.status === 403) {\n";
    echo "            throw new Error('Authentication failed - please refresh page and try again');\n";
    echo "        }\n";
    echo "        \n";
    echo "        if (!response.ok) {\n";
    echo "            throw new Error(`HTTP error! status: \${response.status}`);\n";
    echo "        }\n";
    echo "        return response.json();\n";
    echo "    })\n";
    echo "    .then(data => {\n";
    echo "        console.log('Success:', data);\n";
    echo "        if (data.success) {\n";
    echo "            showNotification(data.message || 'Job accepted successfully!', 'success');\n";
    echo "            setTimeout(() => {\n";
    echo "                window.location.reload();\n";
    echo "            }, 1000);\n";
    echo "        } else {\n";
    echo "            showNotification(data.error || 'Failed to accept job', 'error');\n";
    echo "        }\n";
    echo "    })\n";
    echo "    .catch(error => {\n";
    echo "        console.error('Error:', error);\n";
    echo "        showNotification('Error: ' + error.message, 'error');\n";
    echo "    });\n";
    echo "}\n";
    echo "</textarea><br>";
    
    echo "<div class='fix'><h3>📋 Fix Steps:</h3></div>";
    echo "<div class='success'>1. Update .env file with the settings above</div>";
    echo "<div class='success'>2. Clear all caches: run optimize.php</div>";
    echo "<div class='success'>3. Replace acceptJob function in driver layout</div>";
    echo "<div class='success'>4. Ensure driver logs out and logs back in</div>";
    echo "<div class='success'>5. Test job acceptance</div><br>";
    
    echo "<div class='warning'><h3>🎯 Root Cause:</h3></div>";
    echo "<div class='info'>Driver authentication cookies aren't being sent with AJAX requests</div>";
    echo "<div class='info'>This is typically caused by:</div>";
    echo "<div class='error'>• SESSION_HTTP_ONLY=true blocking JavaScript access</div>";
    echo "<div class='error'>• SESSION_SAME_SITE=strict blocking cross-request cookies</div>";
    echo "<div class='error'>• Missing 'credentials: same-origin' in fetch requests</div>";
    
    echo "<br><div class='error'><h3>⚠ DELETE THIS FILE AFTER USE!</h3></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body></html>";
?>