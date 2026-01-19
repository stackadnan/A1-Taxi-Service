<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed common statuses
        DB::table('booking_statuses')->insert([
            ['name' => 'new', 'description' => 'New booking', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'confirmed', 'description' => 'Confirmed', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'in_progress', 'description' => 'In progress', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'completed', 'description' => 'Completed', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'cancelled', 'description' => 'Cancelled', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_statuses');
    }
};
