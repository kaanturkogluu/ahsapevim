@extends('layouts.app')

@section('title', 'El Yapımı Ürünler — AhşapEvim | Masif Ahşap Çerçeve Koleksiyonu')

@section('content')

{{-- Full-Bleed Edge-to-Edge Hero Banner (Full Viewport Width 2400x600) --}}
<div class="relative w-full overflow-hidden bg-transparent">
    <picture class="w-full h-auto block">
        @if(file_exists(public_path('images/hero-banner-desktop.png')))
            <source media="(min-width: 768px)" srcset="{{ url('/images/hero-banner-desktop.png') }}">
        @endif
        
        @if(file_exists(public_path('images/hero-banner-mobile.png')))
            <img src="{{ url('/images/hero-banner-mobile.png') }}" 
                 alt="AhşapEvim — Masif Ahşap El İşçiliği" 
                 class="w-full h-auto block">
        @else
            <img src="{{ url('/images/hero-banner.png') }}" 
                 alt="AhşapEvim — Masif Ahşap El İşçiliği" 
                 class="w-full h-auto block">
        @endif
    </picture>
</div>


<div class="min-h-screen pb-16 pt-8">
    <div class="container mx-auto px-4">

        {{-- Product Grid --}}
        <div class="w-full">

            {{-- Top Bar --}}
            <div class="flex flex-wrap justify-between items-center mb-4 bg-white px-5 py-3.5 rounded-2xl shadow-sm gap-3">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="text-base font-extrabold text-gray-900">
                        @if(request('q') || request('search'))
                            Arama Sonuçları: "{{ request('q') ?: request('search') }}"
                        @elseif(request('category'))
                            {{ $categories->where('slug', request('category'))->first()->name ?? 'Ürünler' }}
                        @else
                            Tüm El Yapımı Ürünler
                        @endif
                    </span>
                    <span class="text-xs bg-brand/10 text-brand font-bold px-2 py-0.5 rounded-full">{{ $products->total() }} ürün</span>
                    @if(request('q') || request('search'))
                        <a href="{{ url('/urunler') }}" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-2.5 py-1 rounded-lg transition inline-flex items-center gap-1">
                            <i class="fa-solid fa-xmark"></i> Aramayı Temizle
                        </a>
                    @endif
                </div>
                <div class="hidden sm:flex items-center gap-1.5 text-xs text-gray-500 font-semibold">
                    <i class="fa-solid fa-clock text-brand"></i> Sipariş anında üretim başlar
                </div>
            </div>

            {{-- Temsili Görsel Bilgilendirme Çıtası --}}
            <div class="mb-5 p-3.5 bg-amber-50/90 border border-amber-200/90 rounded-2xl text-amber-950 text-xs flex items-center gap-3 shadow-2xs">
                <i class="fa-solid fa-circle-info text-amber-600 text-base shrink-0"></i>
                <div class="leading-relaxed">
                    <strong>📌 Önemli Bilgilendirme:</strong> Ürün görsellerindeki fotoğraflar temsilidir. Gönderilecek ahşap çerçevede ürün resmindeki fotoğraf değil, <strong>sipariş verirken yükleyeceğiniz kendi fotoğrafınız</strong> basılarak hazırlanacaktır.
                </div>
            </div>

            {{-- Warm Products Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @forelse($products as $product)
                    @php
                        $allImages = array_values(array_filter(array_merge([$product->image], $product->gallery_urls)));
                    @endphp

                    {{-- Product Card (Warm Artisan Style) --}}
                    <a href="{{ $product->url }}" class="product-card group flex flex-col rounded-2xl overflow-hidden transition-all duration-300 relative">
                        
                        {{-- Favorite --}}
                        <button type="button" class="absolute top-3 right-3 z-30 w-9 h-9 bg-white/95 backdrop-blur-sm rounded-full shadow-md border border-gray-100 flex items-center justify-center hover:scale-110 transition duration-200" onclick="event.preventDefault(); event.stopPropagation(); toggleFavorite({{ $product->id }}, this);" title="Favorilere Ekle">
                            <i class="{{ $product->isFavoritedBy() ? 'fa-solid fa-heart text-red-500 text-base drop-shadow-sm scale-110' : 'fa-regular fa-heart text-gray-500 text-base hover:text-red-500' }}"></i>
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
                            <div class="absolute top-3 left-3 bg-brand/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-full tracking-wide">
                                El Yapımı
                            </div>

                            {{-- Görsel Temsilidir Badge --}}
                            <div class="absolute bottom-2.5 left-3 bg-black/60 backdrop-blur-xs text-white text-[9px] font-bold px-2 py-0.5 rounded-md z-10">
                                Görsel Temsilidir
                            </div>

                            @if($product->discount_percent > 0)
                                <div class="absolute top-3 right-12 bg-red-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow z-10">
                                    %{{ $product->discount_percent }} İndirim
                                </div>
                            @endif

                            @if($product->stock > 0)
                                <div class="absolute bottom-0 left-0 bg-green-600/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-tr-xl">
                                    ✓ Stokta
                                </div>
                            @endif
                        </div>

                        {{-- Card Body --}}
                        <div class="p-3.5 flex flex-col flex-grow">
                            <div class="text-[12px] text-brand font-bold uppercase tracking-wider mb-1">AhşapEvim Atölyesi</div>
                            <div class="text-[13px] font-bold text-gray-900 leading-snug mb-2 h-10 overflow-hidden">
                                {{ $product->name }}
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
                    <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-gray-100 p-8 shadow-xs">
                        <i class="fa-solid fa-magnifying-glass-location text-6xl text-brand/35 mb-4 block"></i>
                        @if(request('q') || request('search'))
                            <div class="text-gray-900 font-bold text-lg mb-1">"{{ request('q') ?: request('search') }}" ile eşleşen ürün bulunamadı</div>
                            <p class="text-xs text-gray-500 mb-6 max-w-sm mx-auto">Farklı bir kelime yazarak arama yapabilir veya tüm masif ahşap çerçeve koleksiyonumuzu inceleyebilirsiniz.</p>
                        @else
                            <div class="text-gray-500 font-medium mb-4">Bu kategoride henüz ürün bulunmamaktadır.</div>
                        @endif
                        <a href="{{ url('/urunler') }}" class="inline-flex items-center gap-2 text-xs bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold px-6 py-3 rounded-xl shadow-md transition">
                            <i class="fa-solid fa-store"></i> Tüm Ürünleri İncele
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $products->links() }}
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
