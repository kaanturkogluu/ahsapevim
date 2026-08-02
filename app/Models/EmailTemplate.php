<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'subject',
        'content',
        'shortcodes',
        'is_active',
    ];

    protected $casts = [
        'shortcodes' => 'array',
        'is_active' => 'boolean',
    ];
}
