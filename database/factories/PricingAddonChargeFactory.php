<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PricingAddonCharge>
 */
class PricingAddonChargeFactory extends Factory
{
    public function definition()
    {
        $name = $this->faker->words(2, true);
        return [
            'charge_name' => $name,
            'vehicle_type' => $this->faker->randomElement(['saloon','mpv6','mpv8', null]),
            'charge_type' => $this->faker->randomElement(['flat','percentage']),
            'charge_value' => $this->faker->randomFloat(2, 0, 100),
            'status' => 'active'
        ];
    }
}
