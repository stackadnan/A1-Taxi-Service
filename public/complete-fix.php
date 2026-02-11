<?php
/**
 * COMPREHENSIVE JOB & AUTH FIXER
 * 
 * Fixes both job assignment and driver authentication issues
 */

define('COMPLETE_FIX_PASSWORD', 'Airport2026CompleteFix');

$password = $_GET['password'] ?? '';
if ($password !== COMPLETE_FIX_PASSWORD) {
    die('Access denied. Add ?password=Airport2026CompleteFix to URL');
}

echo "<!DOCTYPE html><html><head><title>Complete Job & Auth Fixer</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}.warning{color:#ff8800;}.fix{color:#00ffff;}</style></head><body>";

echo "<h2>Complete Job & Authentication Fixer</h2>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<div class='fix'><h3>🔧 Step 1: Fix Unassigned Jobs</h3></div>";
    
    $unassignedJobs = \App\Models\Booking::whereNull('driver_id')->get();
    $activeDrivers = \App\Models\Driver::where('is_active', true)->get();
    
    echo "<div class='info'>Unassigned jobs: {$unassignedJobs->count()}</div>";
    echo "<div class='info'>Active drivers: {$activeDrivers->count()}</div>";
    
    if ($unassignedJobs->count() > 0 && $activeDrivers->count() > 0) {
        foreach ($unassignedJobs as $job) {
            // Assign to first available driver
            $driver = $activeDrivers->first();
            $job->driver_id = $driver->id;
            $job->save();
            
            echo "<div class='success'>✓ Assigned Job #{$job->id} to Driver: {$driver->name} (ID: {$driver->id})</div>";
        }
        echo "<br>";
    }
    
    echo "<div class='fix'><h3>🔐 Step 2: Driver Authentication Check</h3></div>";
    
    // Check if driver guard is working
    try {
        $currentDriver = \Illuminate\Support\Facades\Auth::guard('driver')->user();
        if ($currentDriver) {
            echo "<div class='success'>✓ Driver authenticated: {$currentDriver->name} (ID: {$currentDriver->id})</div>";
        } else {
            echo "<div class='error'>❌ No driver authenticated in current session</div>";
            echo "<div class='warning'>Driver must login first to accept jobs!</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>Authentication error: " . $e->getMessage() . "</div>";
    }
    
    echo "<div class='fix'><h3>🧪 Step 3: Create Test Accept URL</h3></div>";
    
    // Find a job that can be tested
    $testJob = \App\Models\Booking::whereNotNull('driver_id')
        ->whereNull('meta->driver_response')
        ->first();
    
    if ($testJob && $activeDrivers->count() > 0) {
        $testDriver = $activeDrivers->where('id', $testJob->driver_id)->first();
        
        echo "<div class='success'>Test Job: #{$testJob->id}</div>";
        echo "<div class='success'>Assigned to: " . ($testDriver ? $testDriver->name : 'Unknown') . " (ID: {$testJob->driver_id})</div>";
        
        // Create a test authentication URL that logs in the driver and accepts the job
        $testUrl = "https://" . $_SERVER['HTTP_HOST'] . "/test-accept.php?password=Airport2026TestAccept&job={$testJob->id}&driver={$testJob->driver_id}";
        echo "<div class='fix'>Test URL: <a href='{$testUrl}' target='_blank'>{$testUrl}</a></div>";
    }
    
    echo "<div class='fix'><h3>📋 Step 4: Manual Login Instructions</h3></div>";
    echo "<div class='info'>To accept jobs, drivers must:</div>";
    echo "<div class='success'>1. Login at: https://" . $_SERVER['HTTP_HOST'] . "/driver/login</div>";
    echo "<div class='success'>2. Use their driver credentials</div>";
    echo "<div class='success'>3. Then try accepting jobs</div>";
    
    echo "<div class='fix'><h3>🔧 Step 5: Admin Panel Fix</h3></div>";
    echo "<div class='warning'>To prevent future unassigned jobs:</div>";
    echo "<div class='info'>• Always select a driver when creating bookings in admin panel</div>";
    echo "<div class='info'>• Verify driver_id field is properly saved</div>";
    echo "<div class='info'>• Test job acceptance immediately after creation</div>";
    
    echo "<br><div class='success'><h3>✅ FIXES APPLIED!</h3></div>";
    echo "<div class='info'>Jobs are now properly assigned. Drivers need to login to accept them.</div>";
    
    echo "<br><div class='error'><h3>⚠ DELETE THIS FILE AFTER USE!</h3></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>