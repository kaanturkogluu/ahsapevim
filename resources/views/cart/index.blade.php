@extends('layouts.app')

@section('title', 'Sepetim - AhşapEvim')

@section('content')
<div class="bg-gray-50 pb-12 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Sepetim</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(empty($cart))
            <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
                <i class="fa-solid fa-cart-arrow-down text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-700 mb-2">Sepetiniz Boş</h2>
                <p class="text-gray-500 mb-6">Sepetinizde henüz ürün bulunmamaktadır.</p>
                <a href="{{ url('/urunler') }}" class="inline-block bg-brand hover:bg-brand-dark text-white font-bold py-3 px-8 rounded-lg transition shadow-md">
                    Alışverişe Başla
                </a>
            </div>
        @else
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items -->
                <div class="w-full lg:w-3/4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        @php $total = 0; @endphp
                        @foreach($cart as $key => $item)
                            @php 
                                $total += $item['price'] * $item['quantity']; 
                                $customImgUrl = !empty($item['custom_image']) ? (str_starts_with($item['custom_image'], 'http') ? $item['custom_image'] : url($item['custom_image'])) : null;
                                $baseFrameImg = !empty($item['base_image']) 
                                    ? (str_starts_with($item['base_image'], 'http') ? $item['base_image'] : url($item['base_image']))
                                    : url('/cerceve.png');
                            @endphp
                            <div class="flex flex-col sm:flex-row items-center gap-6 p-6 border-b border-gray-100 last:border-b-0">
                                
                                <!-- Product Image (Frame Template + Applied Custom Photo Overlay) -->
                                <div class="w-32 h-36 flex-shrink-0 bg-stone-100 rounded-xl relative overflow-hidden border border-gray-200 p-2 flex items-center justify-center group shadow-inner cursor-pointer" onclick="openCartPreviewModal('{{ $customImgUrl ?: $baseFrameImg }}', '{{ addslashes($item['name']) }}', '{{ $key }}')">
                                    <img src="{{ $baseFrameImg }}" alt="{{ $item['name'] }}" class="w-full h-full object-contain relative z-0 mix-blend-multiply">
                                    
                                    @if($customImgUrl)
                                        <!-- Overlaid Custom Photo inside inner frame area -->
                                        <div class="absolute inset-0 flex items-center justify-center p-3.5 z-10 pointer-events-none">
                                            <div class="w-[58%] h-[58%] relative overflow-hidden rounded-xs shadow-md border border-black/20 bg-white">
                                                <img src="{{ $customImgUrl }}" alt="Özel Fotoğraf" class="w-full h-full object-cover">
                                            </div>
                                        </div>
                                        
                                        <!-- Interactive Preview Zoom Icon -->
                                        <button type="button" onclick="event.stopPropagation(); openCartPreviewModal('{{ $customImgUrl }}', '{{ addslashes($item['name']) }}', '{{ $key }}')" class="absolute bottom-1.5 right-1.5 z-20 w-6 h-6 bg-[#C87A53] text-white rounded-full flex items-center justify-center text-[10px] shadow hover:scale-110 transition" title="Fotoğrafı Büyüt">
                                            <i class="fa-solid fa-magnifying-glass-plus"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Details -->
                                <div class="flex-1 text-center sm:text-left">
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">
                                        <a href="{{ url('/urun/' . $item['product_id']) }}" class="hover:text-brand transition">{{ $item['name'] }}</a>
                                    </h3>
                                    
                                    @if($customImgUrl)
                                        <div class="inline-flex items-center gap-2 bg-amber-50 text-amber-900 border border-amber-200/80 text-xs font-bold px-2.5 py-1 rounded-md mb-2">
                                            <i class="fa-solid fa-wand-magic-sparkles text-amber-600"></i>
                                            <span>Yüklenen Fotoğraf:</span>
                                            <img src="{{ $customImgUrl }}" class="w-5 h-5 object-cover rounded border border-amber-300 cursor-pointer" onclick="openCartPreviewModal('{{ $customImgUrl }}', 'Orijinal Yüklenen Fotoğraf')">
                                            <button type="button" onclick="openCartPreviewModal('{{ $customImgUrl }}', 'Orijinal Yüklenen Fotoğraf')" class="text-brand hover:underline font-extrabold ml-1">
                                                (Orijinal Fotoğrafı İncele)
                                            </button>
                                        </div>
                                    @endif
                                    
                                    <div class="text-brand font-extrabold text-xl mt-2">
                                        {{ number_format($item['price'], 2, ',', '.') }} TL
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="key" value="{{ $key }}">
                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                        <button type="submit" {{ $item['quantity'] <= 1 ? 'disabled' : '' }} class="w-8 h-8 rounded-full bg-gray-150 hover:bg-gray-200 disabled:opacity-50 flex items-center justify-center font-bold text-gray-700 transition border border-gray-200 select-none">
                                            -
                                        </button>
                                    </form>
                                    
                                    <span class="w-8 text-center font-bold text-gray-800">{{ $item['quantity'] }}</span>
                                    
                                    <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="key" value="{{ $key }}">
                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                        <button type="submit" class="w-8 h-8 rounded-full bg-gray-150 hover:bg-gray-200 flex items-center justify-center font-bold text-gray-700 transition border border-gray-200 select-none">
                                            +
                                        </button>
                                    </form>
                                </div>

                                <!-- Remove -->
                                <form action="{{ route('cart.remove') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="key" value="{{ $key }}">
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition p-2" title="Sepetten Kaldır">
                                        <i class="fa-solid fa-trash-can text-xl"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="w-full lg:w-1/4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Sipariş Özeti</h3>
                        
                        <div class="flex justify-between items-center mb-3 text-sm text-gray-600">
                            <span>Ürünler Toplamı</span>
                            <span class="font-bold">{{ number_format($total, 2, ',', '.') }} TL</span>
                        </div>
                        
                        <div class="flex justify-between items-center mb-4 text-sm text-gray-600">
                            <span>Kargo Ücreti</span>
                            <span class="text-green-600 font-bold">Ücretsiz</span>
                        </div>
                        
                        <div class="border-t border-gray-100 pt-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-bold text-gray-800">Genel Toplam</span>
                                <span class="text-2xl font-extrabold text-[#C87A53]">{{ number_format($total, 2, ',', '.') }} TL</span>
                            </div>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="w-full bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold py-3.5 px-4 rounded-lg transition text-base shadow-md flex items-center justify-center gap-2">
                            Alışverişi Tamamla
                            <i class="fa-solid fa-chevron-right text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Image Preview Modal -->
<div id="cartPreviewModal" class="fixed inset-0 z-[99999] bg-black/80 hidden items-center justify-center p-4 backdrop-blur-sm" onclick="closeCartPreviewModal()">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full relative shadow-2xl border border-gray-100" onclick="event.stopPropagation()">
        <button type="button" onclick="closeCartPreviewModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl font-bold leading-none">&times;</button>
        <h4 id="cartPreviewTitle" class="text-base font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2">Kişiselleştirilmiş Fotoğraf</h4>
        <div class="bg-stone-100 p-4 rounded-xl flex items-center justify-center max-h-[70vh] overflow-hidden">
            <img id="cartPreviewImage" src="" class="max-w-full max-h-[60vh] object-contain rounded-lg shadow-md border border-gray-200">
        </div>
        <div class="mt-4 flex justify-end">
            <a id="cartPreviewDownload" href="" target="_blank" class="px-4 py-2 bg-[#C87A53] hover:bg-[#A65F38] text-white text-xs font-bold rounded-lg transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Orijinal Görseli Aç
            </a>
        </div>
    </div>
</div>

<script>
function openCartPreviewModal(imgUrl, title) {
    document.getElementById('cartPreviewImage').src = imgUrl;
    document.getElementById('cartPreviewDownload').href = imgUrl;
    document.getElementById('cartPreviewTitle').innerText = title + ' — Fotoğraf Ön İzleme';
    const modal = document.getElementById('cartPreviewModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeCartPreviewModal() {
    const modal = document.getElementById('cartPreviewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
