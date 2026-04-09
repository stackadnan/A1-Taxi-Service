<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardFleet extends Model
{
    protected $table = 'card_fleet';

    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'description',
        'image',
        'category',
        'link',
        'passengers',
        'suitcases',
        'cabin_bags',
        'order',
    ];

    protected $casts = [
        'passengers' => 'integer',
        'suitcases' => 'integer',
        'cabin_bags' => 'integer',
        'order' => 'integer',
    ];
}
