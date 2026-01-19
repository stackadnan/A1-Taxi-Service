<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_zone_id')->constrained('zones')->cascadeOnDelete();
            $table->foreignId('to_zone_id')->constrained('zones')->cascadeOnDelete();

            // prices by vehicle type
            $table->decimal('saloon_price', 10, 2)->nullable();
            $table->decimal('business_price', 10, 2)->nullable();
            $table->decimal('mpv6_price', 10, 2)->nullable();
            $table->decimal('mpv8_price', 10, 2)->nullable();

            $table->string('pricing_mode')->nullable(); // e.g., distance, flat, zone
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['from_zone_id', 'to_zone_id']);
            $table->index(['from_zone_id']);
            $table->index(['to_zone_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_zones');
    }
};
