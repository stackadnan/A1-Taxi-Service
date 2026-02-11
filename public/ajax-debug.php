<?php
/**
 * AJAX REQUEST DEBUGGER
 * 
 * Captures and debugs AJAX accept requests in real-time
 */

define('AJAX_DEBUG_PASSWORD', 'Airport2026AjaxDebug');

$password = $_GET['password'] ?? '';
if ($password !== AJAX_DEBUG_PASSWORD) {
    die('Access denied. Add ?password=Airport2026AjaxDebug to URL');
}

echo "<!DOCTYPE html><html><head><title>AJAX Debug Interceptor</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}.warning{color:#ff8800;}</style></head><body>";

echo "<h2>AJAX Accept Request Debugger</h2>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Check if this is a POST request (simulating the accept endpoint)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<div class='warning'><h3>🔍 INTERCEPTED AJAX REQUEST</h3></div>";
        
        // Debug headers
        echo "<div class='info'>1. Request Headers:</div>";
        foreach (getallheaders() as $name => $value) {
            echo "<div class='success'>  {$name}: {$value}</div>";
        }
        echo "<br>";
        
        // Debug CSRF token
        echo "<div class='info'>2. CSRF Token Check:</div>";
        $csrfFromHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? 'MISSING';
        $csrfFromSession = csrf_token();
        
        echo "<div class='info'>  Header Token: {$csrfFromHeader}</div>";
        echo "<div class='info'>  Session Token: {$csrfFromSession}</div>";
        echo "<div class='" . ($csrfFromHeader === $csrfFromSession ? 'success' : 'error') . "'>  Match: " . ($csrfFromHeader === $csrfFromSession ? 'YES' : 'NO') . "</div>";
        echo "<br>";
        
        // Debug authentication
        echo "<div class='info'>3. Authentication Check:</div>";
        try {
            $driver = \Illuminate\Support\Facades\Auth::guard('driver')->user();
            if ($driver) {
                echo "<div class='success'>  ✓ Driver authenticated: {$driver->name} (ID: {$driver->id})</div>";
            } else {
                echo "<div class='error'>  ❌ No driver authenticated</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>  Authentication error: " . $e->getMessage() . "</div>";
        }
        echo "<br>";
        
        // Debug job assignment
        $jobId = $_GET['job'] ?? 'unknown';
        echo "<div class='info'>4. Job Assignment Check (Job #{$jobId}):</div>";
        
        if ($jobId !== 'unknown') {
            try {
                $job = \App\Models\Booking::find($jobId);
                if ($job) {
                    echo "<div class='success'>  ✓ Job found: #{$job->id}</div>";
                    echo "<div class='info'>  Assigned to driver: {$job->driver_id}</div>";
                    
                    if ($driver && $job->driver_id == $driver->id) {
                        echo "<div class='success'>  ✓ Job correctly assigned to current driver</div>";
                    } else {
                        echo "<div class='error'>  ❌ Job assignment mismatch!</div>";
                        echo "<div class='error'>  Current driver: " . ($driver ? $driver->id : 'none') . "</div>";
                        echo "<div class='error'>  Job assigned to: {$job->driver_id}</div>";
                    }
                } else {
                    echo "<div class='error'>  ❌ Job not found</div>";
                }
            } catch (Exception $e) {
                echo "<div class='error'>  Job check error: " . $e->getMessage() . "</div>";
            }
        }
        echo "<br>";
        
        echo "<div class='warning'><h3>🎯 DIAGNOSIS COMPLETE</h3></div>";
        
    } else {
        // Show instructions for testing
        echo "<div class='info'>This debugger will intercept AJAX requests to diagnose the 403 error.</div><br>";
        
        echo "<div class='warning'><h3>🧪 How to Use:</h3></div>";
        echo "<div class='success'>1. Replace the accept URL in driver layout with this debug endpoint</div>";
        echo "<div class='success'>2. Try accepting a job</div>";
        echo "<div class='success'>3. This page will show exactly what's wrong</div><br>";
        
        echo "<div class='info'><h3>🔧 Temporary Test URL:</h3></div>";
        $currentDomain = $_SERVER['HTTP_HOST'];
        $testUrl = "https://{$currentDomain}/ajax-debug.php?password=Airport2026AjaxDebug&job=2";
        echo "<div class='success'>Test URL: {$testUrl}</div>";
        echo "<div class='info'>Make a POST request to this URL to simulate job acceptance</div><br>";
        
        // Show current session info
        echo "<div class='info'><h3>📋 Current Session Info:</h3></div>";
        try {
            $driver = \Illuminate\Support\Facades\Auth::guard('driver')->user();
            if ($driver) {
                echo "<div class='success'>Driver: {$driver->name} (ID: {$driver->id})</div>";
            } else {
                echo "<div class='error'>No driver authenticated</div>";
            }
            
            echo "<div class='info'>CSRF Token: " . csrf_token() . "</div>";
            echo "<div class='info'>Session ID: " . session()->getId() . "</div>";
            
        } catch (Exception $e) {
            echo "<div class='error'>Session check error: " . $e->getMessage() . "</div>";
        }
    }
    
    echo "<br><div class='error'><h3>⚠ DELETE THIS FILE AFTER DEBUGGING!</h3></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>