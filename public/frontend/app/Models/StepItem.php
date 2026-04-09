<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StepItem extends Model
{
    protected $table = 'steps';

    protected $fillable = [
        'title',
        'link',
        'icon1',
        'icon2',
        'description',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];
}
