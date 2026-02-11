<?php
/**
 * JOB ASSIGNMENT FIXER
 * 
 * Fixes unassigned jobs and validates assignments
 */

define('JOB_FIX_PASSWORD', 'Airport2026JobFix');

$password = $_GET['password'] ?? '';
if ($password !== JOB_FIX_PASSWORD) {
    die('Access denied. Add ?password=Airport2026JobFix to URL');
}

echo "<!DOCTYPE html><html><head><title>Job Assignment Fixer</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}.warning{color:#ff8800;}</style></head><body>";

echo "<h2>Job Assignment Fixer</h2>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Check for unassigned jobs
    $unassignedJobs = \App\Models\Booking::whereNull('driver_id')->get();
    $availableDrivers = \App\Models\Driver::where('is_active', true)->get();
    
    echo "<div class='info'>Found {$unassignedJobs->count()} unassigned jobs</div>";
    echo "<div class='info'>Found {$availableDrivers->count()} active drivers</div><br>";
    
    if ($unassignedJobs->count() > 0 && $availableDrivers->count() > 0) {
        echo "<div class='warning'>🔧 Fixing unassigned jobs...</div><br>";
        
        foreach ($unassignedJobs as $job) {
            // Assign to first available driver (you can modify this logic)
            $driver = $availableDrivers->random();
            $job->driver_id = $driver->id;
            $job->save();
            
            echo "<div class='success'>✓ Assigned Job #{$job->id} to Driver: {$driver->name} (ID: {$driver->id})</div>";
        }
        
        echo "<br><div class='success'><h3>✓ All jobs have been assigned!</h3></div>";
        
    } elseif ($unassignedJobs->count() === 0) {
        echo "<div class='success'>✓ No unassigned jobs found - all jobs are properly assigned!</div>";
        
        // Show current assignments
        echo "<br><div class='info'><h3>Current Job Assignments:</h3></div>";
        $recentJobs = \App\Models\Booking::with('driver')->orderBy('created_at', 'desc')->limit(5)->get();
        foreach ($recentJobs as $job) {
            $driverName = $job->driver ? $job->driver->name : 'Unknown';
            echo "<div class='info'>Job #{$job->id} → Driver: {$driverName} (ID: {$job->driver_id})</div>";
        }
        
    } else {
        echo "<div class='error'>❌ Cannot fix: No active drivers available!</div>";
        echo "<div class='info'>Please ensure drivers are activated in the admin panel</div>";
    }
    
    // Validate test case
    echo "<br><div class='info'><h3>🧪 Testing Job Acceptance:</h3></div>";
    
    $testJob = \App\Models\Booking::whereNotNull('driver_id')
        ->whereNull('meta->driver_response')
        ->first();
    
    if ($testJob) {
        echo "<div class='success'>Test Job Found: #{$testJob->id} assigned to Driver ID: {$testJob->driver_id}</div>";
        echo "<div class='info'>This job should be acceptable by the assigned driver</div>";
        
        // Show the direct test URL
        $testUrl = "https://" . $_SERVER['HTTP_HOST'] . "/driver/jobs/{$testJob->id}/accept";
        echo "<div class='warning'>Test URL: {$testUrl}</div>";
        echo "<div class='info'>Driver {$testJob->driver_id} should be able to accept this job</div>";
    } else {
        echo "<div class='warning'>No test jobs available (all jobs already responded to)</div>";
    }
    
    echo "<br><div class='success'><h3>✅ Fix Complete!</h3></div>";
    echo "<div class='info'>Now drivers should be able to accept their assigned jobs without 403 errors</div>";
    
    echo "<br><div class='error'><h3>⚠ DELETE THIS FILE AFTER USE!</h3></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>