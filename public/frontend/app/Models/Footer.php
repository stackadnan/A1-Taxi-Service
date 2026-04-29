<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    protected $table = 'footer';

    protected $fillable = [
        'section_key',
        'logo',
        'tagline',
        'contact_address',
        'contact_email',
        'contact_phone',
        'links',
        'airports',
        'cities',
        'copyright',
    ];

    protected $casts = [
        'links' => 'array',
        'airports' => 'array',
        'cities' => 'array',
    ];
}
