<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteSection extends Model
{
    protected $table = 'quote_sections';

    protected $fillable = [
        'section_key',
        'hero_title',
        'hero_subtitle',
        'description',
        'phone',
        'highlights',
        'order',
    ];

    protected $casts = [
        'highlights' => 'array',
        'order' => 'integer',
    ];
}
