<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'gallery';

    protected $fillable = [
        'source_path',
        'image_path',
        'alt',
        'image_date',
        'meta',
        'is_active',
    ];

    protected $casts = [
        'source_path' => 'string',
        'image_path' => 'string',
        'short_url' => 'string',
        'alt' => 'string',
        'image_date' => 'date',
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::created(function (self $gallery): void {
            if (!is_string($gallery->short_url) || trim($gallery->short_url) === '') {
                $gallery->forceFill([
                    'short_url' => 'i/'.$gallery->id,
                ])->saveQuietly();
            }
        });
    }
}
