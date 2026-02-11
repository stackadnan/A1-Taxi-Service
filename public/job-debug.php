<?php
/**
 * JOB ASSIGNMENT DEBUGGER
 * 
 * Checks job assignments and driver authentication
 */

define('JOB_DEBUG_PASSWORD', 'Airport2026JobDebug');

$password = $_GET['password'] ?? '';
if ($password !== JOB_DEBUG_PASSWORD) {
    die('Access denied. Add ?password=Airport2026JobDebug to URL');
}

echo "<!DOCTYPE html><html><head><title>Job Assignment Debugger</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#00ff00;}";
echo ".error{color:#ff0000;}.success{color:#00ff00;}.info{color:#ffff00;}.warning{color:#ff8800;}</style></head><body>";

echo "<h2>Job Assignment & Driver Debug</h2>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // 1. Check available drivers
    echo "<div class='info'><h3>1. Available Drivers:</h3></div>";
    $drivers = \App\Models\Driver::all();
    foreach ($drivers as $driver) {
        echo "<div class='success'>Driver ID: {$driver->id} - Name: {$driver->name} - Email: {$driver->email}</div>";
    }
    echo "<br>";
    
    // 2. Check recent bookings and their assignments
    echo "<div class='info'><h3>2. Recent Bookings & Assignments:</h3></div>";
    $bookings = \App\Models\Booking::orderBy('created_at', 'desc')->limit(10)->get();
    foreach ($bookings as $booking) {
        $driverInfo = $booking->driver_id ? "Assigned to Driver ID: {$booking->driver_id}" : "NOT ASSIGNED";
        $statusInfo = $booking->meta['driver_response'] ?? 'no response';
        
        echo "<div class='info'>Booking #{$booking->id} - {$driverInfo} - Status: {$statusInfo}</div>";
        
        if (!$booking->driver_id) {
            echo "<div class='error'>   ❌ This booking has NO driver assigned!</div>";
        }
    }
    echo "<br>";
    
    // 3. Check if admin is properly assigning jobs
    echo "<div class='info'><h3>3. Job Assignment Process Check:</h3></div>";
    
    $unassignedJobs = \App\Models\Booking::whereNull('driver_id')->count();
    if ($unassignedJobs > 0) {
        echo "<div class='error'>❌ Found {$unassignedJobs} unassigned jobs!</div>";
        echo "<div class='warning'>Problem: Admin is not properly assigning jobs to drivers</div>";
    } else {
        echo "<div class='success'>✓ All jobs have drivers assigned</div>";
    }
    echo "<br>";
    
    // 4. Test current driver authentication (if session exists)
    echo "<div class='info'><h3>4. Current Driver Session:</h3></div>";
    try {
        $currentDriver = \Illuminate\Support\Facades\Auth::guard('driver')->user();
        if ($currentDriver) {
            echo "<div class='success'>✓ Driver logged in: ID {$currentDriver->id} - {$currentDriver->name}</div>";
            
            // Check jobs assigned to this driver
            $assignedJobsCount = \App\Models\Booking::where('driver_id', $currentDriver->id)->count();
            echo "<div class='info'>Jobs assigned to this driver: {$assignedJobsCount}</div>";
            
            $newJobs = \App\Models\Booking::where('driver_id', $currentDriver->id)
                ->whereNull('meta->driver_response')
                ->get();
            
            echo "<div class='info'>New jobs waiting for response: " . $newJobs->count() . "</div>";
            foreach ($newJobs as $job) {
                echo "<div class='success'>  - Job #{$job->id} can be accepted by this driver</div>";
            }
        } else {
            echo "<div class='warning'>⚠ No driver currently logged in</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>Error checking driver session: " . $e->getMessage() . "</div>";
    }
    echo "<br>";
    
    // 5. Fix recommendations
    echo "<div class='warning'><h3>🔧 Fix Recommendations:</h3></div>";
    if ($unassignedJobs > 0) {
        echo "<div class='error'>CRITICAL: Jobs are not being assigned to drivers!</div>";
        echo "<div class='info'>Solutions:</div>";
        echo "<div class='success'>1. Check admin booking creation - ensure driver_id is set</div>";
        echo "<div class='success'>2. Manually assign existing jobs via admin panel</div>";
        echo "<div class='success'>3. Run job assignment fix script</div>";
    } else {
        echo "<div class='info'>Check that:</div>";
        echo "<div class='success'>1. Driver is properly logged in</div>";
        echo "<div class='success'>2. Job is assigned to the correct driver</div>";
        echo "<div class='success'>3. No session conflicts</div>";
    }
    
    echo "<br><div class='error'><h3>⚠ DELETE THIS FILE AFTER DEBUGGING!</h3></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>ERROR: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>