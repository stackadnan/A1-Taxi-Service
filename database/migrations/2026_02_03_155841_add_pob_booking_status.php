<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if POB status already exists
        $exists = DB::table('booking_statuses')->where('name', 'pob')->exists();
        
        if (!$exists) {
            DB::table('booking_statuses')->insert([
                'name' => 'pob',
                'description' => 'Proof of Business',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('booking_statuses')->where('name', 'pob')->delete();
    }
};
