<?php

namespace Database\Seeders;

use App\Models\Zone;
use App\Models\PricingZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportZonePricesFromCsvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Expects per-airport CSVs in storage/app/zone_prices/*.csv
     * CSV columns: zone_name,saloon_price,business_price,mpv6_price,mpv8_price
     */
    public function run()
    {
        $dir = storage_path('app/zone_prices');
        if (! is_dir($dir)) {
            $this->command->warn("Directory not found: $dir — export CSVs first (see tools/export_zone_prices_csv.py)");
            return;
        }

        $files = glob($dir . '/*.csv');
        foreach ($files as $file) {
            $airport = pathinfo($file, PATHINFO_FILENAME);

            $fromZone = Zone::whereRaw('LOWER(zone_name) = ?', [strtolower($airport)])->first();
            if (! $fromZone) {
                // permissive fallback
                $fromZone = Zone::where('zone_name', 'like', "%{$airport}%")->first();
            }

            if (! $fromZone) {
                $this->command->warn("No Zone row found for airport '{$airport}' — skipping file: {$file}");
                continue;
            }

            $this->command->info("Importing prices for airport: {$airport} (zone_id={$fromZone->id})");

            if (($handle = fopen($file, 'r')) === false) {
                $this->command->error("Could not open {$file}");
                continue;
            }

            $header = fgetcsv($handle);
            $map = array_change_key_case(array_flip($header));

            $count = 0;
            DB::beginTransaction();
            try {
                while (($row = fgetcsv($handle)) !== false) {
                    $zoneName = trim($row[$map['zone_name']] ?? '');
                    if (! $zoneName) continue;

                    // try exact match first, then LIKE
                    $toZone = Zone::whereRaw('LOWER(zone_name) = ?', [strtolower($zoneName)])->first();
                    if (! $toZone) {
                        $toZone = Zone::where('zone_name', 'like', "%{$zoneName}%")->first();
                    }

                    if (! $toZone) {
                        $this->command->warn("Destination zone not found: '{$zoneName}' — skipping");
                        continue;
                    }

                    $data = [
                        'saloon_price' => $row[$map['saloon_price']] ?? null,
                        'business_price' => $row[$map['business_price']] ?? null,
                        'mpv6_price' => $row[$map['mpv6_price']] ?? null,
                        'mpv8_price' => $row[$map['mpv8_price']] ?? null,
                        'status' => 1,
                    ];

                    PricingZone::updateOrCreate([
                        'from_zone_id' => $fromZone->id,
                        'to_zone_id' => $toZone->id,
                    ], $data);

                    $count++;
                }
                DB::commit();
            } catch (\Exception $ex) {
                DB::rollBack();
                $this->command->error('Import failed for ' . $airport . ': ' . $ex->getMessage());
                fclose($handle);
                continue;
            }

            fclose($handle);
            $this->command->info("Imported {$count} pricing rows for airport {$airport}\n");
        }

        $this->command->info('Seeder finished.');
    }
}
