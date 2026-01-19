<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_postcode_charges', function (Blueprint $table) {
            $table->id();
            $table->string('pickup_postcode');
            $table->string('dropoff_postcode');

            $table->decimal('saloon_price', 10, 2)->nullable();
            $table->decimal('business_price', 10, 2)->nullable();
            $table->decimal('mpv6_price', 10, 2)->nullable();
            $table->decimal('mpv8_price', 10, 2)->nullable();

            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['pickup_postcode','dropoff_postcode']);
            $table->index(['pickup_postcode']);
            $table->index(['dropoff_postcode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_postcode_charges');
    }
};