@extends('layouts.app')

@section('title', 'El Yapımı Ürünler — AhşapEvim | Masif Ahşap Çerçeve Koleksiyonu')

@section('content')

{{-- Warm Hero Banner (Atölye & El İşçiliği Teması) --}}
<div class="relative overflow-hidden bg-white/80 backdrop-blur-sm">
    {{-- Overlay to tone down the body pattern inside the hero --}}
    <div class="absolute inset-0 bg-white/60"></div>
    <div class="container mx-auto px-4 py-9 relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 bg-brand/10 text-brand text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-3">
                    <i class="fa-solid fa-hammer text-xs"></i> El İşçiliği Koleksiyonu
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight mb-2">
                    Her Çerçeve Bir <span class="text-brand">El Emeği</span>
                </h1>
                <p class="text-sm md:text-base text-gray-600 max-w-lg leading-relaxed">
                    Manisa'daki küçük atölyemizde, her ürün baştan sona el işçiliğiyle tamamlanıyor.<br>
                    <span class="font-semibold text-gray-700">Masif ahşap · 45° gönyeli birleşim · Kişiye özel üretim</span>
                </p>
                <div class="flex items-center gap-4 mt-4 text-sm text-gray-500 font-medium">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-seedling text-green-600"></i> %100 Doğal Malzeme</span>
                    <span class="hidden sm:flex items-center gap-1.5"><i class="fa-solid fa-truck-fast text-brand"></i> Ücretsiz Kargo</span>
                    <span class="hidden sm:flex items-center gap-1.5"><i class="fa-solid fa-rotate-left text-blue-600"></i> 14 Gün İade</span>
                </div>
            </div>
            {{-- Atölye ürün görseli --}}
            <div class="hidden lg:flex flex-col items-center gap-2 shrink-0">
                <div class="w-36 h-36 rounded-2xl overflow-hidden shadow-xl">
                    <img src="/artisan_frame_hero.png" alt="El Yapımı Masif Ahşap Çerçeve" class="w-full h-full object-cover">
                </div>
                <span class="text-[11px] text-amber-700 font-bold uppercase tracking-wider">El yapımı atölye ürünü</span>
            </div>
        </div>
    </div>
</div>

<div class="min-h-screen pb-16 pt-8">
    <div class="container mx-auto px-4">

        {{-- Product Grid --}}
        <div class="w-full">

            {{-- Top Bar --}}
            <div class="flex justify-between items-center mb-5 bg-white px-5 py-3.5 rounded-2xl shadow-sm">
                <div class="flex items-center gap-2.5">
                    <span class="text-base font-extrabold text-gray-900">
                        {{ request('category') ? ($categories->where('slug', request('category'))->first()->name ?? 'Ürünler') : 'Tüm El Yapımı Ürünler' }}
                    </span>
                    <span class="text-xs bg-brand/10 text-brand font-bold px-2 py-0.5 rounded-full">{{ $products->total() }} ürün</span>
                </div>
                <div class="hidden sm:flex items-center gap-1.5 text-xs text-gray-500 font-semibold">
                    <i class="fa-solid fa-clock text-amber-500"></i> Sipariş anında üretim başlar
                </div>
            </div>

            {{-- Warm Products Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @forelse($products as $product)
                    @php
                        $addImgs = is_array($product->features['images'] ?? null) ? $product->features['images'] : [];
                        $allImages = array_values(array_filter(array_merge([$product->image ?: '/cerceve.png'], $addImgs)));
                    @endphp

                    {{-- Product Card (Warm Artisan Style) --}}
                    <a href="/product/{{ $product->id }}" class="product-card group flex flex-col rounded-2xl overflow-hidden transition-all duration-300 relative">
                        
                        {{-- Favorite --}}
                        <button class="absolute top-3 right-3 z-20 w-8 h-8 bg-white/95 rounded-full shadow flex items-center justify-center text-gray-300 hover:text-brand transition" onclick="event.preventDefault();">
                            <i class="fa-solid fa-heart text-sm"></i>
                        </button>

                        {{-- Image Area --}}
                        <div class="relative pt-[115%] w-full bg-[#fdf6ec] overflow-hidden"
                             onmousemove="hoverCardImage(event, this)"
                             onmouseleave="resetCardImage(this)">
                            <img src="{{ $allImages[0] }}" alt="{{ $product->name }}"
                                 class="card-preview-img absolute inset-0 w-full h-full object-contain p-3 transition-all duration-300 group-hover:scale-105"
                                 data-images="{{ json_encode($allImages) }}"
                                 data-default="{{ $allImages[0] }}">
                            
                            @if(count($allImages) > 1)
                                <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                    @foreach($allImages as $idx => $img)
                                        <span class="dot-indicator w-1.5 h-1.5 rounded-full {{ $idx === 0 ? 'bg-brand' : 'bg-gray-300' }} transition-colors"></span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- El yapımı badge --}}
                            <div class="absolute top-3 left-3 bg-amber-700/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-full tracking-wide">
                                El Yapımı
                            </div>

                            @if($product->stock > 0)
                                <div class="absolute bottom-0 left-0 bg-green-600/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-tr-xl">
                                    ✓ Stokta
                                </div>
                            @endif
                        </div>

                        {{-- Card Body --}}
                        <div class="p-3.5 flex flex-col flex-grow">
                            <div class="text-[12px] text-amber-700 font-bold uppercase tracking-wider mb-1">AhşapEvim Atölyesi</div>
                            <div class="text-[13px] font-bold text-gray-900 leading-snug mb-2 h-10 overflow-hidden">
                                {{ $product->name }}
                            </div>

                            {{-- Star Rating --}}
                            <div class="flex items-center gap-1 mb-3">
                                <div class="flex text-amber-400 text-[11px]">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                                </div>
                                <span class="text-[11px] text-gray-400 font-medium">({{ rand(8,42) }})</span>
                            </div>

                            {{-- Price --}}
                            <div class="mt-auto flex items-end justify-between">
                                <div>
                                    <div class="text-brand font-extrabold text-lg leading-none">{{ number_format($product->price, 2, ',', '.') }} ₺</div>
                                    @if($product->original_price > $product->price)
                                        <div class="text-xs text-gray-400 line-through mt-0.5">{{ number_format($product->original_price, 2, ',', '.') }} ₺</div>
                                    @endif
                                </div>
                                <div class="w-8 h-8 rounded-xl bg-brand/10 group-hover:bg-brand flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-cart-plus text-brand group-hover:text-white text-sm transition-colors"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center">
                        <i class="fa-solid fa-tree text-6xl text-amber-200 mb-4 block"></i>
                        <div class="text-gray-500 font-medium">Bu kategoride henüz ürün bulunmamaktadır.</div>
                        <a href="/products" class="inline-flex items-center gap-2 mt-4 text-sm text-brand font-bold hover:underline">
                            <i class="fa-solid fa-arrow-left"></i> Tüm ürünlere dön
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $products->links() }}
            </div>

            {{-- Atölye Banner (alt kısım) --}}
            @if($products->count() > 0)
            <div class="mt-10 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-hammer text-3xl text-amber-700"></i>
                    </div>
                    <div>
                        <div class="font-extrabold text-gray-900 text-base mb-0.5">Özel ölçü veya tasarım mı istiyorsunuz?</div>
                        <div class="text-sm text-gray-600">Atölyemizle doğrudan iletişime geçin, sizin için özel üretelim.</div>
                    </div>
                </div>
                <a href="https://wa.me/905xxxxxxxxx" target="_blank" class="shrink-0 inline-flex items-center gap-2.5 bg-green-600 hover:bg-green-700 text-white font-bold text-sm px-5 py-3 rounded-xl transition shadow-md shadow-green-200 whitespace-nowrap">
                    <i class="fa-brands fa-whatsapp text-lg"></i> WhatsApp'tan Yazın
                </a>
            </div>
            @endif
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
            dot.classList.toggle('bg-brand', i === index);
            dot.classList.toggle('bg-gray-300', i !== index);
        });
    }
}

function resetCardImage(container) {
    const img = container.querySelector('.card-preview-img');
    if (!img) return;
    img.src = img.dataset.default;
    const dots = container.querySelectorAll('.dot-indicator');
    dots.forEach((dot, i) => {
        dot.classList.toggle('bg-brand', i === 0);
        dot.classList.toggle('bg-gray-300', i !== 0);
    });
}
</script>
@endsection
