<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ahşap Evim Manisa')</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ url('/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ url('/ahsaplogo_yataybg.png') }}">
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
                <a href="tel:+90850xxxxxxx" class="hover:text-brand transition flex items-center gap-1.5">
                    <i class="fa-solid fa-phone text-amber-700"></i> 0850 XXX XX XX
                </a>
                <a href="https://wa.me/905xxxxxxxxx" target="_blank"
                    class="hover:text-brand transition flex items-center gap-1.5 text-green-700">
                    <i class="fa-brands fa-whatsapp"></i> WhatsApp Destek
                </a>
                <a href="#" class="hover:text-brand transition flex items-center gap-1.5">
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
            <div class="flex-1 w-full max-w-2xl">
                <div
                    class="relative flex items-center w-full bg-amber-50 border-2 border-amber-100 focus-within:border-brand focus-within:bg-white rounded-xl transition-all">
                    <input type="text" placeholder="Ürün, kategori veya tasarım arayın…"
                        class="w-full bg-transparent py-2.5 px-4 pr-12 outline-none text-sm text-gray-700 placeholder-amber-400">
                    <button
                        class="absolute right-0 h-full px-4 bg-brand hover:bg-brand-dark rounded-r-xl text-white text-base flex items-center justify-center transition">
                        <i class="fa-solid fa-search"></i>
                    </button>
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
                    <!-- User Dropdown -->
                    <div class="relative group" id="userMenuDropdown">
                        <button type="button" onclick="toggleUserDropdown(event)" class="hover:text-brand flex flex-col items-center gap-0.5 transition outline-none">
                            <i class="fa-solid fa-user-check text-xl text-[#C87A53]"></i>
                            <span class="hidden md:inline text-[11px] truncate max-w-[90px]">{{ auth()->user()->name }}</span>
                        </button>
                        
                        <!-- Dropdown Menu with pt-2 hover bridge -->
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
        @if(session('success'))
            <div id="toast-success"
                class="fixed top-4 right-4 z-[9999] bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg transition-opacity duration-500 flex items-center justify-between min-w-[300px]">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="document.getElementById('toast-success').style.display='none'"
                    class="text-green-700 hover:text-green-900 focus:outline-none">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('toast-success');
                    if (toast) {
                        toast.style.opacity = '0';
                        setTimeout(() => toast.style.display = 'none', 500);
                    }
                }, 3000);
            </script>
        @endif
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto pt-10 pb-6 text-sm">
        <div class="container mx-auto px-4">
            <!-- Top Footer: Social & Support -->
            <div
                class="flex flex-col md:flex-row justify-between items-center mb-8 pb-8 border-b border-gray-200 gap-6">
                <div class="flex items-center gap-4">
                    <span class="font-bold text-gray-800 text-lg">Bizi Takip Edin:</span>
                    <div class="flex gap-3 text-2xl text-gray-600">
                        <a href="https://instagram.com/ahsapevimmanisa" target="_blank"
                            class="hover:text-brand transition"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="hover:text-brand transition"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="hover:text-brand transition"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-headset text-3xl text-brand"></i>
                    <div>
                        <div class="font-bold text-gray-800 text-lg">Hızlı Destek Hattı</div>
                        <div class="text-gray-600 font-semibold">0850 XXX XX XX</div>
                    </div>
                </div>
            </div>

            <!-- Middle Footer: Links & Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- Kurumsal -->
                <div>
                    <h3 class="font-bold text-gray-800 text-base mb-4">Kurumsal</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li><a href="{{ url('/iletisim') }}" class="hover:text-brand transition">İletişim</a></li>
                        <li><a href="{{ url('/sikca-sorulanlar') }}" class="hover:text-brand transition">Sıkça
                                Sorulanlar</a></li>
                    </ul>
                </div>

                <!-- Politikalar -->
                <div>
                    <h3 class="font-bold text-gray-800 text-base mb-4">Sözleşmeler ve Politikalar</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li><a href="{{ url('/mesafeli-satis-sozlesmesi') }}"
                                class="hover:text-brand transition">Mesafeli Satış Sözleşmesi</a></li>
                        <li><a href="{{ url('/gizlilik-politikasi') }}" class="hover:text-brand transition">Gizlilik
                                Politikası</a></li>
                        <li><a href="{{ url('/teslimat-ve-iade') }}" class="hover:text-brand transition">Teslimat ve
                                İade Şartları</a></li>
                    </ul>
                </div>

                <!-- ETBİS -->
                <div>
                    <h3 class="font-bold text-gray-800 text-base mb-4">Güven Damgası</h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 inline-block text-center w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-16 h-16 mx-auto mb-2 drop-shadow-sm">
                            <rect width="100" height="100" rx="16" fill="#E30613"/>
                            <path d="M50 14 L78 28 V48 C78 65 50 82 50 82 C50 82 22 65 22 48 V28 Z" fill="#FFFFFF" opacity="0.25"/>
                            <text x="50" y="48" fill="#FFFFFF" font-family="system-ui, sans-serif" font-weight="900" font-size="20" text-anchor="middle" letter-spacing="1">ETBİS</text>
                            <text x="50" y="66" fill="#FFFFFF" font-family="system-ui, sans-serif" font-weight="700" font-size="7.5" text-anchor="middle" opacity="0.95">KAYITLI SİTE</text>
                        </svg>
                        <span class="text-xs text-gray-700 font-bold block">ETBİS'e Kayıtlıdır</span>
                    </div>
                </div>

                <!-- İyzico & Secure Shopping -->
                <div>
                    <h3 class="font-bold text-gray-800 text-base mb-4">Güvenli Alışveriş</h3>
                    <div class="flex flex-col gap-4">
                        <div class="bg-gray-50 border border-gray-200 rounded p-2 flex items-center justify-center">
                            <!-- iyzico logo -->
                            <img src="https://iyzi.co/g/logo/iyzico-logo.svg" alt="iyzico" class="h-6">
                        </div>
                        <div class="flex gap-2">
                            <i class="fa-brands fa-cc-visa text-4xl text-blue-800"></i>
                            <i class="fa-brands fa-cc-mastercard text-4xl text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer: Copyright -->
            <div class="pt-6 border-t border-gray-200 text-center text-xs text-gray-500 font-semibold">
                &copy; {{ date('Y') }} ahsapevimmanisa tüm hakları saklıdır
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
                <a href="{{ route('cart.index') }}" class="py-3 px-4 bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold rounded-xl transition text-center text-xs flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-cart-shopping"></i> Sepete Git
                </a>
                <a href="{{ route('checkout.index') }}" class="py-3 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl transition text-center text-xs shadow-md flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-lock"></i> Ödemeye Geç
                </a>
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
        toast.className = `pointer-events-auto flex items-center gap-3 p-4 rounded-2xl shadow-xl text-xs font-bold transition-all duration-300 transform translate-x-10 opacity-0 border ${
            type === 'success' ? 'bg-emerald-800 text-white border-emerald-600' :
            type === 'error' ? 'bg-red-800 text-white border-red-600' :
            'bg-amber-800 text-white border-amber-600'
        }`;

        const iconClass = type === 'success' ? 'fa-circle-check text-emerald-300' :
                          type === 'error' ? 'fa-triangle-exclamation text-red-300' :
                          'fa-circle-info text-amber-300';

        toast.innerHTML = `
            <i class="fa-solid ${iconClass} text-lg shrink-0"></i>
            <div class="flex-1 leading-snug">${message}</div>
            <button onclick="this.parentElement.remove()" class="text-white/70 hover:text-white text-sm focus:outline-none ml-2">
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

    // Auto trigger Toast on session flash messages
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('success') }}', 'success');
        });
    @endif
    @if(session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('error') }}', 'error');
        });
    @endif
    </script>
</body>

</html>