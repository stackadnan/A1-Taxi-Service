<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\DriverNotification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverBroadcastMessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionsSeeder::class);
        $this->seed(\Database\Seeders\AdminRoleSeeder::class);
    }

    public function test_admin_can_send_driver_broadcast_to_all_drivers(): void
    {
        $admin = User::factory()->create();
        $superAdminRole = Role::where('name', 'Super Admin')->firstOrFail();
        $admin->roles()->sync([$superAdminRole->id]);

        $driverA = Driver::create([
            'name' => 'Driver A',
            'phone' => '07000000001',
            'status' => 'active',
        ]);

        $driverB = Driver::create([
            'name' => 'Driver B',
            'phone' => '07000000002',
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.driver-broadcasts.store'), [
            'title' => 'Road Update',
            'message' => 'Please expect delays around terminal pickup area.',
            'broadcast_type' => 'alert',
        ]);

        $response->assertRedirect(route('admin.driver-broadcasts.index'));

        $this->assertDatabaseHas('driver_broadcasts', [
            'title' => 'Road Update',
            'broadcast_type' => 'alert',
            'status' => 'sent',
            'created_by' => $admin->id,
        ]);

        $this->assertSame(2, DriverNotification::count());

        $this->assertDatabaseHas('driver_notifications', [
            'driver_id' => $driverA->id,
            'title' => 'Road Update',
        ]);

        $this->assertDatabaseHas('driver_notifications', [
            'driver_id' => $driverB->id,
            'title' => 'Road Update',
        ]);
    }
}
