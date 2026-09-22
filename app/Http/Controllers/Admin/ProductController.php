<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\R2StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->ordered()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate($this->productValidationRules());

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = R2StorageService::upload($request->file('image'), 'products', 'product');
        }

        // Gallery Images Upload
        $galleryImages = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $galleryImages[] = R2StorageService::upload($file, 'products', 'gal');
            }
        }

        // Price & Discount Calculation
        if ($request->has('has_discount') && $request->filled('discounted_price')) {
            $price = $request->discounted_price;
            $originalPrice = $request->price;
        } else {
            $price = $request->price;
            $originalPrice = null;
        }

        $features = [
            'color'         => $request->color,
            'size'          => $request->size,
            'images'        => $galleryImages,
            'youtube_url'   => $request->youtube_url,
            'instagram_url' => $request->instagram_url,
        ];

        $slugCandidate = $request->filled('slug') ? $request->slug : $request->name;
        $slug = Product::generateUniqueSlug($slugCandidate);

        $productData = [
            'category_id'       => $request->category_id,
            'name'              => $request->name,
            'slug'              => $slug,
            'price'             => $price,
            'original_price'    => $originalPrice,
            'stock'             => $request->stock,
            'description'       => $request->description,
            'image'             => $imagePath,
            'features'          => $features,
            'is_active'         => $request->has('is_active'),
        ];

        if ($request->filled('sort_order')) {
            $productData['sort_order'] = (int) $request->sort_order;
        }

        $product = Product::create($productData);

        // Auto update XML and Sitemap
        app(\App\Http\Controllers\SeoController::class)->sitemap();
        app(\App\Http\Controllers\SeoController::class)->urunlerXml();

        return redirect()->route('admin.products.index')->with('success', 'Ürün başarıyla eklendi (SEO Bağlantısı: /urun/' . $product->slug . ').');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate($this->productValidationRules($id));

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($product->image) {
                R2StorageService::delete($product->image);
            }
            $imagePath = R2StorageService::upload($request->file('image'), 'products', 'product');
        }

        // Gallery Images Handling
        $features = $product->features ?: [];
        $existingGallery = $features['images'] ?? [];

        // Remove deleted gallery images
        if ($request->has('remove_gallery') && is_array($request->remove_gallery)) {
            foreach ($request->remove_gallery as $removedImg) {
                if (($key = array_search($removedImg, $existingGallery)) !== false) {
                    unset($existingGallery[$key]);
                    R2StorageService::delete($removedImg);
                }
            }
            $existingGallery = array_values($existingGallery);
        }

        // Append new gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $existingGallery[] = R2StorageService::upload($file, 'products', 'gal');
            }
        }

        // Price & Discount Calculation
        if ($request->has('has_discount') && $request->filled('discounted_price')) {
            $price = $request->discounted_price;
            $originalPrice = $request->price;
        } else {
            $price = $request->price;
            $originalPrice = null;
        }

        $features['color']         = $request->color;
        $features['size']          = $request->size;
        $features['images']        = array_values($existingGallery);
        $features['youtube_url']   = $request->youtube_url;
        $features['instagram_url'] = $request->instagram_url;

        $slugCandidate = $request->filled('slug') ? $request->slug : $request->name;
        $slug = Product::generateUniqueSlug($slugCandidate, $product->id);

        $updateData = [
            'category_id'       => $request->category_id,
            'name'              => $request->name,
            'slug'              => $slug,
            'price'             => $price,
            'original_price'    => $originalPrice,
            'stock'             => $request->stock,
            'description'       => $request->description,
            'image'             => $imagePath,
            'features'          => $features,
            'is_active'         => $request->has('is_active'),
        ];

        if ($request->filled('sort_order')) {
            $updateData['sort_order'] = (int) $request->sort_order;
        }

        $product->update($updateData);

        // Auto update XML and Sitemap
        app(\App\Http\Controllers\SeoController::class)->sitemap();
        app(\App\Http\Controllers\SeoController::class)->urunlerXml();

        return redirect()->route('admin.products.index')->with('success', 'Ürün başarıyla güncellendi (SEO Bağlantısı: /urun/' . $product->slug . ').');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            R2StorageService::delete($product->image);
        }
        $gallery = $product->features['images'] ?? [];
        if (is_array($gallery)) {
            foreach ($gallery as $gImg) {
                R2StorageService::delete($gImg);
            }
        }
        $product->delete();

        // Auto update XML and Sitemap
        app(\App\Http\Controllers\SeoController::class)->sitemap();
        app(\App\Http\Controllers\SeoController::class)->urunlerXml();

        return redirect()->route('admin.products.index')->with('success', 'Ürün başarıyla silindi.');
    }

    /**
     * Ürün store() ve update() için ortak validation kuralları.
     */
    private function productValidationRules(?int $ignoreId = null): array
    {
        return [
            'name'               => 'required|string|max:255',
            'slug'               => 'nullable|string|max:255',
            'category_id'        => 'required|exists:categories,id',
            'price'              => 'required|numeric|min:0',
            'discounted_price'   => 'nullable|numeric|min:0',
            'stock'              => 'required|integer|min:0',
            'description'        => 'nullable|string',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
            'gallery.*'          => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:8192',
            'youtube_url'        => 'nullable|url|max:255',
            'instagram_url'      => 'nullable|url|max:255',
            'color'              => 'nullable|string',
            'size'               => 'nullable|string',
            'sort_order'         => 'nullable|integer|min:0',
        ];
    }

    /**
     * AJAX ile toplu/sürükle-bırak ürün sıralama güncellemesi.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:products,id',
            'orders.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->orders as $item) {
            Product::where('id', $item['id'])->update(['sort_order' => (int) $item['sort_order']]);
        }

        app(\App\Http\Controllers\SeoController::class)->sitemap();
        app(\App\Http\Controllers\SeoController::class)->urunlerXml();

        return response()->json([
            'success' => true,
            'message' => 'Ürün sıralaması başarıyla güncellendi.'
        ]);
    }

    /**
     * Otomatik toplu ürün sıralama (Tarih, Fiyat, İsim vb.)
     */
    public function autoSort(Request $request)
    {
        $request->validate([
            'sort_by' => 'required|string|in:newest,oldest,name_asc,name_desc,price_asc,price_desc,id_asc',
        ]);

        $sortBy = $request->sort_by;
        $query = Product::query();

        switch ($sortBy) {
            case 'newest':
                $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
        }

        $products = $query->get();
        $order = 1;
        foreach ($products as $p) {
            $p->sort_order = $order++;
            $p->saveQuietly();
        }

        app(\App\Http\Controllers\SeoController::class)->sitemap();
        app(\App\Http\Controllers\SeoController::class)->urunlerXml();

        return redirect()->route('admin.products.index')->with('success', 'Tüm ürünler seçilen kritere göre otomatik sıralandı.');
    }
}

