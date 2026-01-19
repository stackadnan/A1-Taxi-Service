<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            // optional link to users table if drivers also have user accounts
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // identity & vehicle
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('license_number')->nullable();
            $table->string('vehicle_make')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_plate')->nullable()->unique();
            $table->string('car_type')->nullable();
            $table->string('car_color')->nullable();
            

            // contact / area
            $table->string('coverage_area')->nullable();
            $table->string('badge_number')->nullable()->unique();
            $table->foreignId('council_id')->nullable()->constrained('councils')->nullOnDelete();
            $table->string('time_slot')->nullable();

            // status & activity
            $table->string('status')->default('active');
            $table->decimal('rating', 3, 2)->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('last_assigned_at')->nullable();

            // counters
            $table->unsignedInteger('total_bookings')->default(0);
            $table->unsignedInteger('total_assigned')->default(0);
            $table->unsignedInteger('total_completed')->default(0);
            $table->unsignedInteger('total_cancelled')->default(0);

            $table->timestamps();

            // useful indexes
            $table->index('status');
            $table->index('last_assigned_at');
            $table->index('coverage_area');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
