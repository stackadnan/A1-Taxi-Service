<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\Role;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingCreationNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionsSeeder::class);
        $this->seed(\Database\Seeders\AdminRoleSeeder::class);
    }

    public function test_public_booking_notifies_all_admin_capable_users(): void
    {
        $superAdmin = User::factory()->create(['email' => 'superadmin@example.com']);
        $superAdminRole = Role::where('name', 'Super Admin')->firstOrFail();
        $superAdmin->roles()->sync([$superAdminRole->id]);

        $manager = User::factory()->create(['email' => 'manager@example.com']);
        $managerRole = Role::where('name', 'Manager')->firstOrFail();
        $manager->roles()->sync([$managerRole->id]);

        $status = BookingStatus::firstOrCreate(
            ['name' => 'new'],
            ['description' => 'New']
        );

        Booking::create([
            'booking_code' => 'CD123456',
            'passenger_name' => 'Frontend Passenger',
            'phone' => '07123456789',
            'pickup_address' => 'Heathrow Airport',
            'dropoff_address' => 'Central London',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => now()->format('H:i'),
            'status_id' => $status->id,
            'meta' => ['trip_leg' => 'single'],
        ]);

        $this->assertSame(1, UserNotification::where('user_id', $superAdmin->id)->count());
        $this->assertSame(1, UserNotification::where('user_id', $manager->id)->count());

        $notification = UserNotification::where('user_id', $superAdmin->id)->firstOrFail();
        $this->assertSame('New Booking Received', $notification->title);
        $this->assertStringContainsString('CD123456', $notification->message);
    }

    public function test_admin_created_booking_does_not_self_notify(): void
    {
        $superAdmin = User::factory()->create(['email' => 'manualadmin@example.com']);
        $superAdminRole = Role::where('name', 'Super Admin')->firstOrFail();
        $superAdmin->roles()->sync([$superAdminRole->id]);

        $status = BookingStatus::firstOrCreate(
            ['name' => 'new'],
            ['description' => 'New']
        );

        Booking::create([
            'booking_code' => 'CD654321',
            'passenger_name' => 'Admin Created Passenger',
            'phone' => '07123450000',
            'pickup_address' => 'Gatwick Airport',
            'dropoff_address' => 'Brighton',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => now()->format('H:i'),
            'status_id' => $status->id,
            'created_by_user_id' => $superAdmin->id,
            'meta' => ['trip_leg' => 'single'],
        ]);

        $this->assertSame(0, UserNotification::where('user_id', $superAdmin->id)->count());
    }
}