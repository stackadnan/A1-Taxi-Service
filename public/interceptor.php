<?php
/**
 * TEMPORARY AJAX INTERCEPTOR
 * 
 * Temporarily replaces the acceptJob function to debug the 403 error
 */

define('INTERCEPTOR_PASSWORD', 'Airport2026Intercept');

$password = $_GET['password'] ?? '';
if ($password !== INTERCEPTOR_PASSWORD) {
    die('Access denied. Add ?password=Airport2026Intercept to URL');
}

echo "<!DOCTYPE html><html><head><title>AJAX Interceptor</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}.fix{color:#00ffff;}</style></head><body>";

echo "<h2>Live AJAX Interceptor & Fixer</h2>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<div class='fix'><h3>🔧 Intercepting AJAX Calls to Debug 403 Errors</h3></div>";
    
    // Check current driver authentication
    $driver = \Illuminate\Support\Facades\Auth::guard('driver')->user();
    if ($driver) {
        echo "<div class='success'>✓ Driver authenticated: {$driver->name} (ID: {$driver->id})</div>";
    } else {
        echo "<div class='error'>❌ No driver authenticated - this is the problem!</div>";
    }
    
    echo "<br><div class='fix'>📋 JavaScript Code to Replace Accept Function:</div>";
    echo "<div class='info'>Copy and paste this into browser console on the New Jobs page:</div>";
    
    $currentDomain = $_SERVER['HTTP_HOST'];
    $debugUrl = "https://{$currentDomain}/ajax-debug.php?password=Airport2026AjaxDebug";
    
    echo "<textarea style='width:100%;height:400px;background:#333;color:#0f0;border:1px solid #555;font-family:monospace;'>";
    echo "// TEMPORARY DEBUG INTERCEPTOR\n";
    echo "// Replace the acceptJob function with debug version\n\n";
    echo "window.originalAcceptJob = window.acceptJob;\n\n";
    echo "window.acceptJob = function(bookingId) {\n";
    echo "    console.log('🔍 INTERCEPTED: Accept job called for booking:', bookingId);\n";
    echo "    \n";
    echo "    // Debug current state\n";
    echo "    console.log('CSRF Token:', window.Laravel.csrfToken);\n";
    echo "    console.log('Base URL:', window.Laravel.baseUrl);\n";
    echo "    console.log('Accept URL:', window.Laravel.driverAcceptUrl);\n";
    echo "    \n";
    echo "    const debugUrl = '{$debugUrl}&job=' + bookingId;\n";
    echo "    console.log('Debug URL:', debugUrl);\n";
    echo "    \n";
    echo "    // Make debug request\n";
    echo "    fetch(debugUrl, {\n";
    echo "        method: 'POST',\n";
    echo "        headers: {\n";
    echo "            'Content-Type': 'application/json',\n";
    echo "            'X-CSRF-TOKEN': window.Laravel.csrfToken,\n";
    echo "            'Accept': 'application/json',\n";
    echo "            'X-Requested-With': 'XMLHttpRequest'\n";
    echo "        },\n";
    echo "        credentials: 'same-origin'\n";
    echo "    })\n";
    echo "    .then(response => {\n";
    echo "        console.log('Debug response status:', response.status);\n";
    echo "        return response.text();\n";
    echo "    })\n";
    echo "    .then(html => {\n";
    echo "        console.log('Debug response:', html);\n";
    echo "        // Open debug result in new window\n";
    echo "        const debugWindow = window.open('', '_blank');\n";
    echo "        debugWindow.document.write(html);\n";
    echo "    })\n";
    echo "    .catch(error => {\n";
    echo "        console.error('Debug request failed:', error);\n";
    echo "    });\n";
    echo "};\n\n";
    echo "console.log('🎯 AJAX interceptor installed! Try accepting a job now.');\n";
    echo "</textarea><br>";
    
    echo "<div class='warning'><h3>📝 Instructions:</h3></div>";
    echo "<div class='success'>1. Go to the New Jobs page: https://{$currentDomain}/driver/jobs/new</div>";
    echo "<div class='success'>2. Open browser console (F12 → Console tab)</div>";
    echo "<div class='success'>3. Copy and paste the code above into console</div>";
    echo "<div class='success'>4. Press Enter to install the interceptor</div>";
    echo "<div class='success'>5. Click 'Accept' on a job - debug window will open</div>";
    echo "<div class='success'>6. The debug window will show the exact cause of 403 error</div><br>";
    
    echo "<div class='info'><h3>🔍 What This Will Show:</h3></div>";
    echo "<div class='info'>• CSRF token verification status</div>";
    echo "<div class='info'>• Driver authentication status</div>";
    echo "<div class='info'>• Job assignment verification</div>";
    echo "<div class='info'>• Exact cause of 403 error</div><br>";
    
    // Show direct test option
    echo "<div class='fix'><h3>🚀 Quick Test Option:</h3></div>";
    $testUrl = "https://{$currentDomain}/ajax-debug.php?password=Airport2026AjaxDebug&job=2";
    echo "<div class='info'>Direct POST test: <a href='#' onclick='testAccept()'>Click to test Job #2 acceptance</a></div>";
    
    echo "<script>";
    echo "function testAccept() {";
    echo "    fetch('{$testUrl}', {";
    echo "        method: 'POST',";
    echo "        headers: {";
    echo "            'Content-Type': 'application/json',";
    echo "            'X-CSRF-TOKEN': '" . csrf_token() . "',";
    echo "            'Accept': 'application/json'";
    echo "        }";
    echo "    })";
    echo "    .then(r => r.text())";
    echo "    .then(html => {";
    echo "        const w = window.open('', '_blank');";
    echo "        w.document.write(html);";
    echo "    });";
    echo "}";
    echo "</script>";
    
    echo "<br><div class='error'><h3>⚠ DELETE THIS FILE AFTER DEBUGGING!</h3></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body></html>";
?>