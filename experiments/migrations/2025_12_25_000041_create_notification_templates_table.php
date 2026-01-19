<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('notification_events')->cascadeOnDelete();
            $table->string('recipient_type');
            $table->string('channel_type');
            $table->string('subject')->nullable();
            $table->text('message_body')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['event_id']);
            $table->index(['channel_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};