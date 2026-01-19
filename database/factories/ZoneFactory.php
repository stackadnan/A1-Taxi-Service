<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Zone;

class ZoneFactory extends Factory
{
    protected $model = Zone::class;

    public function definition()
    {
        return [
            'zone_name' => $this->faker->unique()->city(),
            'latitude' => $this->faker->latitude( -90, 90 ),
            'longitude' => $this->faker->longitude( -180, 180 ),
            'code' => strtoupper($this->faker->unique()->bothify('Z??')),
            'status' => 'active',
            'meta' => null,
        ];
    }
}
