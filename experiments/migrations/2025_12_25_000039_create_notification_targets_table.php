<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('notification_events')->cascadeOnDelete();
            $table->string('recipient_type'); // e.g., user, driver, role
            $table->string('recipient_identifier')->nullable(); // e.g., role name or custom identifier
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->index(['event_id']);
            $table->index(['recipient_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_targets');
    }
};