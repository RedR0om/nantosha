<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrLandritoProfileContent extends Model
{
    protected $fillable = [
        'section_type',
        'key',
        'title',
        'subtitle',
        'text',
        'description',
        'image_url',
        'image_alt',
        'caption',
        'content',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
