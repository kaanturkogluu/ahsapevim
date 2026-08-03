<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\OrderTrackingController;

// Sipariş Takip Rotaları
Route::get('/siparis-takip', [OrderTrackingController::class, 'index'])->name('order.tracking');
Route::post('/siparis-takip', [OrderTrackingController::class, 'track'])->name('order.tracking.search');

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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;

// User Auth Routes
Route::get('/giris', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/giris', [AuthController::class, 'login'])->name('login.post');
Route::get('/kayit', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/kayit', [AuthController::class, 'register'])->name('register.post');
Route::get('/auth/google', [AuthController::class, 'googleLogin']);
Route::post('/auth/google', [AuthController::class, 'googleLogin'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('/cikis', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\ProfileController;

// User Profile Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/hesabim', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/hesabim/bilgiler', [ProfileController::class, 'updateInfo'])->name('profile.updateInfo');
    Route::post('/hesabim/adres', [ProfileController::class, 'updateAddress'])->name('profile.updateAddress');
    Route::post('/hesabim/sifre', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});

// Favorites Routes
Route::get('/favoriler', [FavoriteController::class, 'index'])->name('favorites.index');
Route::post('/favori-toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

Route::get('/sepet', [CartController::class, 'index'])->name('cart.index');
Route::post('/sepet/ekle', [CartController::class, 'add'])->name('cart.add');
Route::post('/sepet/guncelle', [CartController::class, 'update'])->name('cart.update');
Route::post('/sepet/sil', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/odeme', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/odeme', [CheckoutController::class, 'process'])->name('checkout.process');
Route::match(['get', 'post'], '/odeme/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
Route::get('/odeme/sonuc', [CheckoutController::class, 'result'])->name('checkout.result');

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ThreeDTemplateController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\LoginController;

// Dynamic Informational Pages (Bilgilendirme Sayfaları)
Route::get('/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
    
    if ($slug === 'iletisim') {
        $contactData = json_decode($page->content, true);
        if (!is_array($contactData)) {
            $contactData = [
                'phone' => '0850 XXX XX XX',
                'whatsapp' => '05XX XXX XX XX',
                'working_hours_weekdays' => '09:00 - 18:00',
                'working_hours_saturday' => '10:00 - 15:00',
                'address' => "Şehzadeler Mevkii, Merkez\nManisa, Türkiye",
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d100001.32837311103!2d27.359288219030202!3d38.61867137839352!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b98d249f0322b7%3A0xc486be78a2e7c4f4!2sManisa%2C%20%C5%9Eehzadeler%2FManisa!5e0!3m2!1str!2str!4v1700000000000!5m2!1str!2str',
                'email' => 'info@ahsapevim.com',
                'note' => '',
            ];
        }
        return view('pages.contact', [
            'pageTitle' => $page->title,
            'contactData' => $contactData,
            'page' => $page
        ]);
    }

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

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\EmailTemplateController;

// Admin Routes (Protected)
Route::prefix('yonetim')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.products.index');
    });

    Route::resource('kategoriler', CategoryController::class)->except(['create', 'show', 'edit'])->names('admin.categories');
    Route::resource('urunler', ProductController::class)->names('admin.products');
    Route::resource('3d-sablonlar', ThreeDTemplateController::class)->parameters(['3d-sablonlar' => 'template'])->names('admin.templates');
    Route::resource('sayfalar', PageController::class)->names('admin.pages');
    Route::resource('siparisler', OrderController::class)->only(['index', 'show', 'update'])->names('admin.orders');

    // E-Posta Şablon Yönetimi
    Route::get('/eposta-sablonlari', [EmailTemplateController::class, 'index'])->name('admin.email_templates.index');
    Route::get('/eposta-sablonlari/{id}/edit', [EmailTemplateController::class, 'edit'])->name('admin.email_templates.edit');
    Route::put('/eposta-sablonlari/{id}', [EmailTemplateController::class, 'update'])->name('admin.email_templates.update');
    Route::get('/eposta-sablonlari/{id}/preview', [EmailTemplateController::class, 'preview'])->name('admin.email_templates.preview');
    Route::post('/eposta-sablonlari/{id}/test', [EmailTemplateController::class, 'sendTest'])->name('admin.email_templates.test');
});
