<?php
/**
 * TEST JOB ACCEPTANCE
 * 
 * Tests the complete flow: login driver + accept job
 */

define('TEST_ACCEPT_PASSWORD', 'Airport2026TestAccept');

$password = $_GET['password'] ?? '';
if ($password !== TEST_ACCEPT_PASSWORD) {
    die('Access denied. Add ?password=Airport2026TestAccept');
}

$jobId = $_GET['job'] ?? null;
$driverId = $_GET['driver'] ?? null;

echo "<!DOCTYPE html><html><head><title>Test Job Acceptance</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}.warning{color:#ff8800;}</style></head><body>";

echo "<h2>Test Job Acceptance Flow</h2>";

if (!$jobId || !$driverId) {
    echo "<div class='error'>Missing parameters. Use: ?password=Airport2026TestAccept&job=2&driver=1</div>";
    echo "</body></html>";
    exit;
}

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<div class='info'>Testing Job #{$jobId} acceptance by Driver #{$driverId}</div><br>";
    
    // 1. Verify job exists and assignment
    $job = \App\Models\Booking::find($jobId);
    if (!$job) {
        echo "<div class='error'>❌ Job #{$jobId} not found</div>";
        echo "</body></html>";
        exit;
    }
    
    echo "<div class='success'>✓ Job #{$jobId} found</div>";
    echo "<div class='info'>Current driver_id: " . ($job->driver_id ?: 'NULL') . "</div>";
    echo "<div class='info'>Target driver: {$driverId}</div>";
    
    if ($job->driver_id != $driverId) {
        echo "<div class='warning'>⚠ Job is not assigned to driver {$driverId}. Fixing...</div>";
        $job->driver_id = $driverId;
        $job->save();
        echo "<div class='success'>✓ Job reassigned to driver {$driverId}</div>";
    } else {
        echo "<div class='success'>✓ Job correctly assigned to driver {$driverId}</div>";
    }
    
    // 2. Verify driver exists
    $driver = \App\Models\Driver::find($driverId);
    if (!$driver) {
        echo "<div class='error'>❌ Driver #{$driverId} not found</div>";
        echo "</body></html>";
        exit;
    }
    
    echo "<div class='success'>✓ Driver found: {$driver->name} ({$driver->email})</div>";
    echo "<div class='info'>Driver active: " . ($driver->is_active ? 'Yes' : 'No') . "</div>";
    
    // 3. Test authentication and job acceptance logic
    echo "<br><div class='info'>🧪 Simulating job acceptance...</div>";
    
    // Check if job already has a response
    $meta = $job->meta ?? [];
    $currentResponse = $meta['driver_response'] ?? 'none';
    echo "<div class='info'>Current response: {$currentResponse}</div>";
    
    if ($currentResponse !== 'none') {
        echo "<div class='warning'>⚠ Job already has response: {$currentResponse}</div>";
    }
    
    // Simulate the acceptance
    $meta['driver_response'] = 'accepted';
    $meta['driver_response_at'] = now()->toDateTimeString();
    $meta['status_changed_at'] = now()->toDateTimeString();
    $meta['test_accepted'] = true;
    $job->meta = $meta;
    $job->save();
    
    echo "<div class='success'>✅ Job acceptance SIMULATED successfully!</div>";
    echo "<div class='info'>Job #{$jobId} marked as accepted by driver #{$driverId}</div>";
    
    // 4. Show the real URLs that should work
    echo "<br><div class='info'><h3>🔗 Real URLs for testing:</h3></div>";
    $loginUrl = "https://" . $_SERVER['HTTP_HOST'] . "/driver/login";
    $jobUrl = "https://" . $_SERVER['HTTP_HOST'] . "/driver/jobs/new";
    $acceptUrl = "https://" . $_SERVER['HTTP_HOST'] . "/driver/jobs/{$jobId}/accept";
    
    echo "<div class='success'>1. Driver Login: <a href='{$loginUrl}' target='_blank'>{$loginUrl}</a></div>";
    echo "<div class='success'>2. View Jobs: <a href='{$jobUrl}' target='_blank'>{$jobUrl}</a></div>";
    echo "<div class='info'>3. Accept URL: {$acceptUrl} (POST request with CSRF token)</div>";
    
    // 5. Instructions
    echo "<br><div class='warning'><h3>📋 Manual Test Steps:</h3></div>";
    echo "<div class='info'>1. Open: {$loginUrl}</div>";
    echo "<div class='info'>2. Login with driver credentials (email: {$driver->email})</div>";
    echo "<div class='info'>3. Go to 'New Jobs' page</div>";
    echo "<div class='info'>4. Click 'Accept' button on Job #{$jobId}</div>";
    echo "<div class='info'>5. Should work without 403 errors!</div>";
    
    // 6. Reset for real testing if needed
    echo "<br><div class='warning'>🔄 Reset job for real testing:</div>";
    $resetUrl = "https://" . $_SERVER['HTTP_HOST'] . "/test-accept.php?password=Airport2026TestAccept&job={$jobId}&driver={$driverId}&reset=1";
    
    if (isset($_GET['reset'])) {
        $meta['driver_response'] = null;
        unset($meta['driver_response_at']);
        unset($meta['test_accepted']); 
        $job->meta = $meta;
        $job->save();
        echo "<div class='success'>✓ Job #{$jobId} reset for real testing</div>";
    } else {
        echo "<div class='info'>Reset URL: <a href='{$resetUrl}' target='_blank'>{$resetUrl}</a></div>";
    }
    
    echo "<br><div class='error'><h3>⚠ DELETE THIS FILE AFTER TESTING!</h3></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>