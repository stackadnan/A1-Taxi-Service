<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('payment_type')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 5)->default('USD');
            $table->string('status')->default('pending');
            $table->string('method')->nullable();
            $table->string('transaction_id')->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->json('meta')->nullable();
            $table->json('gateway_response')->nullable();

            // indexes for quick lookups
            $table->index(['payment_type']);
            $table->index(['status']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_payments');
    }
};
