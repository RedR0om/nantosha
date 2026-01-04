<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateProfileContent extends Model
{
    protected $fillable = [
        'section_type',
        'key',
        'title',
        'subtitle',
        'label',
        'text',
        'value',
        'field_type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
