@extends('layouts.app')

@section('title', 'Giriş Yap — AhşapEvim')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-amber-100 p-8 relative overflow-hidden">
        
        {{-- Wood Accent Line --}}
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#C87A53]"></div>

        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-block mb-3">
                <img src="{{ url('/ahsaplogo_yataybg.png') }}" alt="AhşapEvim Logo" class="h-12 w-auto mx-auto object-contain">
            </a>
            <h2 class="text-2xl font-bold text-gray-900 font-serif">Hoş Geldiniz</h2>
            <p class="text-xs text-gray-500 mt-1">Favorilerinize erişmek ve siparişlerinizi takip etmek için giriş yapın.</p>
        </div>

        @if(session('info'))
            <div class="bg-blue-50 border border-blue-200 text-blue-700 text-xs px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-blue-500 text-sm"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs px-4 py-3 rounded-xl mb-6">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 1-Click Google Login Button --}}
        <form action="{{ route('auth.google') }}" method="POST" class="mb-6">
            @csrf
            <button type="submit" class="w-full py-3 px-4 bg-white border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50/30 text-gray-700 font-bold rounded-xl text-sm transition shadow-sm flex items-center justify-center gap-3 group">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Google ile Tek Tıkla Giriş Yap</span>
            </button>
        </form>

        <div class="relative flex items-center justify-center mb-6">
            <div class="border-t border-gray-200 w-full"></div>
            <span class="bg-white px-3 text-xs text-gray-400 font-semibold uppercase absolute">veya e-posta ile</span>
        </div>

        {{-- Email & Password Form --}}
        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">E-Posta Adresi *</label>
                <input type="email" name="email" required value="{{ old('email') }}" class="w-full text-sm border-gray-300 rounded-xl p-3 border focus:border-brand focus:ring-0 outline-none transition" placeholder="ornek@email.com">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Şifre *</label>
                <input type="password" name="password" required class="w-full text-sm border-gray-300 rounded-xl p-3 border focus:border-brand focus:ring-0 outline-none transition" placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-gray-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-[#C87A53] focus:ring-[#C87A53] w-4 h-4">
                    <span>Beni Hatırla</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl text-sm transition shadow-md flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i> Giriş Yap
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-gray-500">
            Hesabınız yok mu? 
            <a href="{{ route('register') }}" class="font-bold text-[#C87A53] hover:underline">Hemen Üye Olun</a>
        </div>
    </div>
</div>
@endsection