<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhyUs extends Model
{
    protected $table = 'why_us';

    protected $fillable = [
        'section_key',
        'section_title',
        'section_subtitle',
        'left_items',
        'right_items',
    ];

    protected $casts = [
        'left_items' => 'array',
        'right_items' => 'array',
    ];
}
