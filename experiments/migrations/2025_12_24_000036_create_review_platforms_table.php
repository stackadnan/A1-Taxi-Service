<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_platforms', function (Blueprint $table) {
            $table->id();
            $table->string('platform_name');
            $table->string('review_url')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['platform_name']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_platforms');
    }
};