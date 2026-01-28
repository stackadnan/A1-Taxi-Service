<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mark driver notifications older than 10 minutes as read to clear backlog
        try {
            DB::table('driver_notifications')
                ->where('is_read', false)
                ->where('created_at', '<', now()->subMinutes(10))
                ->update(['is_read' => true, 'read_at' => now()]);
        } catch (\Exception $e) {
            logger()->warning('Failed to mark old driver notifications as read: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        // No-op: we cannot revert the marking safely
    }
};