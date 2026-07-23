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

Route::get('/product/{id}', function ($id) {
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

Route::get('/products', function () {
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

Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart/add', [CartController::class, 'add']);

Route::get('/3d', function () {
    return view('pages.3d');
});

// Static Pages
$staticPages = [
    'iletisim' => [
        'title' => 'İletişim', 
        'content' => <<<HTML
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-4">
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-2">İletişim Bilgilerimiz</h3>
        
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-brand text-xl shrink-0">
                <i class="fa-solid fa-phone"></i>
            </div>
            <div>
                <div class="font-bold text-gray-700 text-[15px]">Müşteri Hizmetleri</div>
                <a href="tel:+90850xxxxxxx" class="text-gray-600 hover:text-brand transition block mt-0.5">0850 XXX XX XX</a>
            </div>
        </div>

        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl shrink-0">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <div class="font-bold text-gray-700 text-[15px]">WhatsApp Destek</div>
                <a href="https://wa.me/905xxxxxxxxx" class="text-gray-600 hover:text-green-600 transition block mt-0.5">05XX XXX XX XX</a>
            </div>
        </div>

        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-xl shrink-0">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <div class="font-bold text-gray-700 text-[15px]">Çalışma Saatleri</div>
                <div class="text-gray-600 mt-0.5">Hafta İçi: 09:00 - 18:00<br>Cumartesi: 10:00 - 15:00</div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xl shrink-0">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
                <div class="font-bold text-gray-700 text-[15px]">Atölye / Mağaza Adresi</div>
                <div class="text-gray-600 mt-0.5">Şehzadeler Mevkii, Merkez<br>Manisa, Türkiye</div>
            </div>
        </div>
    </div>
    
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-2">Konumumuz</h3>
        <div class="w-full h-80 rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-100 relative">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d100001.32837311103!2d27.359288219030202!3d38.61867137839352!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b98d249f0322b7%3A0xc486be78a2e7c4f4!2sManisa%2C%20%C5%9Eehzadeler%2FManisa!5e0!3m2!1str!2str!4v1700000000000!5m2!1str!2str" class="absolute inset-0 w-full h-full" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>
HTML
    ],
    'sikca-sorulanlar' => ['title' => 'Sıkça Sorulanlar', 'content' => '<p><strong>Siparişim kaç günde ulaşır?</strong><br>Siparişleriniz ortalama 1-3 iş günü içinde kargoya teslim edilmektedir.</p><p><strong>İade koşulları nelerdir?</strong><br>Kişiselleştirilmiş ürünler hariç 14 gün içinde iade hakkınız bulunmaktadır.</p>'],
    'mesafeli-satis-sozlesmesi' => ['title' => 'Mesafeli Satış Sözleşmesi', 'content' => '<p>Madde 1: Taraflar...</p><p>Madde 2: Sözleşmenin Konusu...</p>'],
    'gizlilik-politikasi' => ['title' => 'Gizlilik Politikası', 'content' => '<p>Kişisel verileriniz 6698 sayılı KVKK kapsamında korunmaktadır...</p>'],
    'teslimat-ve-iade' => ['title' => 'Teslimat ve İade Şartları', 'content' => '<p>Teslimat anlaşmalı kargo firmaları ile yapılmaktadır. Kargo ücreti alıcıya aittir.</p>'],
];

foreach ($staticPages as $slug => $page) {
    Route::get('/' . $slug, function () use ($page) {
        return view('pages.show', [
            'pageTitle' => $page['title'],
            'content' => $page['content']
        ]);
    });
}

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return redirect('/admin/products');
    });

    Route::get('/products', function () {
        // Dummy logic
        return view('admin.products.index');
    });

    Route::get('/orders', function () {
        return view('admin.orders.index');
    });

    Route::get('/categories', function () {
        return view('admin.categories.index');
    });
});
