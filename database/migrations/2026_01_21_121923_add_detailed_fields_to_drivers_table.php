<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Driver Documents
            $table->string('driving_license')->nullable()->after('password');
            $table->date('driving_license_expiry')->nullable()->after('driving_license');
            $table->string('private_hire_drivers_license')->nullable()->after('driving_license_expiry');
            $table->date('private_hire_drivers_license_expiry')->nullable()->after('private_hire_drivers_license');
            $table->string('private_hire_vehicle_insurance')->nullable()->after('private_hire_drivers_license_expiry');
            $table->date('private_hire_vehicle_insurance_expiry')->nullable()->after('private_hire_vehicle_insurance');
            $table->string('private_hire_vehicle_license')->nullable()->after('private_hire_vehicle_insurance_expiry');
            $table->date('private_hire_vehicle_license_expiry')->nullable()->after('private_hire_vehicle_license');
            $table->string('private_hire_vehicle_mot')->nullable()->after('private_hire_vehicle_license_expiry');
            $table->date('private_hire_vehicle_mot_expiry')->nullable()->after('private_hire_vehicle_mot');
            
            // Driver Info
            $table->text('driver_lives')->nullable()->after('private_hire_vehicle_mot_expiry');
            $table->text('driver_address')->nullable()->after('driver_lives');
            $table->string('working_hours')->nullable()->after('driver_address');
            $table->string('bank_name')->nullable()->after('working_hours');
            $table->string('account_title')->nullable()->after('bank_name');
            $table->string('sort_code')->nullable()->after('account_title');
            $table->string('account_number')->nullable()->after('sort_code');
            $table->string('driver_picture')->nullable()->after('account_number');
            
            // Vehicle Info
            $table->unsignedTinyInteger('passenger_capacity')->nullable()->after('driver_picture');
            $table->unsignedTinyInteger('luggage_capacity')->nullable()->after('passenger_capacity');
            $table->string('vehicle_license_number')->nullable()->after('luggage_capacity');
            $table->json('vehicle_pictures')->nullable()->after('vehicle_license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn([
                'driving_license',
                'driving_license_expiry',
                'private_hire_drivers_license',
                'private_hire_drivers_license_expiry',
                'private_hire_vehicle_insurance',
                'private_hire_vehicle_insurance_expiry',
                'private_hire_vehicle_license',
                'private_hire_vehicle_license_expiry',
                'private_hire_vehicle_mot',
                'private_hire_vehicle_mot_expiry',
                'driver_lives',
                'driver_address',
                'working_hours',
                'bank_name',
                'account_title',
                'sort_code',
                'account_number',
                'driver_picture',
                'passenger_capacity',
                'luggage_capacity',
                'vehicle_license_number',
                'vehicle_pictures',
            ]);
        });
    }
};
