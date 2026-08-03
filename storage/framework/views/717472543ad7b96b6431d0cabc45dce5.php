<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AhşapEvim - Kişiye Özel 3D Dönen Çerçeve | Masif Ahşap El İşçiliği</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(url('/favicon.ico')); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(url('/favicon.ico')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(url('/ahsaplogo_yataybg.png')); ?>">

    
    <script>
        (function () {
            // 1024px = Tailwind lg breakpoint (masaüstü sınırı)
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fbfbf9;
            overflow: hidden;
            /* Prevent body scroll, use snap container */
        }

        /* Custom scrollbar for container */
        #scroll-container::-webkit-scrollbar {
            width: 4px;
        }

        #scroll-container::-webkit-scrollbar-thumb {
            background-color: rgba(242, 122, 26, 0.4);
            border-radius: 4px;
        }

        .step-dot {
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
    </style>
    <!-- Three.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
</head>

<body class="text-gray-800 selection:bg-brand selection:text-white">

    <!-- Minimal Brand Logo & "Ürünleri Keşfet" Button Header Group -->
    <div class="fixed top-6 left-6 lg:top-8 lg:left-14 z-50 pointer-events-auto flex items-center gap-4 lg:gap-6">
        <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2.5">
            <img src="<?php echo e(url('/ahsaplogo_yataybg.png')); ?>" alt="AhşapEvim Logo"
                class="h-12 lg:h-16 w-auto object-contain drop-shadow-sm">
        </a>

        <a href="<?php echo e(url('/urunler')); ?>"
            class="inline-flex items-center gap-3 py-3.5 px-7 bg-gradient-to-r from-[#C87A53] via-[#D87843] to-[#F27A1A] hover:from-[#B56740] hover:to-[#E06912] text-white font-black text-sm lg:text-base rounded-2xl shadow-xl shadow-orange-500/25 hover:shadow-2xl hover:shadow-orange-500/40 border border-white/20 backdrop-blur-md transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95 group">
            <i class="fa-solid fa-store text-lg lg:text-xl group-hover:scale-110 transition-transform duration-300"></i>
            <span class="tracking-wide">Ürünleri Keşfet</span>
            <i class="fa-solid fa-arrow-right text-xs lg:text-sm opacity-90 group-hover:translate-x-1 transition-transform duration-300"></i>
        </a>
    </div>

    <!-- Fixed Vertical Step Indicators on Right Edge (o o o dikey sıralanmış) -->
    <div
        class="fixed right-3 top-1/2 -translate-y-1/2 z-50 flex flex-col items-center gap-3.5 bg-white/95 backdrop-blur-md py-6 px-2.5 rounded-full shadow-2xl border border-gray-200/80">
        <button onclick="scrollToStep(1)" id="dot-1" class="step-dot w-4 h-4 rounded-full bg-brand scale-125 shadow-sm"
            title="1. Adım: Masif Ahşap Gövde"></button>
        <button onclick="scrollToStep(2)" id="dot-2"
            class="step-dot w-3.5 h-3.5 rounded-full bg-gray-300 hover:bg-gray-400"
            title="2. Adım: Fotoğraflarınızı Yükleyin"></button>
        <button onclick="scrollToStep(3)" id="dot-3"
            class="step-dot w-3.5 h-3.5 rounded-full bg-gray-300 hover:bg-gray-400"
            title="3. Adım: 360° Çift Taraflı Sunum"></button>
    </div>

    <!-- SNAP SCROLL CONTAINER: "biraz kaydırınca otomatik kaysın" -->
    <div id="scroll-container" class="h-screen w-full overflow-y-auto snap-y snap-mandatory scroll-smooth relative">

        <!-- LEFT COLUMN: Widen to eliminate empty gap (w-full lg:w-1/2 lg:pl-16 lg:pr-10) -->
        <div class="w-full lg:w-1/2 z-10 px-6 lg:pl-16 lg:pr-10">

            <!-- STEP 1: Çerçevenizi Beğenin -->
            <section id="step-1" class="step-section h-screen snap-start snap-always flex flex-col justify-center py-12"
                data-step="1">
                <div
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-brand/10 text-brand text-xs font-bold rounded-full mb-5 w-max shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span> 1. ADIM — DOĞAL AHŞAP KASNAK
                </div>
                <h1
                    class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-5 font-serif">
                    Ustalıkla İşlenen <br><span
                        class="text-brand underline decoration-brand/30 underline-offset-8 font-serif">Doğal Ahşabın
                        Zamansız Zarafeti</span>
                </h1>
                <p class="text-base lg:text-lg text-gray-600 leading-relaxed mb-6 max-w-xl">
                    Anılarınızı sıradan çerçevelere değil, özenle tasarlanmış masif ahşap çerçevelere taşıyın. Doğal
                    dokusu, modern tasarımı ve kaliteli işçiliğiyle yaşam alanlarınıza sıcaklık ve karakter katın.
                    Tarzınıza en uygun rengi seçin, eviniz için kusursuz çerçeveyi keşfedin.
                </p>

                <!-- Interactive Wood Color Selector (Professional e-commerce style) -->
                <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm max-w-xl">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Masif Ahşap
                            Rengini Seçin</label>
                        <span id="activeWoodLabel" class="text-xs font-bold text-brand">Doğal Ceviz</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <button onclick="setWoodColor('#4a2e1b', 'Doğal Ceviz')" id="btn-wood-0"
                            class="py-3 px-4 rounded-xl border-2 border-brand bg-[#4a2e1b] text-white text-xs font-bold shadow-sm transition hover:scale-105 flex items-center justify-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                            <span>Doğal Ceviz</span>
                        </button>
                        <button onclick="setWoodColor('#8b5a2b', 'Sıcak Meşe')" id="btn-wood-1"
                            class="py-3 px-4 rounded-xl border border-gray-300 bg-[#8b5a2b] text-white text-xs font-bold shadow-sm transition hover:scale-105 flex items-center justify-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-200"></span>
                            <span>Sıcak Meşe</span>
                        </button>
                        <button onclick="setWoodColor('#2b1d0c', 'Asil Wenge')" id="btn-wood-2"
                            class="py-3 px-4 rounded-xl border border-gray-300 bg-[#2b1d0c] text-white text-xs font-bold shadow-sm transition hover:scale-105 flex items-center justify-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span>
                            <span>Asil Wenge</span>
                        </button>
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <span>2. Adım: Fotoğraflarınızı Çerçevede Test Edin</span>
                    <i class="fa-solid fa-arrow-down animate-bounce text-brand"></i>
                </div>
            </section>

            <!-- STEP 2: Resminizi Yükleyin ve Gözlemleyin (Professional e-commerce copy) -->
            <section id="step-2" class="step-section h-screen snap-start snap-always flex flex-col justify-center py-12"
                data-step="2">
                <div
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-brand/10 text-brand text-xs font-bold rounded-full mb-5 w-max shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-brand"></span> 2. ADIM — KİŞİSELLEŞTİRME & ÖNİZLEME
                </div>
                <h2
                    class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-5 font-serif">
                    En Güzel Anılarınız, <br><span
                        class="text-brand underline decoration-brand/30 underline-offset-8 font-serif">Size Özel Bir
                        Tasarımda</span> Hayat Bulsun
                </h2>
                <p class="text-base lg:text-lg text-gray-600 leading-relaxed mb-6 max-w-xl">
                    Çift taraflı tasarımı sayesinde iki farklı fotoğrafınızı tek çerçevede sergileyin. Fotoğraflarınızı
                    yükleyin, çerçevenizi her açıdan inceleyin ve eviniz için en özel görünümü oluşturun.
                </p>

                <!-- Dual Photo Upload Box (Professional e-commerce style) -->
                <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm max-w-xl">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Çift Taraflı
                            Galeri Önizlemesi (2 Fotoğraf)</label>
                        <span class="text-[11px] font-semibold text-brand bg-brand/5 px-2.5 py-0.5 rounded-md">3D
                            Derinlikli Hazne</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Photo 1 (Front Face) -->
                        <label for="step2PhotoInput1"
                            class="flex flex-col items-center justify-center border-2 border-dashed border-blue-300 bg-blue-50/40 hover:bg-blue-50/80 hover:border-blue-500 rounded-xl p-3 cursor-pointer transition group relative overflow-hidden h-28">
                            <div id="preview-container-1" class="flex flex-col items-center justify-center text-center">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-1.5 group-hover:scale-110 transition shadow-sm">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-800">1. Fotoğraf (Ön Yüz)</span>
                                <span class="text-[10px] text-gray-500 mt-0.5">Dosya Seç veya Sürükle</span>
                            </div>
                            <img id="preview-img-1"
                                class="hidden absolute inset-0 w-full h-full object-cover rounded-xl" />
                            <input type="file" id="step2PhotoInput1" class="hidden" accept="image/*">
                        </label>

                        <!-- Photo 2 (Back Face) -->
                        <label for="step2PhotoInput2"
                            class="flex flex-col items-center justify-center border-2 border-dashed border-blue-300 bg-blue-50/40 hover:bg-blue-50/80 hover:border-blue-500 rounded-xl p-3 cursor-pointer transition group relative overflow-hidden h-28">
                            <div id="preview-container-2" class="flex flex-col items-center justify-center text-center">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mb-1.5 group-hover:scale-110 transition shadow-sm">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-800">2. Fotoğraf (Arka Yüz)</span>
                                <span class="text-[10px] text-gray-500 mt-0.5">Dosya Seç veya Sürükle</span>
                            </div>
                            <img id="preview-img-2"
                                class="hidden absolute inset-0 w-full h-full object-cover rounded-xl" />
                            <input type="file" id="step2PhotoInput2" class="hidden" accept="image/*">
                        </label>
                    </div>

                    <div id="step2UploadSuccess"
                        class="hidden mt-3 p-2.5 bg-green-50 border border-green-200 rounded-lg text-green-700 text-xs font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span id="step2SuccessText">Fotoğraflarınız işlendi! 3. adımda 360° dönerken
                            inceleyebilirsiniz.</span>
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <span>3. Adım: 360° Dönen Çift Taraflı Sunum</span>
                    <i class="fa-solid fa-arrow-down animate-bounce text-brand"></i>
                </div>
            </section>

            <!-- STEP 3: 360° Dönen Mekanizma ve Alışveriş / Satışa Hazır E-Ticaret Sunumu -->
            <section id="step-3" class="step-section h-screen snap-start snap-always flex flex-col justify-center py-12"
                data-step="3">
                <div
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-brand/10 text-brand text-xs font-bold rounded-full mb-5 w-max shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-brand"></span> 3. ADIM — 360° DÖNEN DENEYİM & SİPARİŞ
                </div>
                <h2
                    class="text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-5 font-serif">
                    İki Farklı Anı, <br><span
                        class="text-brand underline decoration-brand/30 underline-offset-8 font-serif">Tek Bir Çerçevede
                        360° Şıklık</span>
                </h2>
                <p class="text-base lg:text-lg text-gray-600 leading-relaxed mb-6 max-w-xl">
                    En değerli iki fotoğrafınızı tek bir çerçevede buluşturun. Çift taraflı dönebilen tasarımı sayesinde
                    dilediğiniz an farklı anılarınızı sergileyin. Doğal masif ahşap, özenli işçilik ve ücretsiz kargo
                    ile şimdi keşfedin.
                </p>

                <!-- Clean E-Commerce CTA Button -->
                <div class="mt-2">
                    <a href="<?php echo e(url('/urunler')); ?>"
                        class="inline-flex items-center justify-center gap-3 py-4 px-8 bg-brand hover:bg-brand-dark text-white font-extrabold text-base rounded-2xl shadow-xl shadow-brand/25 hover:shadow-2xl hover:shadow-brand/40 transition-all transform hover:-translate-y-0.5">
                        <span>Ürünleri Keşfet</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </section>

        </div>

    </div>

    <!-- RIGHT COLUMN: Enlarge Studio Card & Move Leftward to Eliminate Empty Gap ("boşluğu kapatalım") -->
    <!-- w-[44vw] max-w-[620px] fixed right-10 top-1/2 -translate-y-1/2 -->
    <div
        class="hidden lg:flex w-[44vw] max-w-[620px] h-[82vh] fixed right-10 top-1/2 -translate-y-1/2 z-20 items-center justify-center pointer-events-auto">
        <div
            class="w-full h-full bg-gradient-to-b from-white via-[#fcfdfe] to-[#f1f5f9] rounded-3xl border border-gray-200/80 shadow-2xl overflow-hidden relative">
            <!-- 3D Viewport -->
            <div id="showcase3D" class="w-full h-full cursor-grab active:cursor-grabbing"></div>

            <!-- Active Step Status Badge on Canvas -->
            <div
                class="absolute top-6 left-6 z-10 bg-white/95 backdrop-blur-md px-3.5 py-2 rounded-xl shadow-sm border border-gray-200/80 flex items-center gap-2">
                <div id="canvas-step-icon" class="w-2 h-2 rounded-full bg-brand animate-pulse"></div>
                <span id="canvas-step-label" class="text-xs font-bold text-gray-800">1. Adım: Masif Ahşap Gövde</span>
            </div>

            <!-- Rotate & Zoom Hint -->
            <div
                class="absolute bottom-6 left-6 z-10 bg-black/65 backdrop-blur-md text-white px-3.5 py-2 rounded-xl text-[11px] font-medium flex items-center gap-2 pointer-events-none shadow-lg">
                <i class="fa-solid fa-hand-pointer text-brand animate-bounce"></i>
                <span>Modeli döndürmek için sürükleyin</span>
            </div>

            <!-- Reset Camera Button -->
            <div class="absolute top-6 right-6 z-10">
                <button onclick="resetCameraView()"
                    class="w-9 h-9 bg-white/95 hover:bg-white text-gray-600 hover:text-gray-900 rounded-xl shadow-sm border border-gray-200/80 flex items-center justify-center transition"
                    title="Kamerayı Sıfırla">
                    <i class="fa-solid fa-rotate-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // --- THREE.JS SHOWCASE ENGINE WITH RECESSED INNER FRAME & DUAL USER PHOTOS ---
        let scene, camera, renderer, controls;
        let allFramesGroup, outerGroup, innerGroup;
        let outerMeshes = [];
        let innerMeshes = [];
        let customPhotoFront, customPhotoBack;
        let activeWoodColor = '#4a2e1b';
        let currentStep = 1;
        let isRotatingInner = false;

        // User uploaded textures for Front and Back
        let userUploadedTexture1 = null;
        let userUploadedTexture2 = null;

        // Target vertical position for smooth downward gliding
        let targetGroupY = 2.0;

        // Predetermined admin sample textures for Step 3
        let sampleFrontTexture, sampleBackTexture;
        var sharedWoodBumpMap = null;

        const container = document.getElementById('showcase3D');

        init3D();
        setupScrollObserver();

        function init3D() {
            scene = new THREE.Scene();

            const rect = container.getBoundingClientRect();
            camera = new THREE.PerspectiveCamera(38, rect.width / (rect.height || window.innerHeight), 0.1, 1000);
            camera.position.set(0, 1.0, 54);

            renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setSize(rect.width, rect.height || window.innerHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            renderer.outputEncoding = THREE.sRGBEncoding;
            container.innerHTML = '';
            container.appendChild(renderer.domElement);

            controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;
            controls.maxPolarAngle = Math.PI / 2 + 0.1;
            controls.minDistance = 15;
            controls.maxDistance = 75;
            controls.target.set(0, 0, 0);

            // Soft Studio Gallery Lighting ("ışık çok fazla ışığı azalt")
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.45);
            scene.add(ambientLight);

            const dirLight = new THREE.DirectionalLight(0xffffff, 0.65);
            dirLight.position.set(15, 25, 20);
            dirLight.castShadow = true;
            dirLight.shadow.mapSize.width = 2048;
            dirLight.shadow.mapSize.height = 2048;
            scene.add(dirLight);

            const fillLight = new THREE.DirectionalLight(0xffeedd, 0.25);
            fillLight.position.set(-15, -10, 15);
            scene.add(fillLight);

            // Shadow Floor
            const floorGeo = new THREE.PlaneGeometry(120, 120);
            const floorMat = new THREE.ShadowMaterial({ opacity: 0.10 });
            const floor = new THREE.Mesh(floorGeo, floorMat);
            floor.rotation.x = -Math.PI / 2;
            floor.position.y = -15;
            floor.receiveShadow = true;
            scene.add(floor);

            // Create Mitered Frames (with Recessed Depth for Inner Frame)
            buildShowcaseFrames();

            // Create Sample Photos for Step 3
            sampleFrontTexture = createArtisticPhotoTexture("MUTLU ANILAR", "AhşapEvim Koleksiyonu", "#1e3a8a", "#3b82f6");
            sampleBackTexture = createArtisticPhotoTexture("HAYAT & SEVGİ", "Masif Sanat 2026", "#7c2d12", "#f97316");

            // Enhance with Unsplash high-res artwork asynchronously
            enhanceWithExternalPhotos();

            // Resize Listener
            window.addEventListener('resize', onWindowResize);

            // Animation Loop
            animate();
        }

        function buildShowcaseFrames() {
            allFramesGroup = new THREE.Group();
            allFramesGroup.name = "all_frames_container";
            allFramesGroup.position.y = targetGroupY; // Start at upper position for Step 1

            outerGroup = new THREE.Group();
            outerGroup.name = "showcase_outer_frame";
            innerGroup = new THREE.Group();
            innerGroup.name = "showcase_inner_frame";

            const woodMat = new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.75, metalness: 0.05 });

            // 1. OUTER FRAME (Width: 22, Height: 28, Depth: 3, Thickness: 3)
            const outW = 22, outH = 28, outD = 3, outT = 3;

            const topOuter = new THREE.Mesh(createMiteredFramePiece(outW, outT, outD, true, true), woodMat);
            topOuter.position.y = outH / 2 - outT / 2;
            topOuter.castShadow = true; topOuter.receiveShadow = true;

            const botOuter = new THREE.Mesh(createMiteredFramePiece(outW, outT, outD, true, true), woodMat);
            botOuter.rotation.z = Math.PI;
            botOuter.position.y = -outH / 2 + outT / 2;
            botOuter.castShadow = true; botOuter.receiveShadow = true;

            const leftOuter = new THREE.Mesh(createMiteredFramePiece(outH, outT, outD, true, true), woodMat);
            leftOuter.rotation.z = Math.PI / 2;
            leftOuter.position.x = -outW / 2 + outT / 2;
            leftOuter.castShadow = true; leftOuter.receiveShadow = true;

            const rightOuter = new THREE.Mesh(createMiteredFramePiece(outH, outT, outD, true, true), woodMat);
            rightOuter.rotation.z = -Math.PI / 2;
            rightOuter.position.x = outW / 2 - outT / 2;
            rightOuter.castShadow = true; rightOuter.receiveShadow = true;

            outerGroup.add(topOuter, botOuter, leftOuter, rightOuter);
            outerMeshes.push(topOuter, botOuter, leftOuter, rightOuter);

            // 2. INNER FRAME WITH DEEP RECESSED BORDERS ("buradaki iç çerçevelere derinlik ver, resim çerçevenin içinde gibi dursun")
            // Width: 15, Height: 21, Depth: 2.6 (Thicker depth to wrap around the picture!), Thickness: 1.4
            const inW = 15, inH = 21, inD = 2.6, inT = 1.4;

            const topInner = new THREE.Mesh(createMiteredFramePiece(inW, inT, inD, true, true), woodMat);
            topInner.position.y = inH / 2 - inT / 2;
            topInner.castShadow = true; topInner.receiveShadow = true;

            const botInner = new THREE.Mesh(createMiteredFramePiece(inW, inT, inD, true, true), woodMat);
            botInner.rotation.z = Math.PI;
            botInner.position.y = -inH / 2 + inT / 2;
            botInner.castShadow = true; botInner.receiveShadow = true;

            const leftInner = new THREE.Mesh(createMiteredFramePiece(inH, inT, inD, true, true), woodMat);
            leftInner.rotation.z = Math.PI / 2;
            leftInner.position.x = -inW / 2 + inT / 2;
            leftInner.castShadow = true; leftInner.receiveShadow = true;

            const rightInner = new THREE.Mesh(createMiteredFramePiece(inH, inT, inD, true, true), woodMat);
            rightInner.rotation.z = -Math.PI / 2;
            rightInner.position.x = inW / 2 - inT / 2;
            rightInner.castShadow = true; rightInner.receiveShadow = true;

            innerGroup.add(topInner, botInner, leftInner, rightInner);
            innerMeshes.push(topInner, botInner, leftInner, rightInner);

            // Center divider / inner wood backing
            const divGeom = new THREE.BoxGeometry(inW - inT * 1.5, inH - inT * 1.5, 0.15);
            const divMesh = new THREE.Mesh(divGeom, woodMat);
            innerGroup.add(divMesh);
            innerMeshes.push(divMesh);

            // 3. RECESSED PHOTOS (Front & Back)
            // Placed at z = 0.38 and -0.38, while wood border extends to z = 1.30 and -1.30!
            // This creates ~0.92 units of authentic physical recessed gallery depth!
            const photoW = inW - (inT * 2) + 0.35;
            const photoH = inH - (inT * 2) + 0.35;
            const photoMatFront = new THREE.MeshStandardMaterial({ roughness: 0.35, metalness: 0.0 });
            const photoMatBack = new THREE.MeshStandardMaterial({ roughness: 0.35, metalness: 0.0 });

            customPhotoFront = new THREE.Mesh(new THREE.PlaneGeometry(photoW, photoH), photoMatFront);
            customPhotoFront.position.z = 0.38; // Deeply recessed inside the wood borders!
            customPhotoFront.visible = false;   // Empty inner frame by default in Step 2
            innerGroup.add(customPhotoFront);

            customPhotoBack = new THREE.Mesh(new THREE.PlaneGeometry(photoW, photoH), photoMatBack);
            customPhotoBack.rotation.y = Math.PI;
            customPhotoBack.position.z = -0.38; // Deeply recessed inside the back wood borders!
            customPhotoBack.visible = false;    // Empty inner frame by default in Step 2
            innerGroup.add(customPhotoBack);

            // 4. METAL PINS (Top and Bottom Menteşeler)
            const pinMat = new THREE.MeshStandardMaterial({ color: 0x999999, metalness: 0.9, roughness: 0.2 });
            const pinGeo = new THREE.CylinderGeometry(0.32, 0.32, 2.5, 16);

            const pinTop = new THREE.Mesh(pinGeo, pinMat);
            pinTop.position.y = inH / 2 + 1.25;
            innerGroup.add(pinTop);

            const pinBottom = new THREE.Mesh(pinGeo, pinMat);
            pinBottom.position.y = -(inH / 2) - 1.25;
            innerGroup.add(pinBottom);

            allFramesGroup.add(outerGroup);
            allFramesGroup.add(innerGroup);
            scene.add(allFramesGroup);

            // Apply procedural wood texture to all wooden parts
            applyWoodTextureToAll('#4a2e1b');

            // Default: Step 1 active (innerGroup hidden)
            innerGroup.visible = false;
        }

        function createMiteredFramePiece(L, T, D, miterLeft, miterRight) {
            const shape = new THREE.Shape();
            const halfL = L / 2;
            const halfT = T / 2;

            shape.moveTo(-halfL, halfT);
            shape.lineTo(halfL, halfT);

            const rightInnerX = miterRight ? (halfL - T) : halfL;
            shape.lineTo(rightInnerX, -halfT);

            const leftInnerX = miterLeft ? (-halfL + T) : -halfL;
            shape.lineTo(leftInnerX, -halfT);

            shape.lineTo(-halfL, halfT);

            const extrudeSettings = { depth: D, bevelEnabled: false, curveSegments: 1 };
            const geom = new THREE.ExtrudeGeometry(shape, extrudeSettings);

            geom.computeBoundingBox();
            const zOffset = -0.5 * (geom.boundingBox.max.z - geom.boundingBox.min.z);
            geom.translate(0, 0, zOffset);
            return geom;
        }

        // Photo-Realistic Masif Wood Texture Generator ("gerçek ahşap dokusu ver çizim gibi duruyor")
        function getWoodBumpMap() {
            if (sharedWoodBumpMap) return sharedWoodBumpMap;
            const canvas = document.createElement('canvas');
            canvas.width = 512;
            canvas.height = 512;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#808080';
            ctx.fillRect(0, 0, 512, 512);

            for (let i = 0; i < 1400; i++) {
                const x = Math.random() * 512;
                const y = Math.random() * 512;
                const len = 30 + Math.random() * 90;
                ctx.strokeStyle = Math.random() > 0.5 ? '#656565' : '#959595';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(x, y);
                ctx.lineTo(x, y + len);
                ctx.stroke();
            }
            sharedWoodBumpMap = new THREE.CanvasTexture(canvas);
            sharedWoodBumpMap.wrapS = THREE.RepeatWrapping;
            sharedWoodBumpMap.wrapT = THREE.RepeatWrapping;
            sharedWoodBumpMap.repeat.set(1.5, 1.5);
            return sharedWoodBumpMap;
        }

        function createRealisticWoodTexture(hexColor) {
            const canvas = document.createElement('canvas');
            canvas.width = 1024;
            canvas.height = 1024;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = hexColor;
            ctx.fillRect(0, 0, 1024, 1024);

            // 1. Fine longitudinal wood fibers for authentic masif grain
            for (let i = 0; i < 1600; i++) {
                const x = Math.random() * 1024;
                const y = Math.random() * 1024;
                const length = 50 + Math.random() * 200;
                const opacity = 0.03 + Math.random() * 0.06;
                const isDark = Math.random() > 0.4;
                ctx.strokeStyle = isDark ? `rgba(0, 0, 0, ${opacity})` : `rgba(255, 255, 255, ${opacity * 0.5})`;
                ctx.lineWidth = 0.8 + Math.random() * 1.5;
                ctx.beginPath();
                ctx.moveTo(x, y);
                ctx.lineTo(x + (Math.random() - 0.5) * 3, y + length);
                ctx.stroke();
            }

            // 2. Natural wood growth rings
            for (let y = 0; y < 1024; y += 4) {
                const ringNoise = Math.sin(y * 0.015 + Math.sin(y * 0.005) * 4) * 0.5 + 0.5;
                if (ringNoise > 0.65) {
                    ctx.fillStyle = `rgba(0, 0, 0, ${(ringNoise - 0.65) * 0.20})`;
                    ctx.fillRect(0, y, 1024, 4);
                } else if (ringNoise < 0.25) {
                    ctx.fillStyle = `rgba(255, 255, 255, ${(0.25 - ringNoise) * 0.10})`;
                    ctx.fillRect(0, y, 1024, 4);
                }
            }

            // 3. Subtle wood pores & grain micro-texture
            for (let i = 0; i < 350; i++) {
                const px = Math.random() * 1024;
                const py = Math.random() * 1024;
                const pr = 1 + Math.random() * 2;
                ctx.fillStyle = 'rgba(0, 0, 0, 0.07)';
                ctx.beginPath();
                ctx.ellipse(px, py, pr * 0.5, pr * 4, 0, 0, Math.PI * 2);
                ctx.fill();
            }

            const texture = new THREE.CanvasTexture(canvas);
            texture.wrapS = THREE.RepeatWrapping;
            texture.wrapT = THREE.RepeatWrapping;
            texture.repeat.set(1.5, 1.5);
            texture.anisotropy = 16;
            texture.needsUpdate = true;
            return texture;
        }

        function applyWoodTextureToAll(hexColor) {
            activeWoodColor = hexColor;
            const realisticTex = createRealisticWoodTexture(hexColor);
            const bumpTex = getWoodBumpMap();

            [...outerMeshes, ...innerMeshes].forEach(m => {
                if (m && m.material) {
                    m.material.map = realisticTex;
                    m.material.bumpMap = bumpTex;
                    m.material.bumpScale = 0.035; // Tactile 3D wood grain ridges
                    m.material.roughness = 0.52;  // Satin oiled walnut / oak lustre
                    m.material.needsUpdate = true;
                }
            });
        }

        function setWoodColor(hexColor, name) {
            applyWoodTextureToAll(hexColor);
            const label = document.getElementById('activeWoodLabel');
            if (label && name) {
                label.innerText = name;
            }
        }

        // Create High-Res Artistic Sample Photo Texture via Canvas
        function createArtisticPhotoTexture(title, subtitle, bgColor, accentColor) {
            const canvas = document.createElement('canvas');
            canvas.width = 1024;
            canvas.height = 1024;
            const ctx = canvas.getContext('2d');

            const grad = ctx.createLinearGradient(0, 0, 1024, 1024);
            grad.addColorStop(0, bgColor);
            grad.addColorStop(1, '#0f172a');
            ctx.fillStyle = grad;
            ctx.fillRect(0, 0, 1024, 1024);

            ctx.strokeStyle = accentColor;
            ctx.lineWidth = 16;
            ctx.strokeRect(60, 60, 904, 904);

            ctx.beginPath();
            ctx.arc(512, 420, 140, 0, Math.PI * 2);
            ctx.fillStyle = accentColor;
            ctx.fill();

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 85px Inter, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('✨', 512, 450);

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 64px Inter, sans-serif';
            ctx.fillText(title, 512, 660);

            ctx.fillStyle = '#cbd5e1';
            ctx.font = '36px Inter, sans-serif';
            ctx.fillText(subtitle, 512, 740);

            const texture = new THREE.CanvasTexture(canvas);
            texture.needsUpdate = true;
            return texture;
        }

        // External photo enhancement disabled — canvas textures used as reliable fallback
        function enhanceWithExternalPhotos() {
            // Unsplash requests removed: they caused the browser tab to keep spinning
            // and caused slow page transitions. Canvas-generated textures are used instead.
        }

        // --- PHOTO UPLOAD HANDLERS FOR FRONT (1. RESİM) & BACK (2. RESİM) ---
        document.getElementById('step2PhotoInput1').addEventListener('change', function (e) {
            handlePhotoUpload(e.target.files[0], 1);
        });

        document.getElementById('step2PhotoInput2').addEventListener('change', function (e) {
            handlePhotoUpload(e.target.files[0], 2);
        });

        function handlePhotoUpload(file, side) {
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (event) {
                const img = new Image();
                img.onload = function () {
                    const texture = new THREE.Texture(img);
                    texture.needsUpdate = true;

                    // Show thumbnail preview in UI
                    const previewImg = document.getElementById(`preview-img-${side}`);
                    const previewContainer = document.getElementById(`preview-container-${side}`);
                    if (previewImg && previewContainer) {
                        previewImg.src = event.target.result;
                        previewImg.classList.remove('hidden');
                        previewContainer.classList.add('hidden');
                    }

                    if (side === 1) {
                        userUploadedTexture1 = texture;
                        if (currentStep === 2) {
                            customPhotoFront.material.map = userUploadedTexture1;
                            customPhotoFront.material.needsUpdate = true;
                            customPhotoFront.visible = true;
                        }
                    } else if (side === 2) {
                        userUploadedTexture2 = texture;
                        if (currentStep === 2) {
                            customPhotoBack.material.map = userUploadedTexture2;
                            customPhotoBack.material.needsUpdate = true;
                            customPhotoBack.visible = true;
                        }
                    }

                    // Show success message
                    const successBox = document.getElementById('step2UploadSuccess');
                    const successText = document.getElementById('step2SuccessText');
                    if (userUploadedTexture1 && userUploadedTexture2) {
                        successText.innerText = "Her 2 fotoğrafınız da çerçevenize eklendi! 3. adımda dönerken izleyebilirsiniz.";
                    } else if (side === 1) {
                        successText.innerText = "1. fotoğrafınız ön yüze eklendi! Dilerseniz 2. arka yüzü de seçebilirsiniz.";
                    } else {
                        successText.innerText = "2. fotoğrafınız arka yüze eklendi! 3. adımda dönerken izleyebilirsiniz.";
                    }
                    successBox.classList.remove('hidden');
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }

        // --- STEP NAVIGATION ENGINE WITH VERTICAL DOWNWARD GLIDE ---
        function setStep(stepNum) {
            if (currentStep === stepNum) return;
            currentStep = stepNum;

            // Update Vertical Dots on Right Edge
            for (let i = 1; i <= 3; i++) {
                const dot = document.getElementById(`dot-${i}`);
                if (i === stepNum) {
                    dot.className = "step-dot w-4 h-4 rounded-full bg-brand scale-125 shadow-sm";
                } else {
                    dot.className = "step-dot w-3.5 h-3.5 rounded-full bg-gray-300 hover:bg-gray-400";
                }
            }

            // Update Top-Left Canvas Status Badge
            const badgeIcon = document.getElementById('canvas-step-icon');
            const badgeLabel = document.getElementById('canvas-step-label');

            if (stepNum === 1) {
                badgeIcon.className = "w-2 h-2 rounded-full bg-brand animate-pulse";
                badgeLabel.innerText = "1. Adım: Masif Ahşap Gövde";

                targetGroupY = 2.2; // Upper position inside studio card
                outerGroup.visible = true;
                innerGroup.visible = false;
                isRotatingInner = false;
                innerGroup.rotation.y = 0;
                customPhotoFront.visible = false;
                customPhotoBack.visible = false;
            }
            else if (stepNum === 2) {
                badgeIcon.className = "w-2 h-2 rounded-full bg-blue-600 animate-pulse";
                badgeLabel.innerText = "2. Adım: Fotoğraf Önizleme";

                targetGroupY = 0.0; // Glides downwards ("dış çerçeve aşağıya kayacak")!
                outerGroup.visible = true;
                innerGroup.visible = true;
                isRotatingInner = false;
                innerGroup.rotation.y = 0; // Face user directly

                // In Step 2, show user uploaded photos if selected, otherwise keep inner frame empty
                if (userUploadedTexture1) {
                    customPhotoFront.material.map = userUploadedTexture1;
                    customPhotoFront.material.needsUpdate = true;
                    customPhotoFront.visible = true;
                } else {
                    customPhotoFront.visible = false;
                }

                if (userUploadedTexture2) {
                    customPhotoBack.material.map = userUploadedTexture2;
                    customPhotoBack.material.needsUpdate = true;
                    customPhotoBack.visible = true;
                } else {
                    customPhotoBack.visible = false;
                }
            }
            else if (stepNum === 3) {
                badgeIcon.className = "w-2 h-2 rounded-full bg-amber-600 animate-pulse";
                badgeLabel.innerText = "3. Adım: 360° Çift Taraflı Sunum";

                targetGroupY = -1.5; // Glides further downwards ("3. adımda çerçeve aşağıya inecek")!
                outerGroup.visible = true;
                innerGroup.visible = true;
                isRotatingInner = true;

                // Use uploaded photo 1 or fallback to sample front photo
                customPhotoFront.material.map = userUploadedTexture1 || sampleFrontTexture;
                customPhotoFront.material.needsUpdate = true;
                customPhotoFront.visible = true;

                // Use uploaded photo 2 (or photo 1 if only 1 uploaded) or fallback to sample back photo
                customPhotoBack.material.map = userUploadedTexture2 || userUploadedTexture1 || sampleBackTexture;
                customPhotoBack.material.needsUpdate = true;
                customPhotoBack.visible = true;
            }
        }

        function scrollToStep(stepNum) {
            const targetSection = document.getElementById(`step-${stepNum}`);
            const scrollContainer = document.getElementById('scroll-container');
            if (targetSection && scrollContainer) {
                targetSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setStep(stepNum);
            }
        }

        function setupScrollObserver() {
            const sections = document.querySelectorAll('.step-section');
            const scrollContainer = document.getElementById('scroll-container');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const stepNum = parseInt(entry.target.getAttribute('data-step'));
                        setStep(stepNum);
                    }
                });
            }, { root: scrollContainer, threshold: 0.5 });

            sections.forEach(sec => observer.observe(sec));
        }

        function resetCameraView() {
            camera.position.set(0, 1.0, 54);
            controls.target.set(0, 0, 0);
            controls.update();
            if (!isRotatingInner) {
                innerGroup.rotation.set(0, 0, 0);
            }
        }

        function onWindowResize() {
            const rect = container.getBoundingClientRect();
            if (rect.width === 0) return;
            camera.aspect = rect.width / (rect.height || window.innerHeight);
            camera.updateProjectionMatrix();
            renderer.setSize(rect.width, rect.height || window.innerHeight);
        }

        function animate() {
            requestAnimationFrame(animate);

            // Smoothly glide the entire frame group towards targetGroupY on Y axis
            if (allFramesGroup) {
                allFramesGroup.position.y += (targetGroupY - allFramesGroup.position.y) * 0.08;
            }

            // Step 3: Only the inner frame smoothly rotates around its Y-axis ("çok hızlı dönüyor yavaşlat")
            if (isRotatingInner && innerGroup && innerGroup.visible) {
                innerGroup.rotation.y += 0.0035;
            }

            // Step 1: Subtle breathing/sway animation on outer frame for premium feel
            if (currentStep === 1 && outerGroup) {
                outerGroup.rotation.y = Math.sin(Date.now() * 0.001) * 0.15;
            } else if (outerGroup && currentStep !== 1) {
                outerGroup.rotation.y = 0;
            }

            controls.update();
            renderer.render(scene, camera);
        }
    </script>
</body>

</html><?php /**PATH C:\xampp\htdocs\cerceve\resources\views/home.blade.php ENDPATH**/ ?>