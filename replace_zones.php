<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$csvPath = base_path('zones.csv');
$fh = fopen($csvPath, 'r');
$header = fgetcsv($fh);

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('zone_points')->truncate();
DB::table('zones')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$inserted = 0;

while (($row = fgetcsv($fh)) !== false) {
    if (count($header) !== count($row)) {
        continue;
    }
    $assoc = array_combine($header, $row);
    
    $meta = $assoc['meta'] ?? null;
    if ($meta === 'NULL' || empty($meta)) {
        $meta = null;
    }
    
    $metaDecoded = $meta ? json_decode($meta, true) : null;
    
    DB::table('zones')->insert([
        'id' => $assoc['id'],
        'zone_name' => $assoc['zone_name'],
        'latitude' => $assoc['latitude'] === 'NULL' ? null : $assoc['latitude'],
        'longitude' => $assoc['longitude'] === 'NULL' ? null : $assoc['longitude'],
        'code' => $assoc['code'] === 'NULL' ? null : $assoc['code'],
        'status' => $assoc['status'] === 'NULL' ? 'active' : $assoc['status'],
        'meta' => $meta,
        'created_at' => $assoc['created_at'] === 'NULL' ? now() : $assoc['created_at'],
        'updated_at' => $assoc['updated_at'] === 'NULL' ? now() : $assoc['updated_at'],
    ]);

    if ($metaDecoded && isset($metaDecoded['polygon']['coordinates'][0])) {
        $points = [];
        foreach ($metaDecoded['polygon']['coordinates'][0] as $i => $pt) {
            $points[] = [
                'zone_id' => $assoc['id'],
                'latitude' => (float)$pt[1],
                'longitude' => (float)$pt[0],
                'point_order' => $i + 1,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        foreach (array_chunk($points, 500) as $chunk) {
            DB::table('zone_points')->insert($chunk);
        }
    }

    $inserted++;
}

fclose($fh);
echo "Successfully replaced $inserted zones and their points.\n";
