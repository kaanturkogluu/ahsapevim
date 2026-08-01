@extends('layouts.app')

@section('title', 'Favorilerim — AhşapEvim')

@section('content')
<div class="bg-gray-50 pb-16 min-h-screen pt-8">
    <div class="container mx-auto px-4">

        <div class="flex items-center justify-between mb-8 pb-4 border-b border-amber-100">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 font-serif">Favorilerim</h1>
                <p class="text-xs text-gray-500 mt-1">Beğendiğiniz ve daha sonra incelemek üzere kaydettiğiniz masif ahşap tasarımlar.</p>
            </div>
            <a href="{{ url('/urunler') }}" class="text-xs font-bold text-[#C87A53] hover:underline flex items-center gap-1">
                <i class="fa-solid fa-store"></i> Tüm Ürünleri İncele
            </a>
        </div>

        @if($favorites->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-amber-100 max-w-lg mx-auto">
                <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center text-amber-500 text-3xl mx-auto mb-4">
                    <i class="fa-regular fa-heart"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Henüz Favori Ürününüz Yok</h3>
                <p class="text-xs text-gray-500 mb-6">Beğendiğiniz ürünlerin üzerindeki kalp simgesine tıklayarak favorilerinize ekleyebilirsiniz.</p>
                <a href="{{ url('/urunler') }}" class="inline-block bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold py-3 px-8 rounded-xl transition shadow-md text-sm">
                    Ürünleri Keşfet
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6" id="favoritesGrid">
                @foreach($favorites as $product)
                    <div class="product-card group relative flex flex-col justify-between" id="favCard-{{ $product->id }}">
                        
                        {{-- Favorite Toggle Button --}}
                        <button type="button" onclick="toggleFavorite({{ $product->id }}, this)" class="absolute top-3 right-3 z-20 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-red-500 shadow-md hover:scale-110 transition" title="Favorilerden Çıkar">
                            <i class="fa-solid fa-heart text-sm"></i>
                        </button>

                        {{-- Discount Badge --}}
                        @if($product->discount_percent > 0)
                            <div class="absolute top-3 left-3 z-20 bg-red-600 text-white font-extrabold text-[10px] px-2 py-0.5 rounded-full shadow">
                                %{{ $product->discount_percent }} İNDİRİM
                            </div>
                        @endif

                        <a href="{{ url('/urun/' . $product->id) }}" class="block p-4">
                            <div class="aspect-square w-full rounded-xl bg-stone-100 overflow-hidden mb-3 relative flex items-center justify-center p-2">
                                <img src="{{ $product->image ?: '/cerceve.png' }}" alt="{{ $product->name }}" class="w-full h-full object-contain mix-blend-multiply group-hover:scale-105 transition duration-300">
                            </div>

                            <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider block mb-1">
                                {{ $product->category->name ?? 'Ahşap' }}
                            </span>
                            <h3 class="text-xs font-bold text-gray-800 line-clamp-2 min-h-[32px] group-hover:text-brand transition">
                                {{ $product->name }}
                            </h3>

                            <div class="mt-3 flex items-baseline gap-2">
                                <span class="text-sm font-extrabold text-[#C87A53]">
                                    {{ number_format($product->price, 2, ',', '.') }} TL
                                </span>
                                @if($product->original_price && $product->original_price > $product->price)
                                    <span class="text-[11px] text-gray-400 line-through">
                                        {{ number_format($product->original_price, 2, ',', '.') }} TL
                                    </span>
                                @endif
                            </div>
                        </a>

                        <div class="p-4 pt-0">
                            <a href="{{ url('/urun/' . $product->id) }}" class="w-full py-2.5 bg-amber-50 hover:bg-[#C87A53] text-[#C87A53] hover:text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 border border-amber-200 hover:border-[#C87A53]">
                                <i class="fa-solid fa-eye"></i> İncele & Sepete Ekle
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection