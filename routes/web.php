<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ThreeDTemplateController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\ShippingCompanyController;
use App\Http\Controllers\Admin\MessageLogController;
use App\Http\Controllers\Admin\HomeBannerController;
use App\Http\Controllers\SeoController;

// ─── SEO, Sitemap & XML Product Feed ──────────────────────────────────────────
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/urunler.xml', [SeoController::class, 'urunlerXml'])->name('seo.urunler_xml');

// ─── Shared Search Helper ─────────────────────────────────────────────────────
// Applies partial-word product search filter to an existing Eloquent query.
// Used by / (home), /urunler (products list) and /canli-arama (live AJAX search)
// so that changes only need to be made in one place.
function applyProductSearch($query, string $search): void
{
    $keywords = array_filter(explode(' ', $search), fn($w) => mb_strlen($w) >= 2);

    $query->where(function ($q) use ($search, $keywords) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('description', 'like', "%{$search}%")
          ->orWhere('barcode', 'like', "%{$search}%")
          ->orWhere('model_code', 'like', "%{$search}%")
          ->orWhere('features', 'like', "%{$search}%")
          ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$search}%"));

        foreach ($keywords as $word) {
            $q->orWhere('name', 'like', "%{$word}%")
              ->orWhere('description', 'like', "%{$word}%")
              ->orWhere('features', 'like', "%{$word}%")
              ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$word}%"));
        }
    });
}

// ─── Sipariş Takip ───────────────────────────────────────────────────────────
Route::get('/siparis-takip',  [OrderTrackingController::class, 'index'])->name('order.tracking');
Route::post('/siparis-takip', [OrderTrackingController::class, 'track'])->name('order.tracking.search');

// ─── Frontend Routes ──────────────────────────────────────────────────────────
Route::get('/', function (Request $request) {
    // Mobil cihazları sunucu tarafında yönlendir (886 satır sayfa yüklenmeden)
    $ua = $request->userAgent() ?? '';
    if (preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $ua)) {
        return redirect('/urunler', 301);
    }

    $query = Product::where('is_active', true);

    if (request('category')) {
        $slug = request('category');
        $query->whereHas('category', fn($q) => $q->where('slug', $slug));
    }

    $search = trim(request('q') ?: request('search', ''));
    if ($search !== '') {
        applyProductSearch($query, $search);
    }

    $products   = $query->ordered()->paginate(20)->withQueryString();
    $categories = Category::withCount('products')->get();
    
    $homeBanners = collect();
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('home_banners')) {
            $homeBanners = \App\Models\HomeBanner::where('is_active', true)->orderBy('order', 'asc')->get();
        }
    } catch (\Throwable $e) {
        $homeBanners = collect();
    }

    return view('home', compact('products', 'categories', 'homeBanners'));
});

// Canlı Arama (AJAX / Autocomplete)
Route::get('/canli-arama', function (Request $request) {
    $q = trim($request->input('q', ''));
    if (mb_strlen($q) < 2) {
        return response()->json(['status' => 'success', 'products' => []]);
    }

    $query = Product::where('is_active', true)->with('category');
    applyProductSearch($query, $q);
    $products = $query->take(6)->get();

    $data = $products->map(fn($p) => [
        'id'            => $p->id,
        'name'          => $p->name,
        'category_name' => $p->category ? $p->category->name : 'Ahşap Çerçeve',
        'price'         => number_format($p->price, 2, ',', '.') . ' ₺',
        'image'         => url($p->image ?: '/cerceve.png'),
        'url'           => $p->url,
    ]);

    return response()->json(['status' => 'success', 'products' => $data, 'count' => count($data)]);
})->name('search.live');

Route::get('/urun/{id}', function ($id) {
    $product = Product::with('category')->where('id', $id)->orWhere('slug', $id)->firstOrFail();

    $similarProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $product->id)
        ->where('is_active', true)
        ->inRandomOrder()
        ->take(4)
        ->get();

    $recentlyViewedIds = session()->get('recently_viewed', []);
    $recentlyViewed    = collect();

    if (count($recentlyViewedIds) > 0) {
        $recentlyViewed = Product::whereIn('id', $recentlyViewedIds)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();
    }

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
        $slug = request('category');
        $query->whereHas('category', fn($q) => $q->where('slug', $slug));
    }

    $search = trim(request('q') ?: request('search', ''));
    if ($search !== '') {
        applyProductSearch($query, $search);
    }

    $products   = $query->ordered()->paginate(20)->withQueryString();
    $categories = Category::all();

    return view('products.index', compact('products', 'categories'));
});

// ─── User Auth Routes ─────────────────────────────────────────────────────────
Route::get('/giris',  [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/giris', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:10,1');
Route::get('/kayit',  [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/kayit', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:5,1');
Route::get('/auth/google',          [AuthController::class, 'googleLogin']);
Route::post('/auth/google',         [AuthController::class, 'googleLogin'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('/cikis',               [AuthController::class, 'logout'])->name('logout');

// ─── User Profile Routes (Protected) ─────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/hesabim',                       [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/hesabim/bilgiler',             [ProfileController::class, 'updateInfo'])->name('profile.updateInfo');
    Route::post('/hesabim/adres',                [ProfileController::class, 'updateAddress'])->name('profile.updateAddress');
    Route::post('/hesabim/sifre',                [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::put('/hesabim/siparisler/{id}/iptal', [ProfileController::class, 'cancelOrder'])->name('orders.cancel');
});

// ─── Favorites ────────────────────────────────────────────────────────────────
Route::get('/favoriler',      [FavoriteController::class, 'index'])->name('favorites.index');
Route::post('/favori-toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

// ─── Cart ─────────────────────────────────────────────────────────────────────
Route::get('/sepet',           [CartController::class, 'index'])->name('cart.index');
Route::get('/sepet/data',      [CartController::class, 'getCartData'])->name('cart.data');
Route::post('/sepet/ekle',     [CartController::class, 'add'])->name('cart.add');
Route::post('/sepet/guncelle', [CartController::class, 'update'])->name('cart.update');
Route::post('/sepet/sil',      [CartController::class, 'remove'])->name('cart.remove');

// ─── Checkout ─────────────────────────────────────────────────────────────────
Route::get('/odeme',                             [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/odeme',                            [CheckoutController::class, 'process'])->name('checkout.process');
Route::match(['get', 'post'], '/odeme/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
Route::get('/odeme/sonuc',                       [CheckoutController::class, 'result'])->name('checkout.result');

// ─── Dynamic Informational Pages ─────────────────────────────────────────────
Route::get('/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

    if ($slug === 'iletisim') {
        $contactData = json_decode($page->content, true);
        if (!is_array($contactData)) {
            $contactData = [
                'phone'                  => '0850 XXX XX XX',
                'whatsapp'               => '05XX XXX XX XX',
                'working_hours_weekdays' => '09:00 - 18:00',
                'working_hours_saturday' => '10:00 - 15:00',
                'address'                => "Şehzadeler Mevkii, Merkez\nManisa, Türkiye",
                'map_url'                => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d100001.32837311103!2d27.359288219030202!3d38.61867137839352!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b98d249f0322b7%3A0xc486be78a2e7c4f4!2sManisa%2C%20%C5%9Eehzadeler%2FManisa!5e0!3m2!1str!2str!4v1700000000000!5m2!1str!2str',
                'email'                  => 'info@ahsapevim.com',
                'note'                   => '',
            ];
        }
        return view('pages.contact', ['pageTitle' => $page->title, 'contactData' => $contactData, 'page' => $page]);
    }

    if ($slug === 'sikca-sorulanlar') {
        $faqItems = json_decode($page->content, true);
        if (!is_array($faqItems)) {
            $faqItems = [];
        }
        return view('pages.faq', ['pageTitle' => $page->title, 'faqItems' => $faqItems, 'rawContent' => $page->content, 'page' => $page]);
    }

    return view('pages.show', ['pageTitle' => $page->title, 'content' => $page->content]);
});

// ─── Admin Auth ───────────────────────────────────────────────────────────────
Route::get('/yonetim/giris',  [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/yonetim/giris', [LoginController::class, 'login'])->name('admin.login.post')->middleware('throttle:5,1');
Route::post('/yonetim/cikis', [LoginController::class, 'logout'])->name('admin.logout');

Route::get('/admin', fn() => redirect()->route('admin.revenue.index'));

// ─── Admin Routes (Protected) ─────────────────────────────────────────────────
Route::prefix('yonetim')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', fn() => redirect()->route('admin.revenue.index'));

    Route::resource('kategoriler', CategoryController::class)->except(['create', 'show', 'edit'])->names('admin.categories');

    Route::post('urunler/siralama-guncelle', [ProductController::class, 'updateOrder'])->name('admin.products.update_order');
    Route::post('urunler/otomatik-sirala',    [ProductController::class, 'autoSort'])->name('admin.products.auto_sort');
    Route::post('urunler',          [ProductController::class, 'store'])->middleware('throttle:15,1')->name('admin.products.store');
    Route::put('urunler/{product}', [ProductController::class, 'update'])->middleware('throttle:15,1')->name('admin.products.update');
    Route::resource('urunler', ProductController::class)->parameters(['urunler' => 'product'])->except(['store', 'update'])->names('admin.products');

    Route::post('3d-sablonlar',           [ThreeDTemplateController::class, 'store'])->middleware('throttle:15,1')->name('admin.templates.store');
    Route::put('3d-sablonlar/{template}', [ThreeDTemplateController::class, 'update'])->middleware('throttle:15,1')->name('admin.templates.update');
    Route::resource('3d-sablonlar', ThreeDTemplateController::class)->parameters(['3d-sablonlar' => 'template'])->except(['store', 'update'])->names('admin.templates');

    Route::resource('sayfalar', PageController::class)->names('admin.pages');
    Route::resource('anasayfa-gorselleri', HomeBannerController::class)->names('admin.banners');
    Route::get('/siparis-gorsel-indir', [OrderController::class, 'downloadImage'])->name('admin.orders.download_image');
    Route::resource('siparisler', OrderController::class)->only(['index', 'show', 'update', 'destroy'])->names('admin.orders');
    Route::resource('kargo-sirketleri', ShippingCompanyController::class)->except(['create', 'show', 'edit'])->names('admin.shipping_companies');

    // E-Posta Şablon Yönetimi
    Route::get('/eposta-sablonlari',              [EmailTemplateController::class, 'index'])->name('admin.email_templates.index');
    Route::get('/eposta-sablonlari/{id}/edit',    [EmailTemplateController::class, 'edit'])->name('admin.email_templates.edit');
    Route::put('/eposta-sablonlari/{id}',         [EmailTemplateController::class, 'update'])->name('admin.email_templates.update');
    Route::get('/eposta-sablonlari/{id}/preview', [EmailTemplateController::class, 'preview'])->name('admin.email_templates.preview');
    Route::post('/eposta-sablonlari/{id}/test',   [EmailTemplateController::class, 'sendTest'])->name('admin.email_templates.test');

    // İletişim & Mesaj Logları
    Route::get('/loglar/mail',         [MessageLogController::class, 'mailLogs'])->name('admin.mail_logs.index');
    Route::get('/loglar/sms',          [MessageLogController::class, 'smsLogs'])->name('admin.sms_logs.index');
    Route::post('/manuel-mail-gonder', [MessageLogController::class, 'sendManualMail'])->name('admin.mail.send_manual');
    Route::post('/manuel-sms-gonder',  [MessageLogController::class, 'sendManualSms'])->name('admin.sms.send_manual');

    // Gelir Tablosu & İstatistikler
    Route::get('/gelir-tablosu', [\App\Http\Controllers\Admin\RevenueController::class, 'index'])->name('admin.revenue.index');
});
