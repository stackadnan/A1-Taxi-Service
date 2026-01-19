<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Zone;
use App\Models\PricingZone;

class AdminPricingZoneTest extends TestCase
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

        $response = $this->actingAs($admin)->get(route('admin.pricing.zones.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_zone_pricing()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $from = Zone::factory()->create();
        $to = Zone::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.pricing.zones.store'), [
            'from_zone_id' => $from->id,
            'to_zone_id' => $to->id,
            'saloon_price' => '10.00',
            'status' => 'active'
        ]);

        $response->assertRedirect(route('admin.pricing.zones.index'));
        $this->assertDatabaseHas('pricing_zones', ['from_zone_id' => $from->id, 'to_zone_id' => $to->id]);
    }

    public function test_user_without_permission_cannot_access_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.pricing.zones.create'));
        $response->assertStatus(403);

        $from = Zone::factory()->create();
        $to = Zone::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.pricing.zones.store'), [
            'from_zone_id' => $from->id, 'to_zone_id' => $to->id, 'status' => 'active'
        ]);
        $response->assertStatus(403);
    }

    public function test_admin_can_delete_zone_pricing()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $pz = PricingZone::factory()->create();

        $response = $this->actingAs($admin)->deleteJson(route('admin.pricing.zones.destroy', $pz->id));
        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseMissing('pricing_zones', ['id' => $pz->id]);
    }

    public function test_creating_pricing_also_creates_reverse()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $a = Zone::factory()->create();
        $b = Zone::factory()->create();

        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.store'), [
            'from_zone_id' => $a->id,
            'to_zone_id' => $b->id,
            'saloon_price' => 15.00,
            'status' => 'active'
        ]);

        $response->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('pricing_zones', ['from_zone_id' => $a->id, 'to_zone_id' => $b->id, 'saloon_price' => 15.00]);
        $this->assertDatabaseHas('pricing_zones', ['from_zone_id' => $b->id, 'to_zone_id' => $a->id, 'saloon_price' => 15.00]);
    }

    public function test_same_zone_allowed_and_single_row_saved()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $z = Zone::factory()->create();

        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.store'), [
            'from_zone_id' => $z->id,
            'to_zone_id' => $z->id,
            'saloon_price' => 9.00,
            'status' => 'active'
        ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('pricing_zones', ['from_zone_id' => $z->id, 'to_zone_id' => $z->id, 'saloon_price' => 9.00]);
        $count = \App\Models\PricingZone::where('from_zone_id', $z->id)->where('to_zone_id', $z->id)->count();
        $this->assertEquals(1, $count);
    }

    public function test_updating_pricing_does_not_update_reverse()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $a = Zone::factory()->create();
        $b = Zone::factory()->create();

        // create both directions initially
        $pz = PricingZone::factory()->create(['from_zone_id' => $a->id, 'to_zone_id' => $b->id, 'saloon_price' => 10.00]);
        $reverse = PricingZone::factory()->create(['from_zone_id' => $b->id, 'to_zone_id' => $a->id, 'saloon_price' => 10.00]);

        $response = $this->actingAs($admin)->putJson(route('admin.pricing.zones.update', $pz->id), [
            'from_zone_id' => $a->id,
            'to_zone_id' => $b->id,
            'saloon_price' => 22.50,
            'status' => 'active'
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('pricing_zones', ['id' => $pz->id, 'saloon_price' => 22.50]);
        // reverse should still have old price
        $this->assertDatabaseHas('pricing_zones', ['id' => $reverse->id, 'saloon_price' => 10.00]);
    }

    public function test_admin_can_create_zone_on_map()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $polygon = json_encode(['type' => 'Polygon', 'coordinates' => [[[-0.1,51.5],[0.1,51.5],[0.1,51.6],[-0.1,51.6],[-0.1,51.5]]]]);

        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.store_map'), [
            'zone_name' => 'Map Zone',
            'polygon' => $polygon
        ]);

        $response->assertStatus(201)->assertJson(['success' => true])->assertJsonStructure(['option_html','item']);

        $this->assertDatabaseHas('zones', ['zone_name' => 'Map Zone']);
        $zone = \App\Models\Zone::where('zone_name', 'Map Zone')->first();
        $this->assertNotNull($zone->meta['polygon']);
    }

    public function test_updating_postcode_does_not_update_reverse()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $pickup = 'AA1 1AA';
        $drop = 'BB2 2BB';

        $original = \App\Models\PricingPostcodeCharge::create([
            'pickup_postcode' => $pickup,
            'dropoff_postcode' => $drop,
            'saloon_price' => 10.00,
            'status' => 'active'
        ]);

        // create reverse manually
        $reverse = \App\Models\PricingPostcodeCharge::create([
            'pickup_postcode' => $drop,
            'dropoff_postcode' => $pickup,
            'saloon_price' => 10.00,
            'status' => 'active'
        ]);

        // update the original
        $response = $this->actingAs($admin)->putJson(route('admin.pricing.postcodes.update', $original->id), [
            'pickup_postcode' => $pickup,
            'dropoff_postcode' => $drop,
            'saloon_price' => 30.00,
            'status' => 'active'
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('pricing_postcode_charges', ['id' => $original->id, 'saloon_price' => 30.00]);
        // reverse should remain 10.00
        $this->assertDatabaseHas('pricing_postcode_charges', ['id' => $reverse->id, 'saloon_price' => 10.00]);
    }

    public function test_admin_can_quote_by_postcode_fallback()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $pickup = 'AA1 1AA';
        $drop = 'BB2 2BB';

        $pc = \App\Models\PricingPostcodeCharge::create([
            'pickup_postcode' => $pickup,
            'dropoff_postcode' => $drop,
            'saloon_price' => 18.50,
            'business_price' => 25.00,
            'status' => 'active'
        ]);

        // pass lat/lon that are outside any zones (or we don't care), but include postcodes
        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.quote'), [
            'pickup_lat' => 0.0, 'pickup_lon' => 0.0, 'dropoff_lat' => 0.0, 'dropoff_lon' => 0.0,
            'pickup_postcode' => $pickup, 'dropoff_postcode' => $drop, 'vehicle_type' => 'saloon'
        ]);

        $response->assertStatus(200)->assertJson(['success' => true, 'pricing' => ['id' => $pc->id]]);
        $this->assertEquals(18.50, floatval($response->json('pricing.selected_price')));
    }

    public function test_admin_can_edit_zone_polygon()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $z = Zone::factory()->create(['zone_name' => 'Original']);
        $polygon = json_encode(['type' => 'Polygon', 'coordinates' => [[[-0.1,51.5],[0.1,51.5],[0.1,51.6],[-0.1,51.6],[-0.1,51.5]]]]);

        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.update_map', $z->id), [
            'zone_name' => 'Edited',
            'polygon' => $polygon
        ]);

        $response->assertOk()->assertJson(['success' => true])->assertJsonStructure(['option_html','item']);
        $this->assertDatabaseHas('zones', ['id' => $z->id, 'zone_name' => 'Edited']);
        $z->refresh();
        $this->assertNotEmpty($z->meta['polygon']);
    }

    public function test_create_duplicate_zone_returns_validation_error()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        Zone::factory()->create(['zone_name' => 'DupZone']);

        $polygon = json_encode(['type' => 'Polygon', 'coordinates' => [[[0,0],[1,0],[1,1],[0,1],[0,0]]]]);

        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.store_map'), [
            'zone_name' => 'DupZone',
            'polygon' => $polygon
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message','errors' => ['zone_name']]);
    }

    public function test_admin_can_lookup_zone_by_point()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $polygon = ['type' => 'Polygon', 'coordinates' => [[[-0.1,51.5],[0.1,51.5],[0.1,51.6],[-0.1,51.6],[-0.1,51.5]]]];
        $z = Zone::factory()->create(['zone_name' => 'LookupZone', 'meta' => ['polygon' => $polygon]]);

        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.lookup'), ['lat' => 51.55, 'lon' => 0]);
        $response->assertStatus(200)->assertJson(['success' => true, 'zone' => ['id' => $z->id, 'zone_name' => 'LookupZone']]);
    }

    public function test_admin_can_quote_by_zones()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $polyA = ['type' => 'Polygon', 'coordinates' => [[[-0.2,51.5],[-0.05,51.5],[-0.05,51.6],[-0.2,51.6],[-0.2,51.5]]]];
        $polyB = ['type' => 'Polygon', 'coordinates' => [[[-0.05,51.5],[0.2,51.5],[0.2,51.6],[-0.05,51.6],[-0.05,51.5]]]];

        $a = Zone::factory()->create(['zone_name' => 'A', 'meta' => ['polygon' => $polyA]]);
        $b = Zone::factory()->create(['zone_name' => 'B', 'meta' => ['polygon' => $polyB]]);

        $pricing = PricingZone::factory()->create(['from_zone_id' => $a->id, 'to_zone_id' => $b->id, 'saloon_price' => 12.50]);

        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.quote'), ['pickup_lat' => 51.55, 'pickup_lon' => -0.1, 'dropoff_lat' => 51.55, 'dropoff_lon' => 0.1, 'vehicle_type' => 'saloon']);

        $response->assertStatus(200)->assertJson(['success' => true, 'pricing' => ['id' => $pricing->id]]);
        $this->assertEquals(12.5, floatval($response->json('pricing.selected_price')));

        // test vehicle type selection affects selected price
        $pricing->business_price = 20.0; $pricing->save();
        $responseB = $this->actingAs($admin)->postJson(route('admin.pricing.zones.quote'), ['pickup_lat' => 51.55, 'pickup_lon' => -0.1, 'dropoff_lat' => 51.55, 'dropoff_lon' => 0.1, 'vehicle_type' => 'Business']);
        $responseB->assertStatus(200)->assertJson(['success' => true, 'pricing' => ['id' => $pricing->id]]);
        $this->assertEquals(20.0, floatval($responseB->json('pricing.selected_price')));

        // Now simulate storing a booking with the quoted charge
        $form = [
            'pickup_address' => 'Start',
            'dropoff_address' => 'End',
            'passenger_name' => 'Jane',
            'phone' => '07712345678',
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '12:00',
            'vehicle_type' => 'Saloon',
            'booking_charges' => 12.5
        ];

        $response2 = $this->actingAs($admin)->postJson(route('admin.bookings.manual.store'), $form);
        $response2->assertStatus(201)->assertJson(['success' => true]);
        $booking = \App\Models\Booking::first();
        $this->assertEquals(12.5, floatval($booking->total_price));
    }

    public function test_admin_can_quote_by_mileage_when_no_zones_or_postcode()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        // create a mileage pricing range 0-5 miles
        $m = \App\Models\PricingMileageCharge::create([
            'start_mile' => 0,
            'end_mile' => 5,
            'saloon_price' => 10.00,
            'business_price' => 15.00,
            'mpv6_price' => 20.00,
            'mpv8_price' => 25.00,
            'status' => 'active'
        ]);

        // choose two close points (roughly 1 mile apart)
        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.quote'), ['pickup_lat' => 51.5074, 'pickup_lon' => -0.1278, 'dropoff_lat' => 51.5174, 'dropoff_lon' => -0.1278, 'vehicle_type' => 'saloon']);

        $response->assertStatus(200)->assertJson(['success' => true, 'pricing_type' => 'mileage', 'pricing' => ['id' => $m->id]]);
        $this->assertEquals(10.00, floatval($response->json('pricing.selected_price')));
    }

    public function test_admin_fallbacks_to_mileage_when_only_one_zone_exists()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        // create a pickup zone polygon
        $polyA = ['type' => 'Polygon', 'coordinates' => [[[-0.01,51.5073],[0.01,51.5073],[0.01,51.5076],[-0.01,51.5076],[-0.01,51.5073]]]];
        $a = Zone::factory()->create(['zone_name' => 'PickupZone', 'meta' => ['polygon' => $polyA]]);

        // mileage pricing
        $m = \App\Models\PricingMileageCharge::create([
            'start_mile' => 0,
            'end_mile' => 5,
            'saloon_price' => 11.00,
            'business_price' => 16.00,
            'mpv6_price' => 21.00,
            'mpv8_price' => 26.00,
            'status' => 'active'
        ]);

        // pickup inside pickup zone, dropoff well outside
        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.quote'), ['pickup_lat' => 51.5074, 'pickup_lon' => -0.0001, 'dropoff_lat' => 51.5100, 'dropoff_lon' => -0.0001, 'vehicle_type' => 'saloon']);

        $response->assertStatus(200)->assertJson(['success' => true, 'pricing_type' => 'mileage', 'pricing' => ['id' => $m->id]]);
        $this->assertEquals(11.00, floatval($response->json('pricing.selected_price')));
    }

    public function test_quote_reports_inactive_mileage_match()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        // create inactive mileage range that would match an 8 mile journey
        $inactive = \App\Models\PricingMileageCharge::create([
            'start_mile' => 6,
            'end_mile' => 10.99,
            'saloon_price' => 33.00,
            'status' => 'inactive'
        ]);

        // force client driving distance so server checks matching inactive entry
        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.quote'), ['pickup_lat' => 51.5074, 'pickup_lon' => -0.0001, 'dropoff_lat' => 51.5100, 'dropoff_lon' => -0.0001, 'vehicle_type' => 'saloon', 'distance_miles' => 8.0]);

        $response->assertStatus(200)->assertJson(['success' => false, 'message' => 'Matching mileage range exists but is inactive', 'matching_mileage' => ['id' => $inactive->id]]);
    }

    public function test_quote_respects_client_provided_distance()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        // create two mileage ranges
        $m1 = \App\Models\PricingMileageCharge::create([
            'start_mile' => 0,
            'end_mile' => 20,
            'saloon_price' => 11.00,
            'status' => 'active'
        ]);
        $m2 = \App\Models\PricingMileageCharge::create([
            'start_mile' => 21,
            'end_mile' => 40,
            'saloon_price' => 22.00,
            'status' => 'active'
        ]);

        // pick two close points so server's haversine would match m1, but provide a driving distance that falls into m2
        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.quote'), ['pickup_lat' => 51.5074, 'pickup_lon' => -0.1278, 'dropoff_lat' => 51.5174, 'dropoff_lon' => -0.1278, 'vehicle_type' => 'saloon', 'distance_miles' => 25]);

        $response->assertStatus(200)->assertJson(['success' => true, 'pricing_type' => 'mileage', 'pricing' => ['id' => $m2->id]]);
        $this->assertEquals(22.00, floatval($response->json('pricing.selected_price')));
    }

    public function test_update_zone_name_to_existing_returns_validation_error()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $a = Zone::factory()->create(['zone_name' => 'Alpha']);
        $b = Zone::factory()->create(['zone_name' => 'Beta']);

        $response = $this->actingAs($admin)->postJson(route('admin.pricing.zones.update_map', $b->id), [
            'zone_name' => 'Alpha'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message','errors' => ['zone_name']]);
    }
}
