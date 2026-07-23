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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fafafa; }
        .product-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
    <!-- Three.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
</head>
<body class="text-gray-800 flex flex-col min-h-screen">

    <!-- Header Top -->
    <div class="bg-gray-100 text-[12px] text-gray-500 py-1.5 hidden md:block border-b border-gray-200">
        <div class="container mx-auto px-4 flex justify-between items-center font-medium">
            <!-- Left Side -->
            <div class="flex gap-4">
                <a href="tel:+90850xxxxxxx" class="hover:text-brand transition flex items-center gap-1.5"><i class="fa-solid fa-phone"></i> 0850 XXX XX XX</a>
                <a href="https://wa.me/905xxxxxxxxx" target="_blank" class="hover:text-green-600 transition flex items-center gap-1.5 text-green-700"><i class="fa-brands fa-whatsapp text-[14px]"></i> WhatsApp Destek</a>
                <a href="#" class="hover:text-gray-800 transition flex items-center gap-1.5"><i class="fa-solid fa-circle-question"></i> Sıkça Sorulanlar</a>
            </div>
            <!-- Right Side (Removed) -->
    </div>
    
    <header class="bg-white sticky top-0 z-40 shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-4 py-4 flex flex-col md:flex-row items-center justify-between gap-6">
            <!-- Logo -->
            <a href="/" class="text-3xl font-extrabold text-brand flex items-center gap-1 tracking-tight w-48 lowercase">
                ahşapevim
            </a>

            <!-- Search Bar -->
            <div class="flex-1 w-full max-w-3xl">
                <div class="relative flex items-center w-full bg-gray-50 border-2 border-transparent focus-within:border-brand rounded-md transition-colors">
                    <input type="text" placeholder="Aradığınız ürün, kategori veya markayı yazınız" class="w-full bg-transparent py-2.5 px-4 pr-10 outline-none text-sm text-gray-700">
                    <button class="absolute right-0 h-full px-4 text-brand text-lg flex items-center justify-center">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- User Actions -->
            <div class="flex items-center gap-6 text-[13px] font-semibold text-gray-700 shrink-0">
                <a href="#" class="hover:text-brand flex flex-row items-center gap-2 group transition">
                    <i class="fa-regular fa-user text-lg group-hover:text-brand"></i>
                    <span class="hidden md:inline">Giriş Yap</span>
                </a>
                <a href="#" class="hover:text-brand flex flex-row items-center gap-2 group transition">
                    <i class="fa-regular fa-heart text-lg group-hover:text-brand"></i>
                    <span class="hidden md:inline">Favorilerim</span>
                </a>
                <a href="/cart" class="hover:text-brand flex flex-row items-center gap-2 group transition relative">
                    <i class="fa-solid fa-shopping-cart text-lg group-hover:text-brand"></i>
                    <span class="hidden md:inline">Sepetim</span>
                    <span class="absolute -top-2 -right-2 bg-brand text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center">
                        {{ session('cart') ? count(session('cart')) : 0 }}
                    </span>
                </a>
            </div>
        </div>

        <!-- Categories Nav -->
        <nav>
            <div class="container mx-auto px-4 flex justify-center md:justify-start gap-8 overflow-x-auto text-[13px] font-bold text-gray-700 whitespace-nowrap">
                @if(isset($navCategories) && $navCategories->count())
                    @foreach($navCategories as $cat)
                        <a href="/products?category={{ $cat->slug }}" class="hover:text-brand hover:border-b-[3px] hover:border-brand py-3 transition border-b-[3px] border-transparent uppercase">{{ $cat->name }}</a>
                    @endforeach
                @else
                    <a href="/products?category=cerceve" class="hover:text-brand hover:border-b-[3px] hover:border-brand py-3 transition border-b-[3px] border-transparent uppercase">ÇERCEVE</a>
                    <a href="/products?category=bebek-hediyelik" class="hover:text-brand hover:border-b-[3px] hover:border-brand py-3 transition border-b-[3px] border-transparent uppercase">BEBEK HEDİYELİK</a>
                    <a href="/products?category=masa-ve-gece-lambasi" class="hover:text-brand hover:border-b-[3px] hover:border-brand py-3 transition border-b-[3px] border-transparent uppercase">MASA VE GECE LAMBASI</a>
                @endif
                <a href="/3d" class="text-brand hover:text-brand-dark hover:border-b-[3px] hover:border-brand-dark py-3 transition border-b-[3px] border-transparent uppercase flex items-center gap-1.5"><i class="fa-solid fa-cube"></i> 3D STÜDYO</a>
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
