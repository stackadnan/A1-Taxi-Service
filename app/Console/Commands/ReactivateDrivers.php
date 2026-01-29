<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Driver;
use Illuminate\Log\LogManager;
use Carbon\Carbon;

class ReactivateDrivers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drivers:reactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reactivate drivers whose unavailable_to has passed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $drivers = Driver::where('status', 'inactive')
            ->whereNotNull('unavailable_to')
            ->where('unavailable_to', '<=', $now)
            ->get();

        if ($drivers->isEmpty()) {
            $this->info('No drivers to reactivate');
            return 0;
        }

        foreach ($drivers as $driver) {
            $driver->status = 'active';
            $driver->unavailable_from = null;
            $driver->unavailable_to = null;
            $driver->save();
            $this->info("Reactivated driver id={$driver->id} ({$driver->name})");
        }

        return 0;
    }
}
