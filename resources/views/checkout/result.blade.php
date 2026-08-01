@extends('layouts.app')

@section('title', 'Sipariş Sonucu - AhşapEvim')

@section('content')
<div class="bg-[#F7F5F0] pb-12 min-h-screen flex items-center">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12 text-center">
            @if(session('status') === 'success')
                <!-- Success State -->
                <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center text-green-500 text-4xl mx-auto mb-6 border border-green-100">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-4 font-serif">Siparişiniz Başarıyla Alındı!</h1>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Ödemeniz güvenle tamamlandı ve siparişiniz hazırlık sırasına alındı. Sipariş detaylarınız e-posta adresinize gönderilmiştir.
                </p>
                
                <div class="bg-gray-50 rounded-xl p-4 mb-8 inline-block border border-gray-150 text-left">
                    <div class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1 text-center">Sipariş Bilgileri</div>
                    <div class="text-sm text-gray-700"><strong>Sipariş Numarası:</strong> #{{ session('order_id') }}</div>
                    <div class="text-sm text-gray-700 mt-1"><strong>Gönderim Durumu:</strong> Hazırlanıyor</div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ url('/') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 px-8 rounded-lg transition text-base">
                        Ana Sayfaya Dön
                    </a>
                    <a href="{{ url('/urunler') }}" class="bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold py-3.5 px-8 rounded-lg transition text-base shadow-md">
                        Alışverişe Devam Et
                    </a>
                </div>
            @else
                <!-- Error State -->
                <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center text-red-500 text-4xl mx-auto mb-6 border border-red-100">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-4 font-serif">Ödeme Başarısız Oldu</h1>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Siparişinizin ödeme işlemi gerçekleştirilirken bir hata oluştu:<br>
                    <span class="text-red-600 font-semibold block mt-2 bg-red-50 py-2.5 px-4 rounded border border-red-100 inline-block">{{ session('message', 'Beklenmeyen bir hata oluştu.') }}</span>
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('cart.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 px-8 rounded-lg transition text-base">
                        Sepete Geri Dön
                    </a>
                    <a href="{{ route('checkout.index') }}" class="bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold py-3.5 px-8 rounded-lg transition text-base shadow-md">
                        Tekrar Dene
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
