<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_addon_charges', function (Blueprint $table) {
            $table->id();
            $table->string('charge_name');
            $table->string('vehicle_type')->nullable();
            $table->string('charge_type')->nullable(); // flat or percentage
            $table->decimal('charge_value', 10, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['vehicle_type']);
            $table->index(['charge_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_addon_charges');
    }
};