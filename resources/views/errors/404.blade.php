@extends('layouts.app')

@section('title', '404 - Sayfa Bulunamadı')

@section('content')
<div class="container mx-auto px-4 py-16 flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="text-[#C87A53] text-9xl font-bold mb-4 drop-shadow-md">404</div>
    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-4 font-serif-artisan">Sayfa Bulunamadı</h1>
    <p class="text-gray-600 mb-8 max-w-md mx-auto text-lg">Aradığınız sayfa silinmiş, adı değiştirilmiş veya geçici olarak kullanım dışı olabilir.</p>
    <a href="{{ url('/') }}" class="bg-[#C87A53] hover:bg-[#A65F38] text-white px-8 py-3.5 rounded-xl font-bold transition shadow-lg inline-flex items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i> Ana Sayfaya Dön
    </a>
</div>
@endsection
