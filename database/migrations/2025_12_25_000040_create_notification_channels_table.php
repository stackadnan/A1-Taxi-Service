<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_id')->constrained('notification_targets')->cascadeOnDelete();
            $table->string('channel_type'); // email, sms, push
            $table->string('configuration_key')->nullable(); // e.g., email template or SMS provider id
            $table->boolean('enabled')->default(true);
            $table->unsignedTinyInteger('priority')->default(1);
            $table->timestamps();

            $table->index(['target_id']);
            $table->index(['channel_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_channels');
    }
};