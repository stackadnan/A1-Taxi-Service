<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Driver;
use Illuminate\Support\Facades\DB;

// Get active drivers
$drivers = Driver::where('status', 'active')->limit(5)->get();

echo "Found " . $drivers->count() . " active drivers\n";

foreach ($drivers as $driver) {
    // Create varied but realistic locations around London
    $baseLatitude = 51.5074;
    $baseLongitude = -0.1278;
    
    // Vary location based on driver ID to get consistent but different locations
    $latOffset = (($driver->id % 20) - 10) * 0.01; // ±0.1 degrees (about 11km range)
    $lngOffset = (($driver->id % 20) - 10) * 0.01;
    
    $latitude = $baseLatitude + $latOffset;
    $longitude = $baseLongitude + $lngOffset;
    
    // Insert or update driver location
    DB::table('driver_locations')->updateOrInsert(
        ['driver_id' => $driver->id],
        [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => 15.0,
            'created_at' => now(),
            'updated_at' => now()
        ]
    );
    
    echo "Added location for driver {$driver->id} ({$driver->name}): [{$latitude}, {$longitude}]\n";
}

echo "\nSample driver locations added successfully!\n";
echo "You can now test the tracking system.\n";