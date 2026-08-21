@extends('layouts.app')

@section('title', 'Sipariş Takip - AhşapEvim')

@section('content')
<div class="bg-[#F7F5F0] pb-16 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Breadcrumb & Header -->
        <div class="max-w-4xl mx-auto mb-8 text-center">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 font-serif mb-3">Sipariş Takip</h1>
            <p class="text-sm text-gray-600">Üyeliksiz verdiğiniz sipariş durumunu takip etmek için takip kodunuzu ve iletişim bilginizi giriniz.</p>
        </div>

        @if(session('error'))
            <div class="max-w-xl mx-auto bg-red-100 border border-red-400 text-red-700 px-5 py-4 rounded-xl text-xs font-bold mb-8 text-center shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-base mr-1"></i> {{ session('error') }}
            </div>
        @endif

        @if(!isset($order))
            <!-- Search Form Card -->
            <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-md border border-amber-150 p-6 md:p-8 mb-8">
                <form action="{{ route('order.tracking.search') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label for="tracking_code" class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-barcode text-[#C87A53] mr-1"></i> Sipariş Takip Kodu *
                        </label>
                        <input type="text" id="tracking_code" name="tracking_code" value="{{ old('tracking_code') }}" placeholder="Örn: AHS-849201 veya Sipariş No (#12)" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-[#C87A53] focus:ring-0 outline-none text-sm font-mono uppercase transition">
                        <span class="text-[11px] text-gray-400 mt-1 block">Sipariş tamamlandığında ekranınızda gösterilen veya e-postanıza gönderilen koddur.</span>
                    </div>

                    <div>
                        <label for="phone_or_email" class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-phone text-[#C87A53] mr-1"></i> Telefon Numarası veya E-Posta *
                        </label>
                        <input type="text" id="phone_or_email" name="phone_or_email" value="{{ old('phone_or_email') }}" placeholder="Siparişte girdiğiniz e-posta veya telefon..." required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-[#C87A53] focus:ring-0 outline-none text-sm transition">
                    </div>

                    <button type="submit" class="w-full bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold py-3.5 px-6 rounded-xl transition shadow-md flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-magnifying-glass"></i> Siparişimi Sorgula
                    </button>
                </form>

                <!-- Login Option Banner -->
                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-xs text-gray-500 mb-3">Sitemizde hesabınız var mı?</p>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold px-5 py-2.5 rounded-xl text-xs transition border border-stone-200">
                        <i class="fa-solid fa-user-lock text-[#C87A53]"></i> Üye Girişi Yap ve Tüm Siparişlerini Gör
                    </a>
                </div>
            </div>
        @else
            <!-- Search Results View -->
            <div class="max-w-4xl mx-auto space-y-6">
                <!-- Search Again Bar -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('order.tracking') }}" class="text-xs font-bold text-gray-600 hover:text-brand transition flex items-center gap-1">
                        <i class="fa-solid fa-arrow-left"></i> Başka Sipariş Sorgula
                    </a>
                    <span class="text-xs font-bold bg-amber-100 text-amber-900 px-3 py-1 rounded-full">
                        Takip Kodu: <strong class="font-mono uppercase">{{ $order->tracking_code ?: 'AHS-'.$order->id }}</strong>
                    </span>
                </div>

                <!-- Status Card Timeline -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-amber-100">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-gray-100">
                        <div>
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Sipariş Numarası</span>
                            <h2 class="text-xl font-black text-gray-800">#{{ $order->id }} - {{ $order->tracking_code ?: 'AHS-'.$order->id }}</h2>
                            <span class="text-xs text-gray-500 mt-1 block">Tarih: {{ $order->created_at?->format('d.m.Y H:i') ?? '-' }}</span>
                        </div>

                        <div>
                            @if($order->status === 'paid' || $order->status === 'preparing')
                                <span class="px-4 py-2 bg-green-100 text-green-700 rounded-xl font-extrabold text-xs inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-circle-check text-sm"></i> Ödendi / Hazırlanıyor
                                </span>
                            @elseif($order->status === 'shipped')
                                <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-xl font-extrabold text-xs inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-truck text-sm"></i> Kargoya Verildi
                                </span>
                            @elseif(in_array($order->status, ['completed', 'delivered', 'teslim_edildi', 'teslimedildi']))
                                <span class="px-4 py-2 bg-purple-100 text-purple-700 rounded-xl font-extrabold text-xs inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-box-open text-sm"></i> Teslim Edildi
                                </span>
                            @elseif($order->status === 'pending')
                                <span class="px-4 py-2 bg-amber-100 text-amber-700 rounded-xl font-extrabold text-xs inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-clock text-sm"></i> İşleme Alındı / Bekliyor
                                </span>
                            @else
                                <span class="px-4 py-2 bg-red-100 text-red-700 rounded-xl font-extrabold text-xs inline-flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-circle-xmark text-sm"></i> İptal / Başarısız
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Steps -->
                    <div class="pt-6 grid grid-cols-3 gap-2 text-center relative">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm mb-2 shadow-sm {{ in_array($order->status, ['paid', 'preparing', 'shipped', 'completed', 'delivered', 'teslim_edildi', 'teslimedildi']) ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Sipariş Alındı</span>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm mb-2 shadow-sm {{ in_array($order->status, ['shipped', 'completed', 'delivered', 'teslim_edildi', 'teslimedildi']) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Kargoda</span>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm mb-2 shadow-sm {{ in_array($order->status, ['completed', 'delivered', 'teslim_edildi', 'teslimedildi']) ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                <i class="fa-solid fa-house-chimney"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-800">Teslim Edildi</span>
                        </div>
                    </div>
                </div>

                <!-- Delivery Details & Order Items -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Delivery Info -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-amber-100 md:col-span-1 space-y-3 text-xs text-gray-700">
                        <h3 class="text-sm font-extrabold text-gray-800 pb-2 border-b border-gray-100 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-[#C87A53]"></i> Teslimat Adresi
                        </h3>
                        <p><strong>Alıcı:</strong> {{ $order->name }}</p>
                        <p><strong>Telefon:</strong> {{ $order->phone }}</p>
                        <p class="whitespace-pre-line bg-gray-50 p-2.5 rounded-lg border border-gray-150">{{ $order->address }}</p>
                        <p><strong>Şehir:</strong> {{ $order->city ?: 'Manisa' }} / {{ $order->district ?: 'Merkez' }}</p>
                    </div>

                    <!-- Items List -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-amber-100 md:col-span-2">
                        <h3 class="text-sm font-extrabold text-gray-800 pb-2 border-b border-gray-100 mb-4 flex items-center justify-between">
                            <span><i class="fa-solid fa-boxes-stacked text-[#C87A53] mr-1"></i> Sipariş İçeriği</span>
                            <span class="text-xs font-bold text-[#C87A53]">Toplam: ₺{{ number_format($order->total_amount, 2, ',', '.') }}</span>
                        </h3>

                        <div class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ url($item->product->image) }}" class="w-12 h-12 object-cover rounded-lg border border-gray-200">
                                        @else
                                            <div class="w-12 h-12 bg-stone-100 rounded-lg flex items-center justify-center text-gray-400 font-bold">
                                                <i class="fa-solid fa-cube"></i>
                                            </div>
                                        @endif

                                        <div>
                                            <h4 class="text-xs font-bold text-gray-800">{{ $item->product ? $item->product->name : 'Ahşap Çerçeve' }}</h4>
                                            <div class="text-[11px] text-gray-500 mt-0.5">
                                                Adet: <strong>{{ $item->quantity }}</strong> × ₺{{ number_format($item->price, 2, ',', '.') }}
                                            </div>

                                            @php
                                                $fImg = $item->features['front_image'] ?? ($item->features['custom_image'] ?? null);
                                                $bImg = $item->features['back_image'] ?? null;
                                            @endphp
                                            @if($fImg || $bImg)
                                                <div class="mt-1 flex items-center gap-2">
                                                    @if($fImg)
                                                        <a href="{{ str_starts_with($fImg, 'http') ? $fImg : url($fImg) }}" download target="_blank" class="inline-flex items-center gap-1 bg-amber-50 text-amber-900 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded hover:bg-amber-100 transition">
                                                            <i class="fa-solid fa-image text-amber-600"></i> 1. Ön Yüz Fotoğrafı
                                                        </a>
                                                    @endif
                                                    @if($bImg)
                                                        <a href="{{ str_starts_with($bImg, 'http') ? $bImg : url($bImg) }}" download target="_blank" class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-900 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded hover:bg-emerald-100 transition">
                                                            <i class="fa-solid fa-image text-emerald-600"></i> 2. Arka Yüz Fotoğrafı
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-right font-black text-xs text-gray-800">
                                        ₺{{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
