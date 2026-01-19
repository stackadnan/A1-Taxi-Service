<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_mileage_charges', function (Blueprint $table) {
            $table->id();
            $table->decimal('start_mile', 8, 2)->default(0);
            $table->decimal('end_mile', 8, 2)->nullable();

            $table->decimal('saloon_price', 10, 2)->nullable();
            $table->decimal('business_price', 10, 2)->nullable();
            $table->decimal('mpv6_price', 10, 2)->nullable();
            $table->decimal('mpv8_price', 10, 2)->nullable();

            $table->boolean('is_fixed_charge')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['start_mile','end_mile']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_mileage_charges');
    }
};