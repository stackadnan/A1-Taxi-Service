<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PricingZone;
use App\Models\Zone;

class PricingZoneFactory extends Factory
{
    protected $model = PricingZone::class;

    public function definition()
    {
        $from = Zone::factory()->create();
        $to = Zone::factory()->create();

        return [
            'from_zone_id' => $from->id,
            'to_zone_id' => $to->id,
            'saloon_price' => $this->faker->randomFloat(2, 0, 100),
            'business_price' => $this->faker->randomFloat(2, 0, 150),
            'mpv6_price' => $this->faker->randomFloat(2, 0, 200),
            'mpv8_price' => $this->faker->randomFloat(2, 0, 250),
            'pricing_mode' => $this->faker->randomElement(['flat','distance','zone']),
            'status' => 'active'
        ];
    }
}
