<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('driver_notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            }
        });

        // Backfill read_at for any already marked as read
        try {
            \DB::table('driver_notifications')
                ->where('is_read', true)
                ->update(['read_at' => now()]);
        } catch (\Exception $e) {
            // ignore failures during migration backfill
            logger()->warning('Failed to backfill driver_notifications.read_at: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        Schema::table('driver_notifications', function (Blueprint $table) {
            if (Schema::hasColumn('driver_notifications', 'read_at')) {
                $table->dropColumn('read_at');
            }
        });
    }
};