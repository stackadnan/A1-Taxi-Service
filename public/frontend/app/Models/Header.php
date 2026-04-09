<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Header extends Model
{
    use HasFactory;

    protected $table = 'headers';

    protected $fillable = [
        'section_key',
        'top_email',
        'top_address',
        'top_links',
        'social_links',
        'logo_light',
        'logo_dark',
        'phone_label',
        'phone_number',
        'button_text',
        'button_link',
        'airport_links',
        'city_links',
    ];

    protected $casts = [
        'top_links' => 'array',
        'social_links' => 'array',
        'airport_links' => 'array',
        'city_links' => 'array',
    ];
}
