<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPostcodeCharge extends Model
{
    protected $table = 'pricing_postcode_charges';

    protected $fillable = [
        'pickup_postcode', 'dropoff_postcode',
        'saloon_price', 'business_price', 'mpv6_price', 'mpv8_price',
        'status', 'auto_generated'
    ];

    protected $casts = [
        'saloon_price' => 'decimal:2',
        'business_price' => 'decimal:2',
        'mpv6_price' => 'decimal:2',
        'mpv8_price' => 'decimal:2',
        'auto_generated' => 'boolean'
    ];
}
