<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckoutContent extends Model
{
    protected $fillable = [
        'section_type',
        'key',
        'title',
        'content',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
