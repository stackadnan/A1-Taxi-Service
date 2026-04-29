<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $table = 'pages';

    protected $fillable = [
        'name',
        'head_title',
        'quote_title',
        'quote_subtitle',
        'quote_description',
        'why_us_title',
        'why_us_heading',
        'why_use_heading',
        'number_of_rows',
        'one_column',
        'two_column',
        'three_column',
        'row_blocks',
    ];

    protected $casts = [
        'row_blocks' => 'array',
    ];

    public function urls()
    {
        return $this->hasMany(Url::class, 'page_id');
    }

    public function seo()
    {
        return $this->hasMany(Seo::class, 'page_id');
    }
}
