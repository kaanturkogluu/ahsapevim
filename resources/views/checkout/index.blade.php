@extends('layouts.app')

@section('title', 'Ödeme Bilgileri - AhşapEvim')

@section('content')
<div class="bg-[#F7F5F0] pb-12 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 font-serif">Ödeme ve Teslimat Bilgileri</h1>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Side: Delivery Details Form -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 font-serif border-b border-gray-100 pb-3">Teslimat & Fatura Adresi</h2>
                    
                    <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Ad Soyad</label>
                                <input type="text" id="name" name="name" value="{{ old('name', auth()->user() ? auth()->user()->name : '') }}" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#C87A53] focus:border-[#C87A53] transition">
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">E-Posta Adresi</label>
                                <input type="email" id="email" name="email" value="{{ old('email', auth()->user() ? auth()->user()->email : '') }}" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#C87A53] focus:border-[#C87A53] transition">
                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Telefon Numarası *</label>
                                <input type="text" id="phone" name="phone" placeholder="Örn: 05551234567" value="{{ old('phone') }}" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#C87A53] focus:border-[#C87A53] transition">
                                @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="identity_number" class="block text-sm font-semibold text-gray-700 mb-2">T.C. Kimlik No <span class="text-xs font-normal text-gray-400">(Fatura İçin - Opsiyonel)</span></label>
                                <input type="text" id="identity_number" name="identity_number" maxlength="11" placeholder="11 haneli T.C. Kimlik No" value="{{ old('identity_number') }}" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#C87A53] focus:border-[#C87A53] transition">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Teslimat Adresi</label>
                            <textarea id="address" name="address" rows="4" placeholder="Mahalle, sokak, daire no, ilçe ve il bilgilerini tam olarak giriniz." required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#C87A53] focus:border-[#C87A53] transition">{{ old('address') }}</textarea>
                            @error('address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="w-full md:w-auto bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold py-3.5 px-8 rounded-lg transition shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-credit-card"></i>
                            Güvenli Ödeme Adımına Geç
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Side: Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3 font-serif">Sipariş Özeti</h3>
                    
                    <div class="max-h-60 overflow-y-auto mb-6 divide-y divide-gray-100 pr-1">
                        @php $total = 0; @endphp
                        @foreach($cart as $item)
                            @php 
                                $total += $item['price'] * $item['quantity']; 
                                $customImgUrl = null;
                                if (!empty($item['custom_image'])) {
                                    $cImg = $item['custom_image'];
                                    $customImgUrl = (str_starts_with($cImg, '/') || str_starts_with($cImg, 'http')) 
                                        ? $cImg 
                                        : asset('storage/' . $cImg);
                                }
                            @endphp
                            <div class="py-3 flex items-center gap-4">
                                <div class="w-14 h-14 bg-stone-100 rounded-lg border border-gray-200 flex-shrink-0 p-1 relative flex items-center justify-center overflow-hidden">
                                    <img src="{{ $item['image'] ?: '/cerceve.png' }}" alt="{{ $item['name'] }}" class="max-w-full max-h-full object-contain mix-blend-multiply">
                                    @if($customImgUrl)
                                        <div class="absolute inset-0 flex items-center justify-center p-1.5 pointer-events-none">
                                            <img src="{{ $customImgUrl }}" class="w-[55%] h-[55%] object-cover rounded-xs border border-black/20 shadow-xs">
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-800 truncate">{{ $item['name'] }}</div>
                                    @if($customImgUrl)
                                        <div class="text-[11px] font-semibold text-amber-700 flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-camera"></i> Özel Fotoğraflı
                                        </div>
                                    @endif
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $item['quantity'] }} adet x {{ number_format($item['price'], 2, ',', '.') }} TL</div>
                                </div>
                                <div class="text-sm font-bold text-gray-800">
                                    {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }} TL
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="flex justify-between items-center mb-3 text-sm text-gray-600">
                        <span>Ürünler Toplamı</span>
                        <span class="font-bold">{{ number_format($total, 2, ',', '.') }} TL</span>
                    </div>
                    
                    <div class="flex justify-between items-center mb-4 text-sm text-gray-600">
                        <span>Kargo Ücreti</span>
                        <span class="text-green-600 font-bold">Ücretsiz</span>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold text-gray-800">Genel Toplam</span>
                            <span class="text-2xl font-extrabold text-[#C87A53]">{{ number_format($total, 2, ',', '.') }} TL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
