<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahşap Evim Manisa - Yönetim</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ url('/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ url('/ahsaplogo_yataybg.png') }}">
    <!-- Tailwind CSS CDN for admin -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <div class="w-64 bg-[#29221C] text-white flex flex-col">
        <div class="p-4 bg-[#1D1713] text-center font-bold text-xl border-b border-[#3D332B]">
            Ahşap Evim Admin
        </div>
        <nav class="flex-1 overflow-y-auto mt-4">
            <a href="{{ url('/yonetim/siparisler') }}" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-shopping-cart mr-2"></i> Siparişler</a>
            <a href="{{ url('/yonetim/urunler') }}" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-box mr-2"></i> Ürünler</a>
            <a href="{{ url('/yonetim/kategoriler') }}" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-list mr-2"></i> Kategoriler</a>
            <a href="{{ url('/yonetim/3d-sablonlar') }}" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-cube mr-2"></i> 3D Şablonlar</a>
            <a href="{{ route('admin.shipping_companies.index') }}" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-truck-fast mr-2"></i> Kargo Şirketleri</a>
            <a href="{{ route('admin.email_templates.index') }}" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-envelope-open-text mr-2"></i> E-Posta Şablonları</a>
            <a href="{{ url('/yonetim/sayfalar') }}" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-file-lines mr-2"></i> Bilgilendirme</a>
            <a href="{{ url('/') }}" class="block p-4 hover:bg-[#3D332B] mt-4 border-t border-[#3D332B]"><i class="fa-solid fa-eye mr-2"></i> Siteyi Görüntüle</a>
            
            <form action="{{ route('admin.logout') }}" method="POST" class="block w-full border-t border-[#3D332B]">
                @csrf
                <button type="submit" class="w-full text-left p-4 hover:bg-red-950/40 text-red-350 hover:text-red-200 transition-colors flex items-center"><i class="fa-solid fa-sign-out-alt mr-2"></i> Güvenli Çıkış</button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <header class="bg-white p-4 shadow flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">@yield('header', 'Yönetim Paneli')</h2>
            <div>Admin</div>
        </header>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            @if(session('success'))
                <div id="adminSuccessToast" class="bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm mb-5 flex items-center justify-between text-xs font-bold transition-all duration-300">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                            <i class="fa-solid fa-check text-xs"></i>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                        <i class="fa-solid fa-times text-sm"></i>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div id="adminErrorToast" class="bg-gradient-to-r from-rose-50 to-red-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-sm mb-5 flex items-center justify-between text-xs font-bold transition-all duration-300">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                            <i class="fa-solid fa-exclamation text-xs"></i>
                        </div>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1">
                        <i class="fa-solid fa-times text-sm"></i>
                    </button>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-gradient-to-r from-rose-50 to-red-50 border border-rose-200 text-rose-800 p-4 rounded-xl shadow-sm mb-5 text-xs">
                    <div class="font-bold mb-1 flex items-center gap-1.5 text-rose-900">
                        <i class="fa-solid fa-circle-exclamation"></i> Lütfen aşağıdaki hataları düzeltin:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-700 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
