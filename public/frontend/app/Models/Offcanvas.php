<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offcanvas extends Model
{
    protected $table = 'offcanvas';

    protected $fillable = [
        'section_key',
        'logo',
        'address',
        'email',
        'phone',
        'button_text',
        'button_link',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];
}
