@extends('layouts.app')

@section('title', 'Tüm Ürünler - AhşapEvim')

@section('content')
<div class="bg-gray-50 pb-12 pt-4">
    <div class="container mx-auto px-4 flex gap-6">
        
        <!-- Sidebar (Dinamik Kategoriler) -->
        <div class="hidden lg:block w-64 flex-shrink-0">
            <div class="bg-white border border-gray-200 rounded-lg p-4 sticky top-24">
                <h3 class="font-bold text-gray-800 mb-4 text-sm">Kategoriler</h3>
                <ul class="text-[13px] text-gray-600 space-y-3">
                    <li>
                        <a href="/products" class="hover:text-brand transition flex justify-between items-center {{ !request('category') ? 'text-brand font-bold' : '' }}">
                            <span>Tüm Ürünler</span>
                        </a>
                    </li>
                    @foreach($categories as $cat)
                        <li>
                            <a href="/products?category={{ $cat->slug }}" class="hover:text-brand transition flex justify-between items-center {{ request('category') === $cat->slug ? 'text-brand font-bold' : '' }}">
                                <span>{{ $cat->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1">
            <div class="flex justify-between items-center mb-4 bg-white p-4 rounded-lg border border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">
                    {{ request('category') ? ($categories->where('slug', request('category'))->first()->name ?? 'Ürünler') : 'Tüm Ürünler' }}
                </h1>
                <span class="text-xs text-gray-500 font-semibold">{{ $products->total() }} Ürün Listeleniyor</span>
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
                    <div class="col-span-full py-12 text-center text-gray-500">Bu kategoride henüz ürün bulunmamaktadır.</div>
                @endforelse
            </div>

            <!-- Pagination -->
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
