@extends('layouts.app')

@section('title', 'El Yapımı Ürünler — AhşapEvim | Masif Ahşap Çerçeve Koleksiyonu')

@section('content')

{{-- Products Banner — Authentic Atelier Style --}}
<div class="relative overflow-hidden" style="background: #f5efe6;">

    {{-- Subtle horizontal wood-grain stripe --}}
    <div class="absolute top-0 left-0 right-0 h-1" style="background: repeating-linear-gradient(90deg, #c4956a 0px, #b07d50 18px, #d4a874 36px, #b8845a 54px, #c4956a 72px);"></div>

    <div class="container mx-auto px-4" style="padding-top: 2.75rem; padding-bottom: 2.75rem;">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8 md:gap-16">

            {{-- Left: Text block --}}
            <div class="max-w-xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#9c6c3c] mb-3" style="font-family: Georgia, serif; letter-spacing: 0.2em;">
                    Manisa Atölyesi — El Yapımı
                </p>

                <h1 class="font-extrabold text-gray-900 leading-[1.08] mb-4" style="font-size: clamp(1.75rem, 4vw, 2.6rem); font-family: Georgia, 'Times New Roman', serif;">
                    Masif Ahşap,<br>
                    <span style="color: #a0622a;">Kişiye Özel Üretim</span>
                </h1>

                <p class="text-gray-600 leading-relaxed mb-5" style="font-size: 0.9rem; max-width: 42ch;">
                    Her ürün tek tek elle şekillendirilir, 45° gönyeli köşelerle birleştirilir 
                    ve sipariş alındıktan sonra üretilir. Seri değil, özel.
                </p>

                <div class="flex items-center gap-6 text-[0.8rem] text-gray-500 font-medium border-t border-[#d5b896]/60 pt-4">
                    <span>%100 Masif Ahşap</span>
                    <span class="text-[#c4956a]">·</span>
                    <span>Ücretsiz Kargo</span>
                    <span class="text-[#c4956a]">·</span>
                    <span>14 Gün İade</span>
                </div>
            </div>

            {{-- Right: Staggered image mosaic with hand-label --}}
            <div class="hidden lg:flex items-end gap-3 shrink-0">
                {{-- Tall frame --}}
                <div class="flex flex-col items-center gap-2">
                    <div class="rounded-xl overflow-hidden shadow-md" style="width: 88px; height: 128px; border: 2px solid #c8a07a;">
                        <img src="/artisan_frame_hero.png" alt="Ahşap Çerçeve" class="w-full h-full object-cover">
                    </div>
                </div>
                {{-- Short frame offset upward --}}
                <div class="flex flex-col items-center gap-2 mb-5">
                    <div class="rounded-xl overflow-hidden shadow-md" style="width: 78px; height: 100px; border: 2px solid #c8a07a; transform: rotate(-2deg);">
                        <img src="/artisan_frame_hero.png" alt="Ahşap Çerçeve" class="w-full h-full object-cover object-top">
                    </div>
                </div>
                {{-- Stamp label --}}
                <div class="mb-2 text-center" style="width: 72px;">
                    <div style="border: 2px solid #9c6c3c; border-radius: 4px; padding: 6px 4px; transform: rotate(2deg); opacity: 0.85;">
                        <p class="font-bold text-[9px] uppercase tracking-widest text-[#6b3e1a]" style="font-family: Georgia, serif; line-height: 1.5;">El<br>Yapımı<br>✦ 2024</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Bottom grain stripe --}}
    <div class="absolute bottom-0 left-0 right-0 h-[2px]" style="background: repeating-linear-gradient(90deg, #c4956a 0px, #a07040 24px, #d4a874 48px, #b8845a 72px, #c4956a 96px); opacity: 0.5;"></div>
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
                    <i class="fa-solid fa-clock text-brand"></i> Sipariş anında üretim başlar
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
                    <a href="{{ url('/urun/' . $product->id) }}" class="product-card group flex flex-col rounded-2xl overflow-hidden transition-all duration-300 relative">
                        
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
                            <div class="absolute top-3 left-3 bg-brand/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-full tracking-wide">
                                El Yapımı
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
                        <i class="fa-solid fa-tree text-6xl text-brand/35 mb-4 block"></i>
                        <div class="text-gray-500 font-medium">Bu kategoride henüz ürün bulunmamaktadır.</div>
                        <a href="{{ url('/urunler') }}" class="inline-flex items-center gap-2 mt-4 text-sm text-brand font-bold hover:underline">
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
            <div class="mt-10 bg-white border border-wood-light rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-brand/10 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-hammer text-3xl text-brand"></i>
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
