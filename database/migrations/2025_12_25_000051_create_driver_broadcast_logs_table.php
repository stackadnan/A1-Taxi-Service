<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_broadcast_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broadcast_id')->constrained('driver_broadcasts')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('council_id')->nullable()->constrained('councils')->nullOnDelete();
            $table->string('channel_type');
            $table->string('recipient_contact')->nullable();
            $table->string('status')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['broadcast_id']);
            $table->index(['driver_id']);
            $table->index(['council_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_broadcast_logs');
    }
};