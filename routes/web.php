<?php

use Illuminate\Support\Facades\Route;

use App\Models\Product;
use App\Models\Category;

// Frontend Routes
Route::get('/', function () {
    $query = Product::where('is_active', true);
    
    if (request('category')) {
        $categorySlug = request('category');
        $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }
    
    $products = $query->latest()->paginate(16)->withQueryString();
    $categories = Category::withCount('products')->get();
    
    return view('home', compact('products', 'categories'));
});

Route::get('/urun/{id}', function ($id) {
    $product = Product::with('category')->where('id', $id)->orWhere('slug', $id)->firstOrFail();
    
    // Similar Products
    $similarProducts = Product::where('category_id', $product->category_id)
                              ->where('id', '!=', $product->id)
                              ->where('is_active', true)
                              ->inRandomOrder()
                              ->take(4)
                              ->get();
                              
    // Recently Viewed Logic
    $recentlyViewedIds = session()->get('recently_viewed', []);
    
    // Fetch recently viewed products before we add the current one
    $recentlyViewed = collect();
    if (count($recentlyViewedIds) > 0) {
        $recentlyViewed = Product::whereIn('id', $recentlyViewedIds)
                                 ->where('id', '!=', $product->id)
                                 ->take(4)
                                 ->get();
    }
    
    // Add current product to session
    if (!in_array($product->id, $recentlyViewedIds)) {
        array_unshift($recentlyViewedIds, $product->id);
        if (count($recentlyViewedIds) > 10) {
            array_pop($recentlyViewedIds);
        }
        session()->put('recently_viewed', $recentlyViewedIds);
    }
    
    return view('products.show', compact('product', 'similarProducts', 'recentlyViewed'));
});

Route::get('/urunler', function () {
    $query = Product::where('is_active', true);
    if (request('category')) {
        $categorySlug = request('category');
        $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }
    $products = $query->latest()->paginate(16);
    $categories = Category::all();
    return view('products.index', compact('products', 'categories'));
});

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::get('/sepet', [CartController::class, 'index'])->name('cart.index');
Route::post('/sepet/ekle', [CartController::class, 'add'])->name('cart.add');
Route::post('/sepet/guncelle', [CartController::class, 'update'])->name('cart.update');
Route::post('/sepet/sil', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/odeme', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/odeme', [CheckoutController::class, 'process'])->name('checkout.process');
Route::post('/odeme/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
Route::get('/odeme/sonuc', [CheckoutController::class, 'result'])->name('checkout.result');

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ThreeDTemplateController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\LoginController;

// Dynamic Informational Pages (Bilgilendirme Sayfaları)
Route::get('/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
    return view('pages.show', [
        'pageTitle' => $page->title,
        'content' => $page->content
    ]);
})->where('slug', 'iletisim|sikca-sorulanlar|mesafeli-satis-sozlesmesi|gizlilik-politikasi|teslimat-ve-iade');

// Admin Auth Routes
Route::get('/yonetim/giris', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/yonetim/giris', [LoginController::class, 'login']);
Route::post('/yonetim/cikis', [LoginController::class, 'logout'])->name('admin.logout');

// Admin Redirects
Route::get('/admin', function () {
    return redirect()->route('admin.products.index');
});

// Admin Routes (Protected)
Route::prefix('yonetim')->middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.products.index');
    });

    Route::resource('kategoriler', CategoryController::class)->except(['create', 'show', 'edit'])->names('admin.categories');
    Route::resource('urunler', ProductController::class)->names('admin.products');
    Route::resource('3d-sablonlar', ThreeDTemplateController::class)->parameters(['3d-sablonlar' => 'template'])->names('admin.templates');
    Route::resource('sayfalar', PageController::class)->names('admin.pages');

    Route::get('/siparisler', function () {
        return view('admin.orders.index');
    })->name('admin.orders');
});
