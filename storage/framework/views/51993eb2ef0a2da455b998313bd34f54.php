<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AhşapEvim - Masif Ahşap Özel Tasarım Çerçeveler & Dekorasyaon</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(url('/favicon.ico')); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(url('/favicon.ico')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(url('/ahsaplogo_yataybg.png')); ?>">

    
    <script>
        (function () {
            if (window.innerWidth < 1024) {
                window.location.replace('<?php echo e(url('/urunler')); ?>');
            }
        })();
    </script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            DEFAULT: '#C87A53',
                            dark: '#A65F38',
                            light: '#FBF5F1'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #FAFAF8;
            overflow: hidden;
        }

        #scroll-container::-webkit-scrollbar {
            width: 5px;
        }

        #scroll-container::-webkit-scrollbar-thumb {
            background-color: rgba(200, 122, 83, 0.4);
            border-radius: 4px;
        }

        .step-dot {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .banner-fade {
            transition: opacity 0.5s ease-in-out, transform 0.5s ease-out;
        }

        .thumb-active {
            border-color: #C87A53 !important;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(200, 122, 83, 0.25);
        }
    </style>
</head>

<body class="text-gray-800 selection:bg-[#C87A53] selection:text-white">

    <!-- Header Logo & Keşfet Button -->
    <div class="fixed top-6 left-6 lg:top-8 lg:left-14 z-50 pointer-events-auto flex items-center gap-4 lg:gap-6">
        <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2.5">
            <img src="<?php echo e(url('/ahsaplogo_yataybg.png')); ?>" alt="AhşapEvim Logo" class="h-12 lg:h-16 w-auto object-contain drop-shadow-sm">
        </a>

        <a href="<?php echo e(url('/urunler')); ?>" class="inline-flex items-center gap-3 py-3.5 px-7 bg-gradient-to-r from-[#C87A53] via-[#D87843] to-[#F27A1A] hover:from-[#B56740] hover:to-[#E06912] text-white font-black text-sm lg:text-base rounded-2xl shadow-xl shadow-orange-500/25 hover:shadow-2xl hover:shadow-orange-500/40 border border-white/20 backdrop-blur-md transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95 group">
            <i class="fa-solid fa-store text-lg lg:text-xl group-hover:scale-110 transition-transform duration-300"></i>
            <span class="tracking-wide">Ürünleri Keşfet</span>
            <i class="fa-solid fa-arrow-right text-xs lg:text-sm opacity-90 group-hover:translate-x-1 transition-transform duration-300"></i>
        </a>
    </div>

    <!-- Fixed Vertical Step Indicators -->
    <div class="fixed right-4 top-1/2 -translate-y-1/2 z-50 flex flex-col items-center gap-3.5 bg-white/95 backdrop-blur-md py-6 px-3 rounded-full shadow-2xl border border-gray-200/80">
        <button onclick="scrollToStep(1)" id="dot-1" class="step-dot w-4 h-4 rounded-full bg-[#C87A53] scale-125 shadow-sm" title="1. Adım: Masif Ahşap Gövde"></button>
        <button onclick="scrollToStep(2)" id="dot-2" class="step-dot w-3.5 h-3.5 rounded-full bg-gray-300 hover:bg-gray-400" title="2. Adım: Özel Üretim & Detaylar"></button>
        <button onclick="scrollToStep(3)" id="dot-3" class="step-dot w-3.5 h-3.5 rounded-full bg-gray-300 hover:bg-gray-400" title="3. Adım: Şık Tasarım & Sipariş"></button>
    </div>

    <!-- MAIN SNAP CONTAINER -->
    <div id="scroll-container" class="h-screen w-full overflow-y-auto snap-y snap-mandatory scroll-smooth relative">

        <!-- LEFT COLUMN: Content & Steps -->
        <div class="w-full lg:w-1/2 z-10 px-6 lg:pl-16 lg:pr-10">

            <!-- STEP 1 -->
            <section id="step-1" class="step-section h-screen snap-start snap-always flex flex-col justify-center py-12" data-step="1">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-[#C87A53]/10 text-[#C87A53] text-xs font-bold rounded-full mb-5 w-max shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-[#C87A53] animate-pulse"></span> 1. ADIM — DOĞAL MASİF AHŞAP
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-5 font-serif">
                    Ustalıkla İşlenen <br><span class="text-[#C87A53] underline decoration-[#C87A53]/30 underline-offset-8 font-serif">Doğal Ahşabın Zamansız Zarafeti</span>
                </h1>
                <p class="text-base lg:text-lg text-gray-600 leading-relaxed mb-6 max-w-xl">
                    Anılarınızı sıradan çerçevelere değil, özenle tasarlanmış masif ahşap çerçevelere taşıyın. Doğal dokusu, modern tasarımı ve Manisa atölyemizdeki kaliteli işçiliğiyle yaşam alanlarınıza sıcaklık ve karakter katın.
                </p>

                <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm max-w-xl">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Koleksiyon Öne Çıkan Özellikler</label>
                        <span class="text-xs font-bold text-[#C87A53]">%100 El İşçiliği</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="p-3 rounded-xl bg-stone-50 border border-stone-200/60">
                            <i class="fa-solid fa-tree text-lg text-[#C87A53] mb-1"></i>
                            <div class="text-xs font-bold text-gray-800">Masif Ağaç</div>
                            <div class="text-[10px] text-gray-500">Birinci Sınıf Ahşap</div>
                        </div>
                        <div class="p-3 rounded-xl bg-stone-50 border border-stone-200/60">
                            <i class="fa-solid fa-arrows-rotate text-lg text-emerald-600 mb-1"></i>
                            <div class="text-xs font-bold text-gray-800">360° Dönen</div>
                            <div class="text-[10px] text-gray-500">Çift Taraflı Kullanım</div>
                        </div>
                        <div class="p-3 rounded-xl bg-stone-50 border border-stone-200/60">
                            <i class="fa-solid fa-[#C87A53] fa-gift text-lg text-amber-600 mb-1"></i>
                            <div class="text-xs font-bold text-gray-800">Özel Hediye</div>
                            <div class="text-[10px] text-gray-500">Kişiye Özel Not</div>
                        </div>
                    </div>
                </div>

                
                <div class="mt-4 p-3.5 bg-amber-50 border border-amber-200/90 rounded-2xl max-w-xl text-amber-950 text-xs flex items-start gap-3 shadow-xs">
                    <i class="fa-solid fa-circle-info text-[#C87A53] text-lg shrink-0 mt-0.5"></i>
                    <div class="leading-relaxed">
                        <strong class="font-black text-amber-900 block mb-0.5">📌 Önemli Bilgilendirme: Ürün Görselleri Temsilidir</strong>
                        Sitedeki örnek fotoğraflar temsilidir. Sipariş edeceğiniz masif çerçevede ürün görselindeki fotoğraf değil, <strong>sipariş verirken yüklediğiniz kendi fotoğrafınız</strong> basılarak hazırlanmaktadır.
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <span>2. Adım: Koleksiyon Görsellerini İnceleyin</span>
                    <i class="fa-solid fa-arrow-down animate-bounce text-[#C87A53]"></i>
                </div>
            </section>

            <!-- STEP 2 -->
            <section id="step-2" class="step-section h-screen snap-start snap-always flex flex-col justify-center py-12" data-step="2">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-[#C87A53]/10 text-[#C87A53] text-xs font-bold rounded-full mb-5 w-max shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-[#C87A53]"></span> 2. ADIM — KİŞİSELLEŞTİRME & DETAYLAR
                </div>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-5 font-serif">
                    En Güzel Anılarınız, <br><span class="text-[#C87A53] underline decoration-[#C87A53]/30 underline-offset-8 font-serif">Size Özel Bir Tasarımda</span> Hayat Bulsun
                </h2>
                <p class="text-base lg:text-lg text-gray-600 leading-relaxed mb-6 max-w-xl">
                    İster sevdiklerinize unutulmaz bir hediye hazırlayın, ister evinizin en özel köşesini süsleyin. Yüksek kaliteli malzeme, titiz zımparalama ve koruyucu doğal yağ uygulaması ile yıllarca ilk günkü şıklığını korur.
                </p>

                <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm max-w-xl">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Vitrin Görsel Galerisi</label>
                        <span class="text-[11px] font-semibold text-[#C87A53] bg-[#C87A53]/10 px-2.5 py-0.5 rounded-md">Atölyemizden Kareler</span>
                    </div>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Sağ taraftaki galeriden tüm modellerimizi ve atölyemizde üretilen özel tasarımları detaylı olarak inceleyebilirsiniz.
                    </p>
                </div>

                <div class="mt-8 flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <span>3. Adım: Tüm Ürünleri Keşfedin</span>
                    <i class="fa-solid fa-arrow-down animate-bounce text-[#C87A53]"></i>
                </div>
            </section>

            <!-- STEP 3 -->
            <section id="step-3" class="step-section h-screen snap-start snap-always flex flex-col justify-center py-12" data-step="3">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-[#C87A53]/10 text-[#C87A53] text-xs font-bold rounded-full mb-5 w-max shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-[#C87A53]"></span> 3. ADIM — SİPARİŞ & HIZLI TESLİMAT
                </div>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-5 font-serif">
                    İki Farklı Anı, <br><span class="text-[#C87A53] underline decoration-[#C87A53]/30 underline-offset-8 font-serif">Tek Bir Çerçevede Dönen Şıklık</span>
                </h2>
                <p class="text-base lg:text-lg text-gray-600 leading-relaxed mb-6 max-w-xl">
                    En değerli fotoğraflarınızı tek bir masif çerçevede buluşturun. Saat 16:00'a kadar verilen siparişleriniz aynı gün kargoya teslim edilmektedir.
                </p>

                <div class="max-w-xl">
                    <a href="<?php echo e(url('/urunler')); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 py-4 px-8 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold text-base rounded-2xl shadow-xl shadow-[#C87A53]/25 transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-shopping-bag text-lg"></i>
                        <span>Mağazadaki Tüm Ürünleri Keşfet</span>
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </a>
                </div>
            </section>

        </div>
    </div>

    <!-- RIGHT COLUMN: FIXED SHOWCASE BANNER GALLERY (a1.jpeg ... a6.jpeg & Admin Managed) -->
    <div class="fixed right-0 top-0 w-1/2 h-screen hidden lg:flex items-center justify-center p-8 lg:p-12 z-20 pointer-events-auto">
        <div class="relative w-full h-[85vh] max-h-[750px] bg-white rounded-3xl border border-gray-200/90 shadow-2xl overflow-hidden flex flex-col justify-between p-4 group">
            
            
            <div class="relative flex-1 w-full rounded-2xl overflow-hidden bg-stone-100 flex items-center justify-center">
                <?php if(isset($homeBanners) && $homeBanners->count() > 0): ?>
                    <?php $__currentLoopData = $homeBanners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <img id="banner-img-<?php echo e($index); ?>" 
                             src="<?php echo e($banner->image_url); ?>" 
                             alt="<?php echo e($banner->title ?: 'AhşapEvim Görsel'); ?>" 
                             class="banner-slide absolute inset-0 w-full h-full object-cover transition-all duration-700 opacity-0 scale-105 <?php echo e($index === 0 ? 'opacity-100 scale-100 z-10' : 'z-0'); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    
                    <?php for($i = 1; $i <= 6; $i++): ?>
                        <img id="banner-img-<?php echo e($i-1); ?>" 
                             src="<?php echo e(url('/images/a' . $i . '.jpeg')); ?>" 
                             alt="AhşapEvim Görsel <?php echo e($i); ?>" 
                             class="banner-slide absolute inset-0 w-full h-full object-cover transition-all duration-700 opacity-0 scale-105 <?php echo e($i === 1 ? 'opacity-100 scale-100 z-10' : 'z-0'); ?>">
                    <?php endfor; ?>
                <?php endif; ?>

                
                <button onclick="prevBanner()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-black/40 hover:bg-black/70 text-white rounded-full flex items-center justify-center backdrop-blur-xs transition shadow-lg opacity-80 hover:opacity-100">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </button>
                <button onclick="nextBanner()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-black/40 hover:bg-black/70 text-white rounded-full flex items-center justify-center backdrop-blur-xs transition shadow-lg opacity-80 hover:opacity-100">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </button>

                
                <div class="absolute top-4 left-4 z-20 bg-black/60 backdrop-blur-md text-white px-3.5 py-1.5 rounded-full text-xs font-bold flex items-center gap-2 border border-white/20">
                    <span class="w-2 h-2 rounded-full bg-[#C87A53] animate-pulse"></span>
                    <span id="bannerTitle">Özel Koleksiyon Görselleri</span>
                </div>
            </div>

            
            <div class="pt-3 px-1 flex items-center justify-center gap-2.5 overflow-x-auto scrollbar-none">
                <?php
                    $bannerList = (isset($homeBanners) && $homeBanners->count() > 0) ? $homeBanners : collect(range(1,6))->map(fn($i) => (object)['image_url' => url("/images/a{$i}.jpeg"), 'title' => "Görsel {$i}"]);
                ?>
                <?php $__currentLoopData = $bannerList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button onclick="showBanner(<?php echo e($idx); ?>)" 
                            id="thumb-btn-<?php echo e($idx); ?>" 
                            class="banner-thumb w-14 h-14 md:w-16 md:h-16 rounded-xl border-2 border-transparent overflow-hidden shrink-0 bg-stone-200 transition-all duration-300 opacity-70 hover:opacity-100 <?php echo e($idx === 0 ? 'thumb-active opacity-100' : ''); ?>">
                        <img src="<?php echo e(is_object($b) && method_exists($b, 'getImageUrlAttribute') ? $b->image_url : ($b->image_url ?? url($b->image ?? '/images/a1.jpeg'))); ?>" class="w-full h-full object-cover" alt="thumbnail">
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        </div>
    </div>

    <script>
        let currentBannerIndex = 0;
        const totalBanners = <?php echo e((isset($homeBanners) && $homeBanners->count() > 0) ? $homeBanners->count() : 6); ?>;
        let autoPlayTimer = null;

        function showBanner(index) {
            if (index < 0) index = totalBanners - 1;
            if (index >= totalBanners) index = 0;

            const slides = document.querySelectorAll('.banner-slide');
            const thumbs = document.querySelectorAll('.banner-thumb');

            slides.forEach((slide, idx) => {
                if (idx === index) {
                    slide.classList.remove('opacity-0', 'scale-105', 'z-0');
                    slide.classList.add('opacity-100', 'scale-100', 'z-10');
                } else {
                    slide.classList.remove('opacity-100', 'scale-100', 'z-10');
                    slide.classList.add('opacity-0', 'scale-105', 'z-0');
                }
            });

            thumbs.forEach((thumb, idx) => {
                if (idx === index) {
                    thumb.classList.add('thumb-active', 'opacity-100');
                    thumb.classList.remove('opacity-70');
                } else {
                    thumb.classList.remove('thumb-active', 'opacity-100');
                    thumb.classList.add('opacity-70');
                }
            });

            currentBannerIndex = index;
            resetAutoPlay();
        }

        function nextBanner() {
            showBanner(currentBannerIndex + 1);
        }

        function prevBanner() {
            showBanner(currentBannerIndex - 1);
        }

        function startAutoPlay() {
            autoPlayTimer = setInterval(() => {
                nextBanner();
            }, 4500);
        }

        function resetAutoPlay() {
            if (autoPlayTimer) clearInterval(autoPlayTimer);
            startAutoPlay();
        }

        // Scroll step observer
        function scrollToStep(step) {
            const el = document.getElementById('step-' + step);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function setupScrollObserver() {
            const container = document.getElementById('scroll-container');
            const sections = document.querySelectorAll('.step-section');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const step = parseInt(entry.target.getAttribute('data-step'));
                        updateStepDots(step);
                    }
                });
            }, {
                root: container,
                threshold: 0.5
            });

            sections.forEach(sec => observer.observe(sec));
        }

        function updateStepDots(activeStep) {
            for (let i = 1; i <= 3; i++) {
                const dot = document.getElementById('dot-' + i);
                if (dot) {
                    if (i === activeStep) {
                        dot.className = "step-dot w-4 h-4 rounded-full bg-[#C87A53] scale-125 shadow-sm";
                    } else {
                        dot.className = "step-dot w-3.5 h-3.5 rounded-full bg-gray-300 hover:bg-gray-400";
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setupScrollObserver();
            startAutoPlay();
        });
    </script>
</body>

</html><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/home.blade.php ENDPATH**/ ?>