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

    public function getImageAttribute($value)
    {
        if (!$value) {
            return url('/cerceve.png');
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        return url($value);
    }

    public function getRawImageAttribute()
    {
        return $this->attributes['image'] ?? null;
    }

    public function getGalleryUrlsAttribute()
    {
        $images = $this->features['images'] ?? [];
        if (!is_array($images)) return [];

        return array_map(function ($img) {
            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                return $img;
            }
            return url($img);
        }, $images);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function isFavoritedBy($user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }
        if (!$user) return false;

        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
