<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicQuoteRequest extends Model
{
    protected $table = 'public_quote_requests';

    protected $fillable = [
        'quote_ref',
        'pickup_address',
        'dropoff_address',
        'pickup_date',
        'source_ip',
        'source_url',
        'vehicle_type',
        'price',
        'trip_type',
        'linked_quote_ref',
    ];
}
