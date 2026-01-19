<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_broadcast_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broadcast_id')->constrained('driver_broadcasts')->cascadeOnDelete();
            $table->string('channel_type'); // email, sms, push
            $table->foreignId('template_id')->nullable()->constrained('notification_templates')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['broadcast_id']);
            $table->index(['channel_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_broadcast_channels');
    }
};