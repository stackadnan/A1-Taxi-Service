<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;

class AdminPricingPostcodeTest extends TestCase
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

        $response = $this->actingAs($admin)->get(route('admin.pricing.postcodes.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_postcode()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $response = $this->actingAs($admin)->post(route('admin.pricing.postcodes.store'), [
            'pickup_postcode' => 'AB1 2CD',
            'dropoff_postcode' => 'AB2 3EF',
            'saloon_price' => '12.50',
            'business_price' => '18.00',
            'mpv6_price' => '20.00',
            'mpv8_price' => '22.00',
            'status' => 'active'
        ]);

        $response->assertRedirect(route('admin.pricing.postcodes.index'));
        $this->assertDatabaseHas('pricing_postcode_charges', ['pickup_postcode' => 'AB1 2CD', 'dropoff_postcode' => 'AB2 3EF']);
        // Ensure the reversed direction was also created (AB2 3EF -> AB1 2CD)
        $this->assertDatabaseHas('pricing_postcode_charges', ['pickup_postcode' => 'AB2 3EF', 'dropoff_postcode' => 'AB1 2CD']);
    }

    public function test_user_without_permission_cannot_access_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.pricing.postcodes.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post(route('admin.pricing.postcodes.store'), [
            'pickup_postcode' => 'X', 'dropoff_postcode' => 'Y', 'status' => 'active'
        ]);
        $response->assertStatus(403);
    }

    public function test_updating_postcode_updates_reversed_entry()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        // Create initial A->B
        $response = $this->actingAs($admin)->post(route('admin.pricing.postcodes.store'), [
            'pickup_postcode' => 'AA1 1AA',
            'dropoff_postcode' => 'BB2 2BB',
            'saloon_price' => '5.00',
            'business_price' => '7.00',
            'mpv6_price' => '8.00',
            'mpv8_price' => '9.00',
            'status' => 'active'
        ]);

        $this->assertDatabaseHas('pricing_postcode_charges', ['pickup_postcode' => 'AA1 1AA', 'dropoff_postcode' => 'BB2 2BB']);
        $this->assertDatabaseHas('pricing_postcode_charges', ['pickup_postcode' => 'BB2 2BB', 'dropoff_postcode' => 'AA1 1AA']);

        // Find original and update: change dropoff to CC3 3CC and change price
        $original = \App\Models\PricingPostcodeCharge::where('pickup_postcode','AA1 1AA')->where('dropoff_postcode','BB2 2BB')->first();
        $response = $this->actingAs($admin)->put(route('admin.pricing.postcodes.update', $original), [
            'pickup_postcode' => 'AA1 1AA',
            'dropoff_postcode' => 'CC3 3CC',
            'saloon_price' => '6.00',
            'business_price' => '8.00',
            'mpv6_price' => '9.00',
            'mpv8_price' => '10.00',
            'status' => 'active'
        ]);

        // Old reversed (BB2 2BB -> AA1 1AA) should have been updated to new reversed (CC3 3CC -> AA1 1AA)
        $this->assertDatabaseMissing('pricing_postcode_charges', ['pickup_postcode' => 'BB2 2BB', 'dropoff_postcode' => 'AA1 1AA']);
        $this->assertDatabaseHas('pricing_postcode_charges', ['pickup_postcode' => 'CC3 3CC', 'dropoff_postcode' => 'AA1 1AA', 'saloon_price' => '6.00']);
    }
}
