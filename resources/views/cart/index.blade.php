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
                            @php $total += $item['price'] * $item['quantity']; @endphp
                            <div class="flex flex-col sm:flex-row items-center gap-6 p-6 border-b border-gray-100 last:border-b-0">
                                
                                <!-- Product Image & Customization -->
                                <div class="w-32 h-32 flex-shrink-0 bg-gray-50 rounded-lg relative overflow-hidden border border-gray-100 p-2">
                                    <img src="{{ $item['image'] ?: '/cerceve.png' }}" alt="{{ $item['name'] }}" class="w-full h-full object-contain mix-blend-multiply">
                                    
                                    @if(isset($item['custom_image']) && $item['custom_image'])
                                        <div class="absolute inset-0 flex items-center justify-center p-2 z-10 pointer-events-none">
                                            <img src="{{ asset('storage/' . $item['custom_image']) }}" class="max-w-[80%] max-h-[80%] object-contain shadow-sm border border-white rounded">
                                        </div>
                                    @endif
                                </div>

                                <!-- Details -->
                                <div class="flex-1 text-center sm:text-left">
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">
                                        <a href="{{ url('/urun/' . $item['product_id']) }}" class="hover:text-brand transition">{{ $item['name'] }}</a>
                                    </h3>
                                    
                                    @if(isset($item['custom_image']) && $item['custom_image'])
                                        <div class="inline-block bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded mb-2">
                                            <i class="fa-solid fa-camera mr-1"></i> Kişiselleştirilmiş Fotoğraf
                                        </div>
                                    @endif
                                    
                                    <div class="text-brand font-extrabold text-xl mt-2">
                                        {{ number_format($item['price'], 2, ',', '.') }} TL
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-gray-600">Adet:</span>
                                    <span class="bg-gray-100 text-gray-800 font-bold px-4 py-2 rounded-lg">{{ $item['quantity'] }}</span>
                                </div>

                                <!-- Remove (Dummy) -->
                                <button class="text-gray-400 hover:text-red-500 transition p-2">
                                    <i class="fa-solid fa-trash-can text-xl"></i>
                                </button>
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
                                <span class="text-2xl font-extrabold text-brand">{{ number_format($total, 2, ',', '.') }} TL</span>
                            </div>
                        </div>

                        <button class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3.5 px-4 rounded-lg transition text-base shadow-md flex items-center justify-center gap-2">
                            Alışverişi Tamamla
                            <i class="fa-solid fa-chevron-right text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
