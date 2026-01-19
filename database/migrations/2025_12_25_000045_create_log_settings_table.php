<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_settings', function (Blueprint $table) {
            $table->id();
            $table->string('log_level')->default('debug');
            $table->unsignedInteger('retention_days')->default(30);
            $table->string('store_location')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_settings');
    }
};