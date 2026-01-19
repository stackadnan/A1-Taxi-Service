<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingMileageCharge extends Model
{
    protected $table = 'pricing_mileage_charges';

    protected $fillable = [
        'start_mile','end_mile',
        'saloon_price','business_price','mpv6_price','mpv8_price',
        'is_fixed_charge','status'
    ];

    protected $casts = [
        'start_mile' => 'decimal:2',
        'end_mile' => 'decimal:2',
        'saloon_price' => 'decimal:2',
        'business_price' => 'decimal:2',
        'mpv6_price' => 'decimal:2',
        'mpv8_price' => 'decimal:2',
        'is_fixed_charge' => 'boolean'
    ];
}
