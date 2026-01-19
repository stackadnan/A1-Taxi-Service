<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;

class AdminPricingMileageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
        $this->seed(\Database\Seeders\AdminRoleSeeder::class);
    }

    public function test_admin_can_view_index()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $response = $this->actingAs($admin)->get(route('admin.pricing.mileage.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_mileage()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $response = $this->actingAs($admin)->post(route('admin.pricing.mileage.store'), [
            'start_mile' => '0',
            'end_mile' => '5',
            'saloon_price' => '5.00',
            'business_price' => '7.50',
            'mpv6_price' => '9.00',
            'mpv8_price' => '10.00',
            'is_fixed_charge' => '0',
            'status' => 'active'
        ]);

        $response->assertRedirect(route('admin.pricing.mileage.index'));
        $this->assertDatabaseHas('pricing_mileage_charges', ['start_mile' => '0']);
    }

    public function test_user_without_permission_cannot_access_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.pricing.mileage.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post(route('admin.pricing.mileage.store'), [
            'start_mile' => '0', 'status' => 'active'
        ]);
        $response->assertStatus(403);
    }

    public function test_admin_can_delete_mileage()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $mileageId = \App\Models\PricingMileageCharge::create([
            'start_mile' => '10', 'end_mile' => '20', 'saloon_price' => '10.00', 'status' => 'active'
        ])->id;

        $response = $this->actingAs($admin)->deleteJson(route('admin.pricing.mileage.destroy', $mileageId));
        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseMissing('pricing_mileage_charges', ['id' => $mileageId]);
    }

    public function test_cannot_create_more_than_ten_mileage_entries()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        // create 10 entries
        for ($i = 0; $i < 10; $i++) {
            \App\Models\PricingMileageCharge::create([
                'start_mile' => (string)($i * 10),
                'end_mile' => (string)(($i + 1) * 10),
                'saloon_price' => '5.00',
                'status' => 'active'
            ]);
        }

        // attempt to add the 11th
        $response = $this->actingAs($admin)->post(route('admin.pricing.mileage.store'), [
            'start_mile' => '100', 'end_mile' => '110', 'saloon_price' => '5.00', 'status' => 'active'
        ]);

        // Expect redirect with error message (non-AJAX)
        $response->assertRedirect(route('admin.pricing.mileage.index'));
        $this->assertDatabaseMissing('pricing_mileage_charges', ['start_mile' => '100', 'end_mile' => '110']);
    }

    public function test_cannot_create_overlapping_mileage_ranges()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        // existing range 0 - 10.99
        \App\Models\PricingMileageCharge::create([
            'start_mile' => '0', 'end_mile' => '10.99', 'saloon_price' => '5.00', 'status' => 'active'
        ]);

        // overlapping: start 10 (falls into 0-10.99) -> should be rejected
        $response = $this->actingAs($admin)->post(route('admin.pricing.mileage.store'), [
            'start_mile' => '10', 'end_mile' => '20', 'saloon_price' => '6.00', 'status' => 'active'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('pricing_mileage_charges', ['start_mile' => '10', 'end_mile' => '20']);

        // non-overlapping: start 11 -> should be accepted
        $response = $this->actingAs($admin)->post(route('admin.pricing.mileage.store'), [
            'start_mile' => '11', 'end_mile' => '20', 'saloon_price' => '6.00', 'status' => 'active'
        ]);

        $response->assertRedirect(route('admin.pricing.mileage.index'));
        $this->assertDatabaseHas('pricing_mileage_charges', ['start_mile' => '11', 'end_mile' => '20']);
    }
}
