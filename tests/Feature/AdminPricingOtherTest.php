<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\PricingAddonCharge as OtherCharge;

class AdminPricingOtherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
        $this->seed(\Database\Seeders\AdminRoleSeeder::class);
    }

    public function test_admin_can_view_other_index()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $response = $this->actingAs($admin)->get(route('admin.pricing.others.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_other_charge()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $response = $this->actingAs($admin)->post(route('admin.pricing.others.store'), [
            'charge_name' => 'Luggage Fee',
            'charge_type' => 'flat',
            'charge_value' => '5.00',
            'status' => 'active'
        ]);

        $response->assertRedirect(route('admin.pricing.others.index'));
        $this->assertDatabaseHas('pricing_addon_charges', ['charge_name' => 'Luggage Fee']);
    }

    public function test_user_without_permission_cannot_access_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.pricing.others.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post(route('admin.pricing.others.store'), ['name' => 'X', 'status' => 'active']);
        $response->assertStatus(403);
    }

    public function test_admin_can_delete_other_charge()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $o = OtherCharge::factory()->create();

        $response = $this->actingAs($admin)->deleteJson(route('admin.pricing.others.destroy', $o->id));
        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseMissing('pricing_addon_charges', ['id' => $o->id]);
    }
}
