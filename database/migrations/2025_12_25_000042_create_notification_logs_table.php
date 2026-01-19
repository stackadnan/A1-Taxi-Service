<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('notification_events')->cascadeOnDelete();
            $table->string('recipient_type');
            $table->string('channel_type');
            $table->string('recipient_contact')->nullable();
            $table->string('status')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['event_id']);
            $table->index(['recipient_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};