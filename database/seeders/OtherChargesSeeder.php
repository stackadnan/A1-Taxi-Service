<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PricingAddonCharge;

class OtherChargesSeeder extends Seeder
{
    public function run(): void
    {
        $charges = [
            ['charge_name' => 'Gatwick Airport', 'pickup_price' => 0.00, 'dropoff_price' => 0.00, 'status' => 'active', 'active' => true],
            ['charge_name' => 'Heathrow Airport', 'pickup_price' => 0.00, 'dropoff_price' => 0.00, 'status' => 'active', 'active' => true],
            ['charge_name' => 'London Luton Airport', 'pickup_price' => 0.00, 'dropoff_price' => 0.00, 'status' => 'active', 'active' => true],
            ['charge_name' => 'Stansted Airport', 'pickup_price' => 0.00, 'dropoff_price' => 0.00, 'status' => 'active', 'active' => true],
            ['charge_name' => 'London City Airport', 'pickup_price' => 0.00, 'dropoff_price' => 0.00, 'status' => 'active', 'active' => true],
            ['charge_name' => 'Manchester Airport', 'pickup_price' => 0.00, 'dropoff_price' => 0.00, 'status' => 'active', 'active' => true],
            ['charge_name' => 'Birmingham Airport', 'pickup_price' => 0.00, 'dropoff_price' => 0.00, 'status' => 'active', 'active' => true],
            ['charge_name' => 'Congestion Charge Zone', 'pickup_price' => 0.00, 'dropoff_price' => 0.00, 'status' => 'active', 'active' => true],
            ['charge_name' => 'Meet & Greet Charges', 'pickup_price' => 0.00, 'dropoff_price' => 0.00, 'status' => 'active', 'active' => true],
        ];

        foreach ($charges as $charge) {
            PricingAddonCharge::firstOrCreate(
                ['charge_name' => $charge['charge_name']],
                $charge
            );
        }
    }
}
