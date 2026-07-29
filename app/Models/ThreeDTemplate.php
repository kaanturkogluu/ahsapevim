<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThreeDTemplate extends Model
{
    protected $fillable = [
        'name',
        'wood_type',
        'width',
        'height',
        'depth',
        'thickness',
        'has_top',
        'has_bottom',
        'has_left',
        'has_right',
        'inner_width',
        'inner_height',
        'inner_depth',
        'inner_border',
        'pos_x',
        'pos_y',
        'bump_scale',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
