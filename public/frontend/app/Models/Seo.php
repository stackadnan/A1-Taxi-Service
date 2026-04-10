<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    use HasFactory;

    protected $table = 'seo';

    protected $fillable = [
        'page_id',
        'route_path',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical',
        'schema_script',
        'robots',
        'og_title',
        'og_description',
        'og_image',
        'date',
        'meta',
        'is_active',
    ];

    protected $casts = [
        'page_id' => 'integer',
        'date' => 'date',
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}
