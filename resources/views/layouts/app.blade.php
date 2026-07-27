<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ahşap Evim Manisa')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '#f27a1a', // Trendyol orange
                            dark: '#e06912',
                            light: '#fdf1e8'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ede3d5;
            background-image: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 5px,
                rgba(100, 50, 0, 0.045) 5px,
                rgba(100, 50, 0, 0.045) 6px
            );
            min-height: 100vh;
        }
        /* Card framed look: white cards pop against wood grain background */
        .product-card {
            background: #ffffff;
            border: none;
            box-shadow: 0 2px 16px rgba(120,60,0,0.10);
        }
        .product-card:hover {
            box-shadow: 0 12px 36px rgba(120,60,0,0.16), 0 2px 8px rgba(242,122,26,0.08);
        }
        /* Topbar wood grain: same as body background */
        .topbar-wood {
            background-color: #ede3d5;
            background-image: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 5px,
                rgba(100, 50, 0, 0.045) 5px,
                rgba(100, 50, 0, 0.045) 6px
            );
            border-bottom: 1px solid rgba(160, 100, 40, 0.18);
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
                <a href="https://wa.me/905xxxxxxxxx" target="_blank" class="hover:text-brand transition flex items-center gap-1.5 text-green-700">
                    <i class="fa-brands fa-whatsapp"></i> WhatsApp Destek
                </a>
                <a href="#" class="hover:text-brand transition flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-question text-amber-700"></i> Sıkça Sorulanlar
                </a>
            </div>
            <!-- Right: Trust signals -->
            <div class="flex gap-5 text-amber-800">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-truck-fast text-amber-600"></i> Ücretsiz Kargo</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-rotate-left text-amber-600"></i> 14 Gün İade</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-hammer text-amber-600"></i> El İşçiliği Garantisi</span>
            </div>
        </div>
    </div>

    <!-- Main Header: Warm Cream -->
    <header class="bg-[#fffbf5] sticky top-0 z-40 shadow-sm border-b border-amber-100">
        <div class="container mx-auto px-4 py-4 flex flex-col md:flex-row items-center justify-between gap-5">

            <!-- Logo -->
            <a href="/" class="flex items-center gap-2.5 shrink-0">
                <div class="w-10 h-10 rounded-xl bg-brand flex items-center justify-center text-white font-black text-xl shadow-md shadow-brand/20">A</div>
                <div class="flex flex-col leading-none">
                    <span class="text-xl font-extrabold text-gray-900 tracking-tight">Ahşap<span class="text-brand">Evim</span></span>
                    <span class="text-[10px] font-semibold text-amber-700 uppercase tracking-wider mt-0.5">Manisa El Yapımı Atölye</span>
                </div>
            </a>

            <!-- Search Bar -->
            <div class="flex-1 w-full max-w-2xl">
                <div class="relative flex items-center w-full bg-amber-50 border-2 border-amber-100 focus-within:border-brand focus-within:bg-white rounded-xl transition-all">
                    <input type="text" placeholder="Ürün, kategori veya tasarım arayın…" class="w-full bg-transparent py-2.5 px-4 pr-12 outline-none text-sm text-gray-700 placeholder-amber-400">
                    <button class="absolute right-0 h-full px-4 bg-brand hover:bg-brand-dark rounded-r-xl text-white text-base flex items-center justify-center transition">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- User Actions -->
            <div class="flex items-center gap-5 text-[13px] font-semibold text-gray-700 shrink-0">
                <a href="#" class="hover:text-brand flex flex-col items-center gap-0.5 group transition">
                    <i class="fa-regular fa-user text-xl group-hover:text-brand"></i>
                    <span class="hidden md:inline text-[11px]">Giriş Yap</span>
                </a>
                <a href="#" class="hover:text-brand flex flex-col items-center gap-0.5 group transition">
                    <i class="fa-regular fa-heart text-xl group-hover:text-brand"></i>
                    <span class="hidden md:inline text-[11px]">Favorilerim</span>
                </a>
                <a href="/cart" class="hover:text-brand flex flex-col items-center gap-0.5 group transition relative">
                    <i class="fa-solid fa-shopping-bag text-xl group-hover:text-brand"></i>
                    <span class="hidden md:inline text-[11px]">Sepetim</span>
                    <span class="absolute -top-1.5 -right-2 bg-brand text-white text-[10px] font-bold rounded-full h-4.5 w-4.5 min-w-[18px] min-h-[18px] flex items-center justify-center">
                        {{ session('cart') ? count(session('cart')) : 0 }}
                    </span>
                </a>
            </div>
        </div>

        <!-- Categories Nav: Warm Underline Style -->
        <nav class="border-t border-amber-100 bg-[#fffbf5]">
            <div class="container mx-auto px-4 flex items-center justify-start gap-1 overflow-x-auto text-[12.5px] font-bold text-gray-600 whitespace-nowrap pb-0">
                @if(isset($navCategories) && $navCategories->count())
                    @foreach($navCategories as $cat)
                        <a href="/products?category={{ $cat->slug }}" class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition rounded-t-lg border-b-2 border-transparent hover:border-brand uppercase tracking-wide">{{ $cat->name }}</a>
                    @endforeach
                @else
                    <a href="/products?category=cerceve" class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition border-b-2 border-transparent hover:border-brand uppercase tracking-wide">Çerçeve</a>
                    <a href="/products?category=bebek-hediyelik" class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition border-b-2 border-transparent hover:border-brand uppercase tracking-wide">Bebek Hediyelik</a>
                    <a href="/products?category=masa-ve-gece-lambasi" class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition border-b-2 border-transparent hover:border-brand uppercase tracking-wide">Masa & Gece Lambası</a>
                @endif
                <div class="w-px h-5 bg-amber-200 mx-2"></div>
                <a href="/3d" class="text-brand hover:bg-brand/10 px-4 py-3 transition border-b-2 border-transparent hover:border-brand uppercase tracking-wide flex items-center gap-1.5">
                    <i class="fa-solid fa-cube"></i> 3D Stüdyo
                </a>
                <a href="/builder" class="text-amber-700 hover:bg-amber-50 px-4 py-3 transition border-b-2 border-transparent hover:border-amber-700 uppercase tracking-wide flex items-center gap-1.5">
                    <i class="fa-solid fa-hammer"></i> 3D Oluşturucu
                </a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow pt-6 pb-12">
        @if(session('success'))
            <div id="toast-success" class="fixed top-4 right-4 z-[9999] bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg transition-opacity duration-500 flex items-center justify-between min-w-[300px]">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="document.getElementById('toast-success').style.display='none'" class="text-green-700 hover:text-green-900 focus:outline-none">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('toast-success');
                    if(toast) {
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
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 pb-8 border-b border-gray-200 gap-6">
                <div class="flex items-center gap-4">
                    <span class="font-bold text-gray-800 text-lg">Bizi Takip Edin:</span>
                    <div class="flex gap-3 text-2xl text-gray-600">
                        <a href="https://instagram.com/ahsapevimmanisa" target="_blank" class="hover:text-brand transition"><i class="fa-brands fa-instagram"></i></a>
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
                        <li><a href="/iletisim" class="hover:text-brand transition">İletişim</a></li>
                        <li><a href="/sikca-sorulanlar" class="hover:text-brand transition">Sıkça Sorulanlar</a></li>
                    </ul>
                </div>
                
                <!-- Politikalar -->
                <div>
                    <h3 class="font-bold text-gray-800 text-base mb-4">Sözleşmeler ve Politikalar</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li><a href="/mesafeli-satis-sozlesmesi" class="hover:text-brand transition">Mesafeli Satış Sözleşmesi</a></li>
                        <li><a href="/gizlilik-politikasi" class="hover:text-brand transition">Gizlilik Politikası</a></li>
                        <li><a href="/teslimat-ve-iade" class="hover:text-brand transition">Teslimat ve İade Şartları</a></li>
                    </ul>
                </div>

                <!-- ETBİS -->
                <div>
                    <h3 class="font-bold text-gray-800 text-base mb-4">Güven Damgası</h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 inline-block text-center w-full">
                        <img src="https://www.eticaret.gov.tr/images/etbis-kare.png" alt="ETBİS" class="w-16 h-16 mx-auto mb-2 object-contain" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI0NSIgZmlsbD0iI2U1ZTVlNSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkeT0iLjNlbSIgZmlsbD0iIzY2NiIgZm9udC1zaXplPSIyMCIgdGV4dC1hbmNob3I9Im1pZGRsZSI+RVRCSVM8L3RleHQ+PC9zdmc+'">
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

</body>
</html>
