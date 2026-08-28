<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'three_d_template_id',
        'name',
        'slug',
        'price',
        'original_price',
        'stock',
        'description',
        'image',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
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

    public function getInstagramCodeAttribute()
    {
        $url = $this->features['instagram_url'] ?? null;
        if (!$url) return null;

        preg_match('/(?:instagram\.com|instagr\.am)\/(?:p|reel|tv)\/([A-Za-z0-9_-]+)/i', $url, $matches);
        return $matches[1] ?? null;
    }

    public function getInstagramEmbedUrlAttribute()
    {
        $code = $this->instagram_code;
        if (!$code) return null;

        return "https://www.instagram.com/p/{$code}/embed";
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

    public function getImagesAttribute()
    {
        $list = collect();
        $rawImage = $this->getRawOriginal('image') ?: ($this->attributes['image'] ?? null);
        if (!empty($rawImage)) {
            $list->push((object)['url' => ltrim($rawImage, '/')]);
        }

        $gallery = $this->features['images'] ?? [];
        if (is_array($gallery)) {
            foreach ($gallery as $img) {
                if (!empty($img)) {
                    $list->push((object)['url' => ltrim($img, '/')]);
                }
            }
        }

        return $list;
    }

    public function getSkuAttribute()
    {
        return $this->model_code ?: (string)$this->id;
    }

    public function getBrandNameAttribute()
    {
        return $this->features['brand'] ?? 'Ahşap Evim';
    }

    public function getCategoryNameAttribute()
    {
        return $this->category?->name ?? 'Ahşap Çerçeve';
    }

    /**
     * SEO Uyumlu ve Benzersiz URL / Slug Oluşturucu
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = \Illuminate\Support\Str::slug($title);
        if (empty($slug)) {
            $slug = 'urun';
        }

        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Otomatik model dinleyicisi - Slug boşsa ürün başlığından üretilir, sort_order atanır.
     */
    protected static function booted()
    {
        static::saving(function ($product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id ?? null);
            }
            if ($product->sort_order === null || $product->sort_order === '') {
                $product->sort_order = (static::max('sort_order') ?? 0) + 1;
            }
        });
    }

    /**
     * Admin sıralamasına göre listeleme scope'u
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }

    /**
     * Ürün detay URL'i ($product->url)
     */
    public function getUrlAttribute(): string
    {
        return url('/urun/' . ($this->slug ?: $this->id));
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

        if ($this->relationLoaded('favorites')) {
            return $this->favorites->contains('user_id', $user->id);
        }

        static $userFavoriteIds = null;
        if ($userFavoriteIds === null) {
            $userFavoriteIds = Favorite::where('user_id', $user->id)->pluck('product_id')->flip()->toArray();
        }

        return isset($userFavoriteIds[$this->id]);
    }
}
