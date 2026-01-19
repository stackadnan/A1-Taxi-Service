<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->nullable(); // 1-5
            $table->enum('experience_type', ['positive','negative'])->nullable();
            $table->text('comments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['booking_id']);
            $table->index(['rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_feedback');
    }
};