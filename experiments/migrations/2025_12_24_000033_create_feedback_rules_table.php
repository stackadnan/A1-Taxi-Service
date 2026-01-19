<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('trigger_type', ['positive','negative'])->default('negative');
            $table->unsignedTinyInteger('min_rating')->nullable();
            $table->unsignedTinyInteger('max_rating')->nullable();
            $table->enum('response_type', ['apology','review_link','custom'])->default('review_link');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['trigger_type']);
            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_rules');
    }
};