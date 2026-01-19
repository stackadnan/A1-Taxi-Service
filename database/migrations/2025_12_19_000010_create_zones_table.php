<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('zone_name')->unique();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('code')->nullable();
            $table->string('status')->default('active');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['latitude','longitude']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
