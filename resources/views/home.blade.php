@extends('layouts.app')

@section('content')
<div class="bg-gray-50 pb-12">
    <!-- Main Banner Widgets -->
    <div class="container mx-auto px-4 pt-6">
        <div class="flex gap-4">
            <!-- Left Big Banner -->
            <div class="w-full lg:w-2/3 h-80 bg-brand-light rounded-xl overflow-hidden relative group cursor-pointer border border-gray-200">
                <div class="absolute inset-0 flex items-center justify-between p-12">
                    <div class="max-w-md z-10">
                        <h2 class="text-4xl font-extrabold text-gray-800 mb-4 tracking-tight">Kişiye Özel<br><span class="text-brand">Hediyelikler</span></h2>
                        <p class="text-gray-700 text-lg mb-6">Sevdiklerinize özel tasarlanmış eşyalar ile onları mutlu edin.</p>
                        <a href="/products?category=hediyelik-esya" class="bg-brand hover:bg-brand-dark text-white font-bold py-3 px-8 rounded-md transition shadow-md inline-block">Alışverişe Başla</a>
                    </div>
                </div>
                <img src="/cerceve.png" alt="Kampanya" class="absolute right-0 bottom-0 h-[120%] object-contain drop-shadow-2xl transform rotate-12 group-hover:scale-105 transition duration-500 origin-bottom">
            </div>

            <!-- Right Small Banners -->
            <div class="hidden lg:flex w-1/3 flex-col gap-4">
                <div class="flex-1 bg-yellow-50 rounded-xl overflow-hidden relative cursor-pointer group border border-gray-200 p-6 flex flex-col justify-center">
                    <h3 class="text-2xl font-bold text-gray-800 z-10">Yeni Gelenler</h3>
                    <p class="text-gray-600 text-sm mt-1 z-10">Ahşap Çerçeveler</p>
                    <img src="/cerceve.png" class="absolute right-2 -bottom-4 w-32 object-contain group-hover:scale-110 transition duration-300">
                </div>
                <div class="flex-1 bg-blue-50 rounded-xl overflow-hidden relative cursor-pointer group border border-gray-200 p-6 flex flex-col justify-center">
                    <h3 class="text-2xl font-bold text-gray-800 z-10">Çok Satanlar</h3>
                    <p class="text-gray-600 text-sm mt-1 z-10">Gece Lambaları</p>
                    <img src="/cerceve.png" class="absolute right-2 -bottom-4 w-32 object-contain group-hover:scale-110 transition duration-300">
                </div>
            </div>
        </div>
    </div>

    <!-- Layout with Sidebar -->
    <div class="container mx-auto px-4 mt-8 flex gap-6">
        
        <!-- Sidebar (Dinamik Kategoriler) -->
        <div class="hidden lg:block w-64 flex-shrink-0">
            <div class="bg-white border border-gray-200 rounded-lg p-4 sticky top-24">
                <h3 class="font-bold text-gray-800 mb-4 text-sm">Tüm Kategoriler</h3>
                <ul class="text-[13px] text-gray-600 space-y-3">
                    @forelse($categories as $cat)
                        <li>
                            <a href="/products?category={{ $cat->slug }}" class="hover:text-brand transition flex justify-between items-center">
                                <span>{{ $cat->name }}</span>
                                <span class="text-xs text-gray-400">({{ $cat->products_count }})</span>
                            </a>
                        </li>
                    @empty
                        <li><a href="#" class="hover:text-brand transition">Ahşap Çerçeveler</a></li>
                    @endforelse
                </ul>

                <hr class="my-4 border-gray-100">
                
                <h3 class="font-bold text-gray-800 mb-4 text-sm">Fiyat Filtresi</h3>
                <ul class="text-[13px] text-gray-600 space-y-2">
                    <li>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="accent-brand w-4 h-4 rounded border-gray-300">
                            0 TL - 200 TL
                        </label>
                    </li>
                    <li>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="accent-brand w-4 h-4 rounded border-gray-300">
                            200 TL - 500 TL
                        </label>
                    </li>
                    <li>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="accent-brand w-4 h-4 rounded border-gray-300">
                            500 TL ve Üzeri
                        </label>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1">
            <div class="flex justify-between items-center mb-4 bg-white p-3 rounded-lg border border-gray-200">
                <h2 class="text-lg font-bold text-gray-800">Senin İçin Seçtiklerimiz</h2>
                <a href="/products" class="text-sm font-semibold text-gray-600 hover:text-brand transition">Tümünü Gör <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i></a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($products as $product)
                    @php
                        $addImgs = is_array($product->features['images'] ?? null) ? $product->features['images'] : [];
                        $allImages = array_values(array_filter(array_merge([$product->image ?: '/cerceve.png'], $addImgs)));
                    @endphp

                    <!-- Product Card -->
                    <a href="/product/{{ $product->id }}" class="product-card bg-white rounded-lg border border-gray-200 overflow-hidden group flex flex-col h-full hover:border-gray-300 transition duration-200 relative">
                        <!-- Favorite Icon -->
                        <button class="absolute top-3 right-3 z-20 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center text-gray-400 hover:text-brand transition" onclick="event.preventDefault();">
                            <i class="fa-solid fa-heart text-sm"></i>
                        </button>

                        <div class="relative pt-[130%] w-full bg-gray-50 border-b border-gray-100 overflow-hidden"
                             onmousemove="hoverCardImage(event, this)"
                             onmouseleave="resetCardImage(this)">
                            <img src="{{ $allImages[0] }}" alt="{{ $product->name }}"
                                 class="card-preview-img absolute inset-0 w-full h-full object-contain p-4 mix-blend-multiply transition-all duration-200"
                                 data-images="{{ json_encode($allImages) }}"
                                 data-default="{{ $allImages[0] }}">
                            
                            @if(count($allImages) > 1)
                                <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                    @foreach($allImages as $idx => $img)
                                        <span class="dot-indicator w-1.5 h-1.5 rounded-full {{ $idx === 0 ? 'bg-brand' : 'bg-gray-300' }} transition-colors"></span>
                                    @endforeach
                                </div>
                            @endif

                            @if($product->stock > 0)
                                <div class="absolute bottom-0 left-0 bg-gray-800 text-white text-[10px] font-bold px-2 py-1 rounded-tr-md z-10">KARGO BEDAVA</div>
                            @endif
                        </div>
                        <div class="p-3 flex flex-col flex-grow">
                            <div class="text-[13px] text-gray-700 leading-tight mb-2 h-10 overflow-hidden">
                                <span class="font-bold text-gray-900">AhşapEvim</span> {{ $product->name }}
                            </div>
                            <div class="flex items-center gap-1 mb-2">
                                <div class="flex text-yellow-400 text-[10px]">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                                <span class="text-[11px] text-gray-500">(15)</span>
                            </div>
                            <div class="mt-auto pt-2">
                                <div class="text-brand font-bold text-lg leading-none">{{ number_format($product->price, 2, ',', '.') }} TL</div>
                                @if($product->original_price > $product->price)
                                    <div class="text-xs text-gray-400 line-through mt-1">{{ number_format($product->original_price, 2, ',', '.') }} TL</div>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-8 text-center text-gray-500">Henüz eklenmiş ürün bulunmamaktadır.</div>
                @endforelse
            </div>
            
            <div class="mt-8 flex justify-center">
                {{ $products->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>

<script>
function hoverCardImage(e, container) {
    const img = container.querySelector('.card-preview-img');
    if (!img) return;
    const images = JSON.parse(img.dataset.images || '[]');
    if (images.length <= 1) return;
    const rect = container.getBoundingClientRect();
    const x = Math.max(0, e.clientX - rect.left);
    const index = Math.min(Math.floor((x / rect.width) * images.length), images.length - 1);
    
    if (images[index] && img.src !== images[index]) {
        img.src = images[index];
        const dots = container.querySelectorAll('.dot-indicator');
        dots.forEach((dot, i) => {
            if (i === index) {
                dot.classList.remove('bg-gray-300');
                dot.classList.add('bg-brand');
            } else {
                dot.classList.remove('bg-brand');
                dot.classList.add('bg-gray-300');
            }
        });
    }
}

function resetCardImage(container) {
    const img = container.querySelector('.card-preview-img');
    if (!img) return;
    const defaultSrc = img.dataset.default;
    img.src = defaultSrc;
    const dots = container.querySelectorAll('.dot-indicator');
    dots.forEach((dot, i) => {
        if (i === 0) {
            dot.classList.remove('bg-gray-300');
            dot.classList.add('bg-brand');
        } else {
            dot.classList.remove('bg-brand');
            dot.classList.add('bg-gray-300');
        }
    });
}
</script>
@endsection
