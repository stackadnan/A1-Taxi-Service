<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pricing_mileage_charges')) {
            return;
        }

        // Skip automatic seeding during unit tests to avoid altering test expectations
        if (app()->environment('testing')) {
            return;
        }

        $ranges = [
            ['start' => 0.00,  'end' => 5.99,  'saloon' => 5.99,  'business' => 7.00,  'mpv6' => 9.00,  'mpv8' => 10.00,  'is_fixed' => true],
            ['start' => 6.00,  'end' => 10.99, 'saloon' => 10.99, 'business' => 33.00,  'mpv6' => 44.00,  'mpv8' => 33.00,  'is_fixed' => true],
            ['start' => 11.00, 'end' => 15.99, 'saloon' => 15.99, 'business' => 12.00,  'mpv6' => 20.00,  'mpv8' => 30.00,  'is_fixed' => false],
            ['start' => 16.00, 'end' => 20.99, 'saloon' => 20.99, 'business' => 55.00,  'mpv6' => 55.00,  'mpv8' => 55.00,  'is_fixed' => false],
            ['start' => 21.00, 'end' => 25.99, 'saloon' => 25.99, 'business' => 39.00,  'mpv6' => 33.00,  'mpv8' => 33.00,  'is_fixed' => false],
            ['start' => 26.00, 'end' => 30.99, 'saloon' => 30.99, 'business' => 11.00,  'mpv6' => 11.00,  'mpv8' => 11.00,  'is_fixed' => false],
            ['start' => 31.00, 'end' => 35.99, 'saloon' => 35.99, 'business' => 22.00,  'mpv6' => 33.00,  'mpv8' => 22.00,  'is_fixed' => false],
            ['start' => 36.00, 'end' => 40.99, 'saloon' => 40.99, 'business' => 22.00,  'mpv6' => 33.00,  'mpv8' => 22.00,  'is_fixed' => false],
            ['start' => 41.00, 'end' => 45.99, 'saloon' => 45.99, 'business' => 22.00,  'mpv6' => 33.00,  'mpv8' => 22.00,  'is_fixed' => false],
            // open-ended range
            ['start' => 46.00, 'end' => null,  'saloon' => 46.00, 'business' => 1000.00,'mpv6' => 32.00,  'mpv8' => 334.00, 'is_fixed' => false],
        ];

        foreach ($ranges as $r) {
            // check existing by start and end (null end handled separately)
            $exists = DB::table('pricing_mileage_charges')
                ->where('start_mile', $r['start'])
                ->when($r['end'] === null, function($q){ return $q->whereNull('end_mile'); })
                ->when($r['end'] !== null, function($q) use ($r){ return $q->where('end_mile', $r['end']); })
                ->exists();

            if ($exists) continue;

            $row = [
                'start_mile' => $r['start'],
                'end_mile' => $r['end'],
                'saloon_price' => $r['saloon'],
                'business_price' => $r['business'],
                'mpv6_price' => $r['mpv6'],
                'mpv8_price' => $r['mpv8'],
                'is_fixed_charge' => $r['is_fixed'],
                'status' => Schema::hasColumn('pricing_mileage_charges','status') ? 'active' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Remove nulls for compatibility
            $row = array_filter($row, function($v){ return !is_null($v); });

            DB::table('pricing_mileage_charges')->insert($row);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('pricing_mileage_charges')) {
            return;
        }

        $starts = [0.00,6.00,11.00,16.00,21.00,26.00,31.00,36.00,41.00,46.00];
        DB::table('pricing_mileage_charges')->whereIn('start_mile', $starts)->delete();
    }
};
