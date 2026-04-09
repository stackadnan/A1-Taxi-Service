<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureBenefit extends Model
{
    protected $table = 'feature_benefits';

    protected $fillable = [
        'title',
        'description',
        'icon',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];
}
