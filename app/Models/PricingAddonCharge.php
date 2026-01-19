<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingAddonCharge extends Model
{
    use HasFactory;

    protected $table = 'pricing_addon_charges';

    protected $fillable = [
        'charge_name', 'vehicle_type', 'charge_type', 'charge_value', 'pickup_price', 'dropoff_price', 'status', 'active'
    ];

    protected $casts = [
        'charge_value' => 'decimal:2',
        'pickup_price' => 'decimal:2',
        'dropoff_price' => 'decimal:2',
        'active' => 'boolean'
    ];
}
