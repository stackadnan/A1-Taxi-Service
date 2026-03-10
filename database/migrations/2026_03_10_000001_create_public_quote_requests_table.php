<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_quote_requests', function (Blueprint $table) {
            $table->id();
            $table->string('quote_ref', 20)->unique();        // e.g. QR123456
            $table->string('pickup_address')->nullable();
            $table->string('dropoff_address')->nullable();
            $table->date('pickup_date')->nullable();
            $table->string('source_ip', 45)->nullable();
            $table->string('source_url')->nullable();
            $table->string('vehicle_type', 50)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('trip_type', 10)->default('one-way'); // one-way | return
            $table->string('linked_quote_ref', 20)->nullable(); // links outbound ↔ return row
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_quote_requests');
    }
};
