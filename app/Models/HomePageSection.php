<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageSection extends Model
{
    protected $table = 'homepage_sections';

    protected $fillable = [
        'type',
        'title',
        'subtitle',
        'content',
        'background_color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
