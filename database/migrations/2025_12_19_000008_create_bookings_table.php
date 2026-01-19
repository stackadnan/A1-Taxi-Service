<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // basic identifiers
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('booking_statuses')->cascadeOnDelete();

            // payment references
            $table->string('payment_type')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            // No foreign key constraint since booking_payments table doesn't exist

            // passenger details
            $table->string('passenger_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedTinyInteger('passengers_count')->default(1);
            $table->unsignedTinyInteger('luggage_count')->default(0);

            // addresses & scheduling
            $table->string('pickup_address')->nullable();
            $table->string('dropoff_address')->nullable();
            $table->date('pickup_date')->nullable();
            $table->time('pickup_time')->nullable();
            $table->timestamp('scheduled_at')->nullable();

            // flight / extras
            $table->string('flight_number')->nullable();
            $table->timestamp('flight_arrival_time')->nullable();
            $table->boolean('meet_and_greet')->default(false);
            $table->boolean('baby_seat')->default(false);
            $table->string('vehicle_type')->nullable();

            // pricing & driver
            $table->decimal('total_price', 10, 2)->nullable();
            $table->decimal('driver_price', 10, 2)->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            // No foreign key constraint - driver_profiles table exists but named differently
            $table->string('driver_name')->nullable();

            // return booking
            $table->boolean('return_booking')->default(false);
            // self-referencing FK (set to null if referenced booking is deleted)
            $table->foreignId('return_booking_id')->nullable()->constrained('bookings')->nullOnDelete();

            // messages & metadata
            $table->text('message_to_driver')->nullable();
            $table->text('message_to_admin')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source_ip', 45)->nullable();

            // audit
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('handled_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            // indexes for commonly queried audit fields
            $table->index('created_by_user_id');
            $table->index('handled_by_user_id');

            // estimates and misc
            $table->decimal('estimated_distance_km', 8, 2)->nullable();
            $table->integer('estimated_duration_minutes')->nullable();

            $table->json('meta')->nullable();
            $table->string('currency', 5)->default('USD');
            $table->timestamps();

            $table->index(['pickup_date', 'pickup_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
