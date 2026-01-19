<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('service_name')->nullable();
            $table->string('base_url')->nullable();
            $table->string('auth_type')->nullable();
            $table->string('api_key')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['service_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_settings');
    }
};