<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Ahşap Evim Manisa'); ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(url('/favicon.ico')); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(url('/favicon.ico')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(url('/ahsaplogo_yataybg.png')); ?>">
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
            background-image: url('<?php echo e(url('/light_wood_bg.jpg')); ?>');
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
    <?php echo $__env->yieldPushContent('head_scripts'); ?>
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
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-rotate-left text-amber-600"></i> 14 Gün
                    İade</span>
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-hammer text-amber-600"></i> El İşçiliği
                    Garantisi</span>
            </div>
        </div>
    </div>

    <!-- Main Header: Warm Cream -->
    <header class="bg-[#fffbf5] sticky top-0 z-40 shadow-sm border-b border-amber-100">
        <div class="container mx-auto px-4 py-4 flex flex-col md:flex-row items-center justify-between gap-5">

            <!-- Logo -->
            <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2.5 shrink-0">
                <img src="<?php echo e(url('/ahsaplogo_yataybg.png')); ?>" alt="AhşapEvim Logo"
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
                <?php if(auth()->guard()->check()): ?>
                    <!-- User Dropdown -->
                    <div class="relative group" id="userMenuDropdown">
                        <button type="button" onclick="toggleUserDropdown(event)" class="hover:text-brand flex flex-col items-center gap-0.5 transition outline-none">
                            <i class="fa-solid fa-user-check text-xl text-[#C87A53]"></i>
                            <span class="hidden md:inline text-[11px] truncate max-w-[90px]"><?php echo e(auth()->user()->name); ?></span>
                        </button>
                        
                        <!-- Dropdown Menu with pt-2 hover bridge -->
                        <div id="userDropdownContent" class="absolute right-0 top-full pt-1.5 w-48 hidden group-hover:block z-50">
                            <div class="bg-white rounded-xl shadow-2xl border border-amber-100 py-2">
                                <div class="px-4 py-2 border-b border-gray-100 font-bold text-gray-800 text-xs">
                                    <?php echo e(auth()->user()->name); ?>

                                    <span class="block font-normal text-[10px] text-gray-400 truncate"><?php echo e(auth()->user()->email); ?></span>
                                </div>
                                <a href="<?php echo e(route('favorites.index')); ?>" class="block px-4 py-2 hover:bg-amber-50 text-xs text-gray-700 font-semibold flex items-center gap-2">
                                    <i class="fa-solid fa-heart text-red-500"></i> Favorilerim
                                </a>
                                <form action="<?php echo e(route('logout')); ?>" method="POST" class="block">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-red-50 text-xs text-red-600 font-semibold flex items-center gap-2">
                                        <i class="fa-solid fa-sign-out-alt"></i> Çıkış Yap
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="hover:text-brand flex flex-col items-center gap-0.5 group transition">
                        <i class="fa-regular fa-user text-xl group-hover:text-brand"></i>
                        <span class="hidden md:inline text-[11px]">Giriş Yap</span>
                    </a>
                <?php endif; ?>

                <!-- Favorites Link -->
                <a href="<?php echo e(route('favorites.index')); ?>" class="hover:text-brand flex flex-col items-center gap-0.5 group transition relative">
                    <i class="fa-regular fa-heart text-xl group-hover:text-brand"></i>
                    <span class="hidden md:inline text-[11px]">Favorilerim</span>
                    <span id="favCounterBadge" class="absolute -top-1.5 -right-2 bg-[#C87A53] text-white text-[10px] font-bold rounded-full h-4.5 w-4.5 min-w-[18px] min-h-[18px] flex items-center justify-center">
                        <?php echo e(auth()->check() ? auth()->user()->favorites()->count() : 0); ?>

                    </span>
                </a>

                <!-- Cart Link -->
                <a href="<?php echo e(url('/sepet')); ?>" class="hover:text-brand flex flex-col items-center gap-0.5 group transition relative">
                    <i class="fa-solid fa-shopping-bag text-xl group-hover:text-brand"></i>
                    <span class="hidden md:inline text-[11px]">Sepetim</span>
                    <span class="absolute -top-1.5 -right-2 bg-brand text-white text-[10px] font-bold rounded-full h-4.5 w-4.5 min-w-[18px] min-h-[18px] flex items-center justify-center">
                        <?php echo e(session('cart') ? count(session('cart')) : 0); ?>

                    </span>
                </a>
            </div>
        </div>

        <!-- Categories Nav: Warm Underline Style -->
        <nav class="border-t border-amber-100 bg-[#fffbf5]">
            <div
                class="container mx-auto px-4 flex items-center justify-start gap-1 overflow-x-auto text-[12.5px] font-bold text-gray-600 whitespace-nowrap pb-0">
                <?php if(isset($navCategories) && $navCategories->count()): ?>
                    <?php $__currentLoopData = $navCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(url('/urunler')); ?>?category=<?php echo e($cat->slug); ?>"
                            class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition rounded-t-lg border-b-2 border-transparent hover:border-brand uppercase tracking-wide"><?php echo e($cat->name); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <a href="<?php echo e(url('/urunler')); ?>?category=cerceve"
                        class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition border-b-2 border-transparent hover:border-brand uppercase tracking-wide">Çerçeve</a>
                    <a href="<?php echo e(url('/urunler')); ?>?category=bebek-hediyelik"
                        class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition border-b-2 border-transparent hover:border-brand uppercase tracking-wide">Bebek
                        Hediyelik</a>
                    <a href="<?php echo e(url('/urunler')); ?>?category=masa-ve-gece-lambasi"
                        class="hover:text-brand hover:bg-amber-50 px-4 py-3 transition border-b-2 border-transparent hover:border-brand uppercase tracking-wide">Masa
                        & Gece Lambası</a>
                <?php endif; ?>

            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow pt-6 pb-12">
        <?php if(session('success')): ?>
            <div id="toast-success"
                class="fixed top-4 right-4 z-[9999] bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg transition-opacity duration-500 flex items-center justify-between min-w-[300px]">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-green-500 text-xl"></i>
                    <span class="font-medium"><?php echo e(session('success')); ?></span>
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
        <?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
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
                        <li><a href="<?php echo e(url('/iletisim')); ?>" class="hover:text-brand transition">İletişim</a></li>
                        <li><a href="<?php echo e(url('/sikca-sorulanlar')); ?>" class="hover:text-brand transition">Sıkça
                                Sorulanlar</a></li>
                    </ul>
                </div>

                <!-- Politikalar -->
                <div>
                    <h3 class="font-bold text-gray-800 text-base mb-4">Sözleşmeler ve Politikalar</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li><a href="<?php echo e(url('/mesafeli-satis-sozlesmesi')); ?>"
                                class="hover:text-brand transition">Mesafeli Satış Sözleşmesi</a></li>
                        <li><a href="<?php echo e(url('/gizlilik-politikasi')); ?>" class="hover:text-brand transition">Gizlilik
                                Politikası</a></li>
                        <li><a href="<?php echo e(url('/teslimat-ve-iade')); ?>" class="hover:text-brand transition">Teslimat ve
                                İade Şartları</a></li>
                    </ul>
                </div>

                <!-- ETBİS -->
                <div>
                    <h3 class="font-bold text-gray-800 text-base mb-4">Güven Damgası</h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 inline-block text-center w-full">
                        <img src="https://www.eticaret.gov.tr/images/etbis-kare.png" alt="ETBİS"
                            class="w-16 h-16 mx-auto mb-2 object-contain"
                            onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI0NSIgZmlsbD0iI2U1ZTVlNSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkeT0iLjNlbSIgZmlsbD0iIzY2NiIgZm9udC1zaXplPSIyMCIgdGV4dC1hbmNob3I9Im1pZGRsZSI+RVRCSVM8L3RleHQ+PC9zdmc+'">
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
                &copy; <?php echo e(date('Y')); ?> ahsapevimmanisa tüm hakları saklıdır
            </div>
        </div>
    </footer>

    <script>
    function toggleFavorite(productId, btnElement) {
        fetch('<?php echo e(route("favorites.toggle")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => {
            if (res.status === 401) {
                window.location.href = '<?php echo e(route("login")); ?>';
                return;
            }
            return res.json();
        })
        .then(data => {
            if (!data) return;

            // Update Header Counter
            const badge = document.getElementById('favCounterBadge');
            if (badge) badge.innerText = data.count;

            // Update heart icon styling & text on current button
            if (btnElement) {
                const heartIcon = btnElement.querySelector('i');
                const btnSpan = btnElement.querySelector('span');
                if (heartIcon) {
                    if (data.action === 'added') {
                        heartIcon.className = 'fa-solid fa-heart text-red-500 text-base drop-shadow-sm scale-110 transition-transform duration-200';
                        if (btnSpan) btnSpan.innerText = 'Favorilerinizde';
                    } else if (data.action === 'removed') {
                        heartIcon.className = 'fa-regular fa-heart text-gray-500 text-base hover:text-red-500 transition-colors duration-200';
                        if (btnSpan) btnSpan.innerText = 'Favorilere Ekle';

                        // If on favorites page grid, remove card live
                        const card = document.getElementById('favCard-' + productId);
                        if (card) card.remove();
                    }
                }
            }
        })
        .catch(err => console.error('Favori işlemi hatası:', err));
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
    </script>
</body>

</html><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/layouts/app.blade.php ENDPATH**/ ?>