<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function threeDTemplate()
    {
        return $this->belongsTo(ThreeDTemplate::class, 'three_d_template_id');
    }

    public function getDiscountPercentAttribute()
    {
        if ($this->original_price && $this->original_price > $this->price && $this->original_price > 0) {
            return (int) round((1 - ($this->price / $this->original_price)) * 100);
        }
        return 0;
    }

    public function getYoutubeIdAttribute()
    {
        $url = $this->features['youtube_url'] ?? null;
        if (!$url) return null;

        preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }
}
