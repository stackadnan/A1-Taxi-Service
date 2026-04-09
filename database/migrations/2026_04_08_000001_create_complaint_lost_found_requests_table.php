<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_lost_found_requests', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id')->nullable();
            $table->string('name');
            $table->string('email');
            $table->text('concern');
            $table->text('lost_found');
            $table->string('status', 20)->default('new');
            $table->string('source_ip', 45)->nullable();
            $table->string('source_url', 500)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('booking_id');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_lost_found_requests');
    }
};
