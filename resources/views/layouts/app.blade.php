<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ahşap Evim Manisa — Kişiye Özel El Yapımı Masif Ahşap Çerçeveler')</title>
    <meta name="description" content="@yield('meta_description', 'Manisa\'da masif ahşap el işçiliği ile kişiye özel fotoğraflı dönen ahşap çerçeveler ve hediyelik dekoratif tasarımlar üretiyoruz. Aynı gün kargo imkanıyla hemen sipariş verin.')">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Sosyal Medya -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Ahşap Evim Manisa — Kişiye Özel Masif Ahşap Çerçeveler')">
    <meta property="og:description" content="@yield('meta_description', 'Manisa\'da masif ahşap el işçiliği ile kişiye özel fotoğraflı dönen ahşap çerçeveler ve hediyelik dekoratif tasarımlar üretiyoruz.')">
    <meta property="og:image" content="@yield('meta_image', 'https://ahsapevimmanisa.com/ahsaplogo_org.png')">

    <!-- Google Arama & Tarayıcı Faviconları (Google Standart 48x48, 96x96, 192x192) -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="48x48" href="/favicon-48x48.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/favicon-192x192.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

    <!-- Google Arama Logosu için Schema.org Yapılandırılmış Veri -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Organization",
      "name": "Ahşap Evim",
      "url": "https://ahsapevimmanisa.com",
      "logo": "https://ahsapevimmanisa.com/favicon-192x192.png",
      "sameAs": [
        "https://www.instagram.com/ahsapevimmanisa"
      ]
    }
    </script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '#C87A53', // Premium terracotta/artisan copper
                            dark: '#A65F38',
                            light: '#FAF3EE'
                        },
                        wood: {
                            dark: '#2E251E',    // Walnut/charcoal text
                            medium: '#8C6239',  // Oak/teak accent
                            light: '#E6DFD5',   // Ash/pine border
                            cream: '#F7F5F0'    // Soft organic linen background
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif']
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F7F5F0;
            /* Soft organic linen background fallback */
            background-image: url('{{ url('/light_wood_bg.jpg') }}');
            background-repeat: repeat;
            background-size: 320px 320px;
            min-height: 100vh;
            color: #2E251E;
        }

        h1,
        h2,
        h3,
        .font-serif-artisan {
            font-family: 'Playfair Display', serif;
        }

        /* Premium Soft shadow for wood cards */
        .product-card {
            background: #ffffff;
            border: 1px solid #EFEAE0;
            box-shadow: 0 4px 20px rgba(74, 58, 44, 0.03);
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 36px rgba(74, 58, 44, 0.06);
            border-color: #D6CEBE;
        }

        /* Premium Topbar light wood details */
        .topbar-wood {
            background-color: #FAF9F6;
            border-bottom: 1px solid #EFEAE0;
        }

        /* Scrollbar styles for a refined look */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #F7F5F0;
        }

        ::-webkit-scrollbar-thumb {
            background: #D9D2C6;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #C87A53;
        }
    </style>
    <!-- Page-specific head assets (e.g. Three.js only on home) -->
    @stack('head_scripts')
</head>

<body class="text-gray-800 flex flex-col min-h-screen">

    <!-- Header Top Bar: Wood Grain Texture -->
    <div class="topbar-wood text-[12px] text-amber-900 py-1.5 hidden md:block">
        <div class="container mx-auto px-4 flex justify-between items-center font-medium">
            <!-- Left: Contact -->
            <div class="flex gap-5">
                @if(!empty($contactData['phone']))
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactData['phone']) }}" class="hover:text-brand transition flex items-center gap-1.5">
                        <i class="fa-solid fa-phone text-amber-700"></i> {{ $contactData['phone'] }}
                    </a>
                @endif
                @if(!empty($contactData['whatsapp']))
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactData['whatsapp']) }}" target="_blank"
                        class="hover:text-brand transition flex items-center gap-1.5 text-green-700">
                        <i class="fa-brands fa-whatsapp text-green-600"></i> WhatsApp Destek
                    </a>
                @endif
                <a href="{{ url('/sikca-sorulanlar') }}" class="hover:text-brand transition flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-question text-amber-700"></i> Sıkça Sorulanlar
                </a>
            </div>
            <!-- Right: Trust signals -->
            <div class="flex gap-5 text-amber-800">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-truck-fast text-amber-600"></i> Ücretsiz
                    Kargo</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-hammer text-amber-600"></i> El İşçiliği
                    Garantisi</span>
            </div>
        </div>
    </div>

    <!-- Main Header: Warm Cream -->
    <header class="bg-[#fffbf5] sticky top-0 z-40 shadow-sm border-b border-amber-100">
        <div class="container mx-auto px-4 py-4 flex flex-col md:flex-row items-center justify-between gap-5">

            <!-- Logo -->
            <a href="{{ url('/urunler') }}" class="flex items-center gap-2.5 shrink-0">
                <img src="{{ url('/ahsaplogo_yataybg.png') }}" alt="AhşapEvim Logo"
                    class="h-12 md:h-16 w-auto object-contain">
            </a>

            <!-- Search Bar -->
            <div class="flex-1 w-full max-w-2xl relative">
                <form action="{{ url('/urunler') }}" method="GET" id="headerSearchForm" class="relative flex items-center w-full bg-amber-50 border-2 border-amber-100 focus-within:border-brand focus-within:bg-white rounded-xl transition-all">
                    <input type="text" name="q" id="headerSearchInput" value="{{ request('q') ?: request('search') }}" autocomplete="off" oninput="handleLiveSearch(this.value)" placeholder="Ürün, kategori veya tasarım arayın…"
                        class="w-full bg-transparent py-2.5 px-4 pr-12 outline-none text-sm text-gray-700 placeholder-amber-400">
                    <button type="submit"
                        class="absolute right-0 h-full px-4 bg-brand hover:bg-brand-dark rounded-r-xl text-white text-base flex items-center justify-center transition">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </form>

                <!-- Live Search Dropdown -->
                <div id="liveSearchDropdown" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-amber-200/80 z-[100] hidden overflow-hidden backdrop-blur-md">
                    <div id="liveSearchResults" class="max-h-96 overflow-y-auto divide-y divide-gray-100 p-2">
                        <!-- Dynamic search suggestions -->
                    </div>
                </div>
            </div>

            <!-- User Actions -->
            <div class="flex items-center gap-5 text-[13px] font-semibold text-gray-700 shrink-0">
                <!-- Sipariş Takip Link -->
                @auth
                    <a href="{{ route('profile.index', ['tab' => 'siparisler']) }}" class="hover:text-brand flex flex-col items-center gap-0.5 group transition">
                        <i class="fa-solid fa-truck-fast text-xl group-hover:text-brand text-[#C87A53]"></i>
                        <span class="hidden md:inline text-[11px]">Sipariş Takip</span>
                    </a>
                @else
                    <a href="{{ route('order.tracking') }}" class="hover:text-brand flex flex-col items-center gap-0.5 group transition">
                        <i class="fa-solid fa-truck-fast text-xl group-hover:text-brand"></i>
                        <span class="hidden md:inline text-[11px]">Sipariş Takip</span>
                    </a>
                @endauth
                @auth
                    @if(auth()->user()->is_admin)
                        <!-- Admin Direct Link (No Dropdown) -->
                        <a href="{{ route('admin.revenue.index') }}" class="hover:text-brand flex flex-col items-center gap-0.5 group transition text-[#C87A53] font-bold">
                            <i class="fa-solid fa-user-gear text-xl text-[#C87A53] group-hover:scale-110 transition-transform"></i>
                            <span class="hidden md:inline text-[11px] font-extrabold">Yönet</span>
                        </a>
                    @else
                        <!-- Customer Dropdown Menu -->
                        <div class="relative group" id="userMenuDropdown">
                            <button type="button" onclick="toggleUserDropdown(event)" class="hover:text-brand flex flex-col items-center gap-0.5 transition outline-none">
                                <i class="fa-solid fa-user-check text-xl text-[#C87A53]"></i>
                                <span class="hidden md:inline text-[11px] truncate max-w-[90px]">{{ auth()->user()->name }}</span>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="userDropdownContent" class="absolute right-0 top-full pt-1.5 w-48 hidden group-hover:block z-50">
                                <div class="bg-white rounded-xl shadow-2xl border border-amber-100 py-2">
                                    <div class="px-4 py-2 border-b border-gray-100 font-bold text-gray-800 text-xs mb-1">
                                        {{ auth()->user()->name }}
                                        <span class="block font-normal text-[10px] text-gray-400 truncate">{{ auth()->user()->email }}</span>
                                    </div>
                                    <a href="{{ route('profile.index', ['tab' => 'siparisler']) }}" class="block px-4 py-2 hover:bg-amber-50 text-xs text-gray-700 font-semibold flex items-center gap-2">
                                        <i class="fa-solid fa-box text-[#C87A53]"></i> Siparişler
                                    </a>
                                    <a href="{{ route('favorites.index') }}" class="block px-4 py-2 hover:bg-amber-50 text-xs text-gray-700 font-semibold flex items-center gap-2">
                                        <i class="fa-solid fa-heart text-red-500"></i> Favorilerim
                                    </a>
                                    <a href="{{ route('profile.index', ['tab' => 'bilgiler']) }}" class="block px-4 py-2 hover:bg-amber-50 text-xs text-gray-700 font-semibold flex items-center gap-2">
                                        <i class="fa-solid fa-user text-[#C87A53]"></i> Kullanıcı Bilgileri
                                    </a>
                                    <a href="{{ route('profile.index', ['tab' => 'adres']) }}" class="block px-4 py-2 hover:bg-amber-50 text-xs text-gray-700 font-semibold flex items-center gap-2">
                                        <i class="fa-solid fa-map-location-dot text-[#C87A53]"></i> Adres Bilgilerim
                                    </a>
                                    <a href="{{ route('profile.index', ['tab' => 'sifre']) }}" class="block px-4 py-2 hover:bg-amber-50 text-xs text-gray-700 font-semibold flex items-center gap-2">
                                        <i class="fa-solid fa-shield-halved text-[#C87A53]"></i> Şifre Güvenlik
                                    </a>
                                    <form action="{{ route('logout') }}" method="POST" class="block border-t border-gray-100 mt-1 pt-1">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-50 text-xs text-red-600 font-semibold flex items-center gap-2">
                                            <i class="fa-solid fa-sign-out-alt"></i> Çıkış Yap
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="hover:text-brand flex flex-col items-center gap-0.5 group transition">
                        <i class="fa-regular fa-user text-xl group-hover:text-brand"></i>
                        <span class="hidden md:inline text-[11px]">Giriş Yap</span>
                    </a>
                @endauth

                <!-- Favorites Link -->
                <a href="{{ route('favorites.index') }}" class="hover:text-brand flex flex-col items-center gap-0.5 group transition relative">
                    <i class="fa-regular fa-heart text-xl group-hover:text-brand"></i>
                    <span class="hidden md:inline text-[11px]">Favorilerim</span>
                    <span id="favCounterBadge" class="absolute -top-1.5 -right-2 bg-[#C87A53] text-white text-[10px] font-bold rounded-full h-4.5 w-4.5 min-w-[18px] min-h-[18px] flex items-center justify-center">
                        {{ auth()->check() ? auth()->user()->favorites()->count() : 0 }}
                    </span>
                </a>

                <!-- Cart Link -->
                <a href="{{ url('/sepet') }}" onclick="event.preventDefault(); openCartDrawer();" class="hover:text-brand flex flex-col items-center gap-0.5 group transition relative cursor-pointer">
                    <i class="fa-solid fa-shopping-bag text-xl group-hover:text-brand"></i>
                    <span class="hidden md:inline text-[11px]">Sepetim</span>
                    <span id="headerCartCountBadge" class="absolute -top-1.5 -right-2 bg-brand text-white text-[10px] font-bold rounded-full h-4.5 w-4.5 min-w-[18px] min-h-[18px] flex items-center justify-center">
                        {{ session('cart') ? count(session('cart')) : 0 }}
                    </span>
                </a>
            </div>
        </div>

        <!-- Categories Nav: Warm Underline Style -->
        <nav class="border-t border-amber-100 bg-[#fffbf5]">
            <div
                class="container mx-auto px-4 flex items-center justify-start gap-1 overflow-x-auto text-[12.5px] font-bold text-gray-600 whitespace-nowrap pb-0">
                @if(isset($navCategories) && $navCategories->count())
                    @foreach($navCategories as $cat)
                        <a href="{{ url('/urunler') }}?category={{ $cat->slug }}"
                            class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition rounded-t-lg border-b-2 border-transparent hover:border-brand uppercase tracking-wide">{{ $cat->name }}</a>
                    @endforeach
                @else
                    <a href="{{ url('/urunler') }}?category=cerceve"
                        class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition border-b-2 border-transparent hover:border-brand uppercase tracking-wide">Çerçeve</a>
                    <a href="{{ url('/urunler') }}?category=bebek-hediyelik"
                        class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition border-b-2 border-transparent hover:border-brand uppercase tracking-wide">Bebek
                        Hediyelik</a>
                    <a href="{{ url('/urunler') }}?category=masa-ve-gece-lambasi"
                        class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition border-b-2 border-transparent hover:border-brand uppercase tracking-wide">Masa
                        & Gece Lambası</a>
                @endif

            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow pt-6 pb-12">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[#FAF8F5] border-t border-[#EFEAE0] mt-auto relative text-sm text-[#2E251E] overflow-hidden">
        <!-- Top Warm Accent Bar -->
        <div class="h-1 bg-gradient-to-r from-amber-800 via-[#C87A53] to-amber-700 w-full"></div>

        <!-- Value Proposition Highlights Banner -->
        <div class="border-b border-[#EFEAE0] bg-white/70 py-6">
            <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-6 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200/80 text-[#C87A53] flex items-center justify-center text-lg shrink-0 shadow-2xs">
                        <i class="fa-solid fa-tree"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider">%100 Masif Ahşap</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Doğal el işçiliği & zararsız cila</p>
                    </div>
                </div>

                <div class="flex items-center justify-center md:justify-start gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200/80 text-[#C87A53] flex items-center justify-center text-lg shrink-0 shadow-2xs">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider">Hızlı & Korumalı Kargo</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Tüm Türkiye'ye özenli paketleme</p>
                    </div>
                </div>

                <div class="flex items-center justify-center md:justify-start gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200/80 text-[#C87A53] flex items-center justify-center text-lg shrink-0 shadow-2xs">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider">Güvenli Alışveriş</h4>
                        <p class="text-xs text-gray-500 mt-0.5">iyzico altyapısı & 3D Secure koruması</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 pt-10 pb-8">
            <!-- Main Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-10">
                <!-- Col 1: Brand Info -->
                <div class="space-y-4">
                    <a href="{{ url('/') }}" class="inline-block">
                        <span class="font-serif text-xl font-bold text-gray-800 tracking-tight">AhşapEvim <span class="text-[#C87A53]">Manisa</span></span>
                    </a>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Manisa'daki atölyemizde masif ahşap el işçiliği ve kişiye özel tasarımlarla yaşam alanlarınıza doğallık ve sıcaklık katıyoruz.
                    </p>
                    
                    <!-- Contact Info Snippets -->
                    <div class="space-y-2 pt-1 text-xs text-gray-600">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-location-dot text-[#C87A53] w-4 text-center"></i>
                            <span>{{ Str::limit(str_replace("\n", ", ", $contactData['address'] ?? 'Manisa Atölye, Türkiye'), 45) }}</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-envelope text-[#C87A53] w-4 text-center"></i>
                            <span>{{ $contactData['email'] ?? 'info@ahsapevimmanisa.com' }}</span>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div class="flex items-center gap-2 pt-2">
                        <a href="https://instagram.com/ahsapevimmanisa" target="_blank" class="w-8 h-8 rounded-xl bg-white border border-gray-200 hover:border-[#C87A53] hover:bg-[#C87A53] hover:text-white text-gray-600 flex items-center justify-center transition shadow-2xs">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-xl bg-white border border-gray-200 hover:border-[#C87A53] hover:bg-[#C87A53] hover:text-white text-gray-600 flex items-center justify-center transition shadow-2xs">
                            <i class="fa-brands fa-facebook-f text-xs"></i>
                        </a>
                        @if(!empty($contactData['whatsapp']))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactData['whatsapp']) }}" target="_blank" title="WhatsApp Destek" class="w-8 h-8 rounded-xl bg-white border border-gray-200 hover:border-emerald-600 hover:bg-emerald-600 hover:text-white text-gray-600 flex items-center justify-center transition shadow-2xs">
                            <i class="fa-brands fa-whatsapp text-xs"></i>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Col 2: Kurumsal & Keşfet -->
                <div>
                    <h3 class="font-serif font-bold text-gray-800 text-base mb-4 relative pb-2 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-8 after:h-0.5 after:bg-[#C87A53]">
                        Kurumsal & Keşfet
                    </h3>
                    <ul class="text-xs text-gray-600 space-y-2.5">
                        <li><a href="{{ url('/urunler') }}" class="hover:text-[#C87A53] hover:translate-x-1 inline-block transition transform">Tüm Ürünlerimiz</a></li>
                        <li><a href="{{ url('/iletisim') }}" class="hover:text-[#C87A53] hover:translate-x-1 inline-block transition transform">İletişim & Konum</a></li>
                        <li><a href="{{ url('/sikca-sorulanlar') }}" class="hover:text-[#C87A53] hover:translate-x-1 inline-block transition transform">Sıkça Sorulan Sorular</a></li>
                        <li><a href="{{ route('order.tracking') }}" class="hover:text-[#C87A53] hover:translate-x-1 inline-block transition transform">Sipariş Takip Ekranı</a></li>
                    </ul>
                </div>

                <!-- Col 3: Sözleşmeler ve Politikalar -->
                <div>
                    <h3 class="font-serif font-bold text-gray-800 text-base mb-4 relative pb-2 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-8 after:h-0.5 after:bg-[#C87A53]">
                        Sözleşmeler & Bilgilendirme
                    </h3>
                    <ul class="text-xs text-gray-600 space-y-2.5">
                        @php
                            $footerPages = \App\Models\Page::where('is_active', true)->whereNotIn('slug', ['iletisim', 'sikca-sorulanlar'])->get();
                        @endphp
                        @forelse($footerPages as $fPage)
                            <li>
                                <a href="{{ url('/' . $fPage->slug) }}" class="hover:text-[#C87A53] hover:translate-x-1 inline-block transition transform">
                                    {{ $fPage->title }}
                                </a>
                            </li>
                        @empty
                            <li><a href="{{ url('/mesafeli-satis-sozlesmesi') }}" class="hover:text-[#C87A53] hover:translate-x-1 inline-block transition transform">Mesafeli Satış Sözleşmesi</a></li>
                            <li><a href="{{ url('/gizlilik-politikasi') }}" class="hover:text-[#C87A53] hover:translate-x-1 inline-block transition transform">Gizlilik & Güvenlik Politikası</a></li>
                            <li><a href="{{ url('/teslimat-ve-iade') }}" class="hover:text-[#C87A53] hover:translate-x-1 inline-block transition transform">Teslimat ve İade Şartları</a></li>
                        @endforelse
                    </ul>
                </div>

                <!-- Col 4: Güvenli Ödeme & ETBİS -->
                <div class="space-y-4">
                    <h3 class="font-serif font-bold text-gray-800 text-base mb-4 relative pb-2 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-8 after:h-0.5 after:bg-[#C87A53]">
                        Güvenli Alışveriş Altyapısı
                    </h3>
                    
                    <!-- Fixed Iyzico Logo & Badge -->
                    <div class="bg-white border border-gray-200/90 rounded-2xl p-3.5 shadow-2xs space-y-2 text-center">
                        <div class="flex items-center justify-center py-1">
                            <img src="{{ url('/images/iyzico-logo.svg') }}" alt="iyzico ile Öde" class="h-7 w-auto mx-auto object-contain">
                        </div>
                        <span class="text-[10.5px] text-gray-500 font-medium block">256-Bit SSL Koruması ile 3D Ödeme</span>
                    </div>

                    <!-- Payment Cards Badges -->
                    <div class="flex items-center justify-center gap-2 pt-1">
                        <span class="px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-blue-900 text-lg font-black shadow-2xs flex items-center justify-center">
                            <i class="fa-brands fa-cc-visa"></i>
                        </span>
                        <span class="px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-red-600 text-lg font-black shadow-2xs flex items-center justify-center">
                            <i class="fa-brands fa-cc-mastercard"></i>
                        </span>
                        <span class="px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-[11px] font-black text-[#005B9C] shadow-2xs tracking-tighter uppercase">
                            TROY
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer Bar -->
            <div class="pt-6 border-t border-[#EFEAE0] flex flex-col md:flex-row justify-between items-center gap-3 text-xs text-gray-500">
                <div>
                    &copy; {{ date('Y') }} <strong>AhşapEvim Manisa</strong>. Tüm hakları saklıdır.
                </div>
                <div class="flex items-center gap-1.5 text-gray-500 font-medium">
                    <span>Manisa atölyemizde sevgiyle üretilmiştir</span>
                    <i class="fa-solid fa-heart text-[#C87A53] text-[10px]"></i>
                </div>
            </div>
        </div>
    </footer>

    <!-- Offcanvas Cart Drawer Overlay -->
    <div id="cartDrawerOverlay" onclick="closeCartDrawer()" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9998] hidden opacity-0 transition-opacity duration-300 pointer-events-auto"></div>

    <!-- Offcanvas Cart Drawer Slide Panel -->
    <div id="cartDrawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-out flex flex-col pointer-events-auto">
        <!-- Drawer Header -->
        <div class="p-4 border-b border-amber-100 flex items-center justify-between bg-[#FFFBF5]">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-orange-100 text-[#C87A53] flex items-center justify-center text-base font-bold">
                    <i class="fa-solid fa-shopping-bag"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-gray-800 text-base font-serif">Alışveriş Sepetim</h3>
                    <span id="drawerCartCountText" class="text-[11px] text-gray-500 block">0 ürün bulunuyor</span>
                </div>
            </div>
            <button onclick="closeCartDrawer()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition focus:outline-none">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Drawer Items Content Body -->
        <div id="cartDrawerContent" class="flex-1 overflow-y-auto p-4 space-y-3">
            <!-- Dynamic Cart Items / Empty State Rendered Here -->
        </div>

        <!-- Drawer Footer -->
        <div id="cartDrawerFooter" class="p-4 border-t border-amber-100 bg-[#FFFBF5] space-y-3 hidden">
            <div class="flex justify-between items-center text-sm font-semibold text-gray-700">
                <span>Ürünler Toplamı:</span>
                <span id="cartDrawerSubtotal" class="text-xl font-black text-[#C87A53]">0,00 TL</span>
            </div>
            <div class="text-[11.5px] text-emerald-700 font-bold bg-emerald-50 border border-emerald-200/80 p-2.5 rounded-xl flex items-center justify-center gap-2">
                <i class="fa-solid fa-truck-fast text-emerald-600"></i> Tüm Siparişlerinizde Kargo Ücretsiz!
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <a href="{{ url('/urunler') }}" onclick="closeCartDrawer()" class="py-3 px-4 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold rounded-xl transition text-center text-xs flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-store"></i> Ürünleri Keşfet
                </a>
                @auth
                    <a href="{{ route('checkout.index') }}" class="py-3 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl transition text-center text-xs shadow-md flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-lock"></i> Ödemeye Geç
                    </a>
                @else
                    <button type="button" onclick="openGuestCheckoutModal()" class="py-3 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl transition text-center text-xs shadow-md flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-lock"></i> Ödemeye Geç
                    </button>
                @endauth
            </div>
        </div>
    </div>

    <!-- Toast UI Container -->
    <div id="toastContainer" class="fixed top-5 right-5 z-[10000] flex flex-col gap-2.5 max-w-sm pointer-events-none"></div>

    <script>
    // Global Toast Notification System
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-center gap-3.5 p-4 rounded-2xl shadow-2xl text-xs font-bold transition-all duration-300 transform translate-x-10 opacity-0 border backdrop-blur-md min-w-[300px] max-w-md ${
            type === 'success' ? 'bg-[#29221C]/95 text-white border-[#C87A53]/50' :
            type === 'error' ? 'bg-rose-950/95 text-white border-rose-600/50' :
            'bg-[#29221C]/95 text-white border-amber-500/50'
        }`;

        const iconBg = type === 'success' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' :
                       type === 'error' ? 'bg-rose-500/20 text-rose-400 border-rose-500/30' :
                       'bg-amber-500/20 text-amber-400 border-amber-500/30';

        const iconClass = type === 'success' ? 'fa-circle-check' :
                          type === 'error' ? 'fa-triangle-exclamation' :
                          'fa-circle-info';

        toast.innerHTML = `
            <div class="w-8 h-8 rounded-xl ${iconBg} border flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid ${iconClass}"></i>
            </div>
            <div class="flex-1 leading-snug text-gray-100">${message}</div>
            <button onclick="this.parentElement.remove()" class="w-6 h-6 rounded-full hover:bg-white/10 text-gray-400 hover:text-white flex items-center justify-center text-xs transition shrink-0 ml-1">
                <i class="fa-solid fa-times"></i>
            </button>
        `;

        container.appendChild(toast);

        // Animate In
        setTimeout(() => {
            toast.classList.remove('translate-x-10', 'opacity-0');
        }, 10);

        // Auto Dismiss after 3.5s
        setTimeout(() => {
            toast.classList.add('translate-x-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    // Offcanvas Cart Drawer Handlers
    function openCartDrawer() {
        const overlay = document.getElementById('cartDrawerOverlay');
        const drawer = document.getElementById('cartDrawer');
        if (overlay && drawer) {
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            drawer.classList.remove('translate-x-full');
            refreshCartDrawer();
        }
    }

    function closeCartDrawer() {
        const overlay = document.getElementById('cartDrawerOverlay');
        const drawer = document.getElementById('cartDrawer');
        if (overlay && drawer) {
            drawer.classList.add('translate-x-full');
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }

    function refreshCartDrawer() {
        const content = document.getElementById('cartDrawerContent');
        const footer = document.getElementById('cartDrawerFooter');
        const subtotalEl = document.getElementById('cartDrawerSubtotal');
        const badge = document.getElementById('headerCartCountBadge');
        const drawerCountText = document.getElementById('drawerCartCountText');

        if (!content) return;

        content.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                <i class="fa-solid fa-spinner fa-spin text-3xl mb-3 text-[#C87A53]"></i>
                <span class="text-xs font-semibold">Sepet yükleniyor...</span>
            </div>
        `;

        fetch('{{ route("cart.data") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') return;

            // Update badge counters
            if (badge) badge.innerText = data.count;
            if (drawerCountText) drawerCountText.innerText = `${data.count} ürün bulunuyor`;

            const cartKeys = Object.keys(data.cart);

            if (cartKeys.length === 0) {
                footer.classList.add('hidden');
                content.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full py-16 text-center px-4">
                        <div class="w-20 h-20 bg-amber-50 text-[#C87A53] rounded-full flex items-center justify-center text-4xl mb-4 border border-amber-200/60 shadow-inner">
                            <i class="fa-solid fa-basket-shopping"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800 mb-1 font-serif">Sepetiniz Henüz Boş</h4>
                        <p class="text-xs text-gray-500 mb-8 max-w-xs leading-relaxed">Harika masif ahşap çerçeve koleksiyonlarımızı keşfetmek ve fotoğraflarınızı ölümsüzleştirmek için hemen alışverişe başlayın.</p>
                        
                        <div class="mt-auto w-full pt-6 border-t border-gray-100">
                            <a href="{{ url('/urunler') }}" onclick="closeCartDrawer()" class="w-full bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold py-3.5 px-6 rounded-xl transition text-center shadow-lg flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                                <i class="fa-solid fa-store text-sm"></i> Alışverişe Başla
                            </a>
                        </div>
                    </div>
                `;
                return;
            }

            footer.classList.remove('hidden');
            if (subtotalEl) subtotalEl.innerText = data.total;

            let html = '';
            cartKeys.forEach(key => {
                const item = data.cart[key];
                const itemTotal = (item.price * item.quantity).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' TL';
                const itemPriceFormatted = (item.price * 1).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' TL';

                html += `
                    <div class="p-3 bg-white rounded-2xl border border-gray-150 shadow-sm flex items-center gap-3 relative group">
                        <div class="w-16 h-16 bg-stone-50 rounded-xl border border-gray-200 overflow-hidden flex-shrink-0 flex items-center justify-center p-1">
                            <img src="${item.image}" alt="${item.name}" class="max-w-full max-h-full object-contain">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="text-xs font-bold text-gray-800 truncate">${item.name}</h5>
                            ${item.custom_image ? '<span class="text-[10px] font-semibold text-amber-700 block mt-0.5"><i class="fa-solid fa-camera"></i> Özel Tasarımlı</span>' : ''}
                            ${(item.is_gift || item.gift_note) ? `<span class="text-[10px] font-extrabold text-amber-900 bg-amber-100 border border-amber-200 px-2 py-0.5 rounded-md inline-block mt-1"><i class="fa-solid fa-gift text-brand"></i> Hediye Notu: ${item.gift_note || 'Hediye Paketi'}</span>` : ''}
                            <div class="text-[11px] text-gray-500 mt-1 font-semibold">${itemPriceFormatted}</div>
                            
                            <div class="flex items-center gap-2 mt-2">
                                <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                                    <button onclick="updateCartDrawerQuantity('${key}', ${item.quantity - 1})" class="px-2 py-0.5 text-xs text-gray-600 hover:bg-gray-200 transition font-bold" ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                                    <span class="px-2 text-xs font-extrabold text-gray-800">${item.quantity}</span>
                                    <button onclick="updateCartDrawerQuantity('${key}', ${item.quantity + 1})" class="px-2 py-0.5 text-xs text-gray-600 hover:bg-gray-200 transition font-bold">+</button>
                                </div>
                                <span class="text-xs font-extrabold text-[#C87A53] ml-auto">${itemTotal}</span>
                            </div>
                        </div>
                        <button onclick="removeFromCartDrawer('${key}')" title="Ürünü Sil" class="text-gray-300 hover:text-red-500 transition text-sm p-1.5 focus:outline-none">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                `;
            });

            content.innerHTML = html;
        })
        .catch(err => {
            console.error('Sepet drawer verisi alınamadı:', err);
            content.innerHTML = `<div class="text-xs text-red-500 text-center py-6">Sepet verisi yüklenirken bir hata oluştu.</div>`;
        });
    }

    function updateCartDrawerQuantity(key, newQty) {
        if (newQty < 1) return;

        fetch('{{ route("cart.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ key: key, quantity: newQty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                refreshCartDrawer();
            } else {
                showToast(data.message || 'Sepet güncellenemedi.', 'error');
            }
        })
        .catch(err => showToast('Bir hata oluştu.', 'error'));
    }

    function removeFromCartDrawer(key) {
        fetch('{{ route("cart.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ key: key })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('Ürün sepetten çıkarıldı.', 'info');
                refreshCartDrawer();
            } else {
                showToast(data.message || 'Silinemedi.', 'error');
            }
        })
        .catch(err => showToast('Bir hata oluştu.', 'error'));
    }

    // Anti-Spam Protected Favorite Toggle
    let isFavoriteProcessing = false;
    function toggleFavorite(productId, btnElement) {
        if (isFavoriteProcessing) return;
        isFavoriteProcessing = true;

        let originalContent = '';
        if (btnElement) {
            originalContent = btnElement.innerHTML;
            btnElement.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-amber-600"></i> İşleniyor...`;
            btnElement.style.pointerEvents = 'none';
        }

        fetch('{{ route("favorites.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => {
            if (res.status === 401) {
                window.location.href = '{{ route("login") }}';
                return;
            }
            return res.json();
        })
        .then(data => {
            if (!data) return;

            const badge = document.getElementById('favCounterBadge');
            if (badge) badge.innerText = data.count;

            if (btnElement) {
                btnElement.innerHTML = originalContent;
                btnElement.style.pointerEvents = 'auto';

                const heartIcon = btnElement.querySelector('i');
                const btnSpan = btnElement.querySelector('span');
                if (heartIcon) {
                    if (data.action === 'added') {
                        heartIcon.className = 'fa-solid fa-heart text-red-500 text-base drop-shadow-sm scale-110 transition-transform duration-200';
                        if (btnSpan) btnSpan.innerText = 'Favorilerinizde';
                        showToast('Ürün favorilerinize eklendi!', 'success');
                    } else if (data.action === 'removed') {
                        heartIcon.className = 'fa-regular fa-heart text-gray-500 text-base hover:text-red-500 transition-colors duration-200';
                        if (btnSpan) btnSpan.innerText = 'Favorilere Ekle';
                        showToast('Ürün favorilerinizden çıkarıldı.', 'info');

                        const card = document.getElementById('favCard-' + productId);
                        if (card) card.remove();
                    }
                }
            }
        })
        .catch(err => showToast('Favori işlemi sırasında hata oluştu.', 'error'))
        .finally(() => {
            isFavoriteProcessing = false;
        });
    }

    function toggleUserDropdown(event) {
        event.stopPropagation();
        const content = document.getElementById('userDropdownContent');
        if (content) {
            content.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('userMenuDropdown');
        const content = document.getElementById('userDropdownContent');
        if (menu && content && !menu.contains(e.target)) {
            content.classList.add('hidden');
        }
    });

    // Fixed Scroll To Top Button Trigger
    window.addEventListener('scroll', function() {
        const btn = document.getElementById('scrollToTopBtn');
        if (btn) {
            if (window.scrollY > 250) {
                btn.classList.remove('opacity-0', 'pointer-events-none');
                btn.classList.add('opacity-100', 'pointer-events-auto');
            } else {
                btn.classList.remove('opacity-100', 'pointer-events-auto');
                btn.classList.add('opacity-0', 'pointer-events-none');
            }
        }
    });

    // Auto trigger Toast on session flash messages & auto open cart drawer if open_cart=1 parameter exists
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('open_cart') === '1') {
            setTimeout(openCartDrawer, 300);
        }

        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
    });
    </script>

    <!-- Fixed Floating WhatsApp Button (Sol Alt) -->
    @if(!empty($contactData['whatsapp']))
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactData['whatsapp']) }}" target="_blank" title="WhatsApp İletişim Hattı" class="fixed bottom-5 left-5 z-[9999] w-12 h-12 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full flex items-center justify-center text-2xl shadow-xl hover:scale-110 transition-transform duration-200 border-2 border-white group">
        <i class="fa-brands fa-whatsapp text-2xl"></i>
        <span class="absolute left-14 bg-emerald-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-md pointer-events-none">
            WhatsApp Destek Hattı
        </span>
    </a>
    @endif

    <!-- Fixed Scroll To Top Button (Sağ Alt) -->
    <button type="button" id="scrollToTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="Yukarı Çık" class="fixed bottom-5 right-5 z-[9999] w-11 h-11 bg-[#C87A53] hover:bg-[#A65F38] text-white rounded-full flex items-center justify-center text-lg shadow-xl hover:scale-110 transition-all duration-200 border-2 border-white opacity-0 pointer-events-none">
        <i class="fa-solid fa-chevron-up"></i>
    </button>

    <!-- Guest Checkout Option Modal -->
    <div id="guestCheckoutModal" class="fixed inset-0 z-[100000] bg-black/80 hidden items-center justify-center p-4 backdrop-blur-sm" onclick="closeGuestCheckoutModal()">
        <div class="bg-white rounded-2xl p-6 md:p-8 max-w-md w-full relative shadow-2xl border border-amber-100 text-center" onclick="event.stopPropagation()">
            <button type="button" onclick="closeGuestCheckoutModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold leading-none">&times;</button>
            
            <div class="w-14 h-14 bg-amber-100 text-[#C87A53] rounded-full flex items-center justify-center text-2xl mx-auto mb-4 border border-amber-200">
                <i class="fa-solid fa-user-lock"></i>
            </div>

            <h3 class="text-xl font-bold text-gray-800 font-serif mb-2">Siparişe Nasıl Devam Etmek İstersiniz?</h3>
            <p class="text-xs text-gray-500 mb-6 leading-relaxed">Üye girişi yaparak tüm siparişlerinizi kolayca takip edebilir veya üye olmadan hızlıca üyeliksiz sipariş verebilirsiniz.</p>

            <div class="space-y-3">
                <!-- Option 1: Log in & Continue -->
                <a href="{{ route('login') }}" class="w-full bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold py-3.5 px-4 rounded-xl transition text-xs shadow-md flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Üye Girişi Yap ve Devam Et
                </a>

                <!-- Option 2: Guest Continue -->
                <a href="{{ route('checkout.index') }}" class="w-full bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold py-3.5 px-4 rounded-xl transition text-xs border border-stone-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-bolt"></i> Üyeliksiz Devam Et
                </a>
            </div>
        </div>
    </div>

    <script>
    function openGuestCheckoutModal() {
        const modal = document.getElementById('guestCheckoutModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeGuestCheckoutModal() {
        const modal = document.getElementById('guestCheckoutModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // Live Auto-complete Search Script
    let liveSearchTimer = null;
    function handleLiveSearch(query) {
        clearTimeout(liveSearchTimer);
        const dropdown = document.getElementById('liveSearchDropdown');
        const resultsContainer = document.getElementById('liveSearchResults');
        if (!dropdown || !resultsContainer) return;

        query = query.trim();
        if (query.length < 2) {
            dropdown.classList.add('hidden');
            resultsContainer.innerHTML = '';
            return;
        }

        resultsContainer.innerHTML = `
            <div class="p-4 text-center text-xs text-gray-400 font-bold flex items-center justify-center gap-2">
                <i class="fa-solid fa-spinner fa-spin text-[#C87A53] text-sm"></i> Aranıyor...
            </div>
        `;
        dropdown.classList.remove('hidden');

        liveSearchTimer = setTimeout(() => {
            fetch('{{ route("search.live") }}?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success' && data.products.length > 0) {
                        let html = '';
                        data.products.forEach(p => {
                            html += `
                                <a href="${p.url}" class="flex items-center gap-3 p-2 hover:bg-amber-50/80 rounded-xl transition group">
                                    <img src="${p.image}" class="w-11 h-11 object-cover rounded-lg border border-gray-200 shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-bold text-gray-800 truncate group-hover:text-[#C87A53] transition">${p.name}</div>
                                        <div class="text-[10px] text-gray-500 font-semibold">${p.category_name}</div>
                                    </div>
                                    <div class="text-xs font-black text-[#C87A53] shrink-0">${p.price}</div>
                                </a>
                            `;
                        });

                        html += `
                            <div class="pt-2 pb-1 text-center border-t border-gray-100 mt-1">
                                <button type="submit" form="headerSearchForm" class="w-full text-xs font-bold text-[#C87A53] hover:text-[#A65F38] py-1.5 transition">
                                    Tüm Sonuçları Gör ("${query}") <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
                                </button>
                            </div>
                        `;
                        resultsContainer.innerHTML = html;
                    } else {
                        resultsContainer.innerHTML = `
                            <div class="p-4 text-center text-xs text-gray-500 font-semibold">
                                <i class="fa-solid fa-search-minus text-amber-500 text-lg mb-1 block"></i>
                                "${query}" ile eşleşen ürün bulunamadı.
                            </div>
                        `;
                    }
                })
                .catch(err => {
                    dropdown.classList.add('hidden');
                });
        }, 250);
    }

    document.addEventListener('click', function(e) {
        const searchForm = document.getElementById('headerSearchForm');
        const dropdown = document.getElementById('liveSearchDropdown');
        if (searchForm && dropdown && !searchForm.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    </script>
</body>

</html>