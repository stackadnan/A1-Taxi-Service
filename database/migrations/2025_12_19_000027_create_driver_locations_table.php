<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 8, 3)->nullable();
            $table->timestamps();

            $table->index(['driver_id']);
            $table->index(['updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_locations');
    }
};