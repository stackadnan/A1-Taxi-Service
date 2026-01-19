<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Broadcast;

class BroadcastSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            ['title' => 'Staff Notice', 'message' => 'Attention team, a staff member is unavailable today. Please adjust shifts as needed.'],
            ['title' => 'System Maintenance', 'message' => 'Scheduled maintenance at midnight. Services may be briefly unavailable.'],
            ['title' => 'New Procedure', 'message' => 'Reminder: new check-in procedure must be followed for all Heathrow pickups.'],
        ];

        foreach ($messages as $m) {
            Broadcast::create(array_merge($m, ['channel' => 'admin']));
        }
    }
}
