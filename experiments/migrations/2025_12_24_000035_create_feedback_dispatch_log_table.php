<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_dispatch_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('feedback_id')->nullable()->constrained('booking_feedback')->nullOnDelete();
            $table->foreignId('rule_id')->nullable()->constrained('feedback_rules')->nullOnDelete();
            $table->enum('message_type', ['email','sms'])->default('email');
            $table->string('sent_to')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->nullable();
            $table->text('gateway_response')->nullable();
            $table->timestamps();

            $table->index(['booking_id']);
            $table->index(['feedback_id']);
            $table->index(['rule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_dispatch_log');
    }
};