@extends('layouts.app')

@section('title', ($product->name ?? 'Ürün Detayı') . ' - AhşapEvim')

@if($product->threeDTemplate)
    @push('head_scripts')
        <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    @endpush
@endif

@section('content')
<div class="bg-white pb-12">
    <div class="container mx-auto px-4 py-4">
        <!-- Breadcrumb -->
        <nav class="flex text-[13px] text-gray-500 mb-6">
            <a href="{{ url('/') }}" class="hover:underline">Anasayfa</a>
            <span class="mx-2">></span>
            <a href="{{ url('/urunler') }}?category={{ $product->category->slug ?? '' }}" class="hover:underline">{{ $product->category->name ?? 'Kategori' }}</a>
            <span class="mx-2">></span>
            <span class="text-gray-800">{{ $product->name }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Side: Images -->
            <div class="w-full lg:w-[45%] flex gap-4">
                <!-- Thumbnails -->
                <div class="hidden md:flex flex-col gap-2 w-16 flex-shrink-0">
                    <div class="thumb-box w-16 h-20 border-2 border-brand rounded-md cursor-pointer overflow-hidden p-1 bg-white transition" onclick="changeMainImage(this, '{{ $product->image ?: '/cerceve.png' }}')">
                        <img src="{{ $product->image ?: '/cerceve.png' }}" class="w-full h-full object-contain" alt="thumbnail">
                    </div>
                    @if(count($product->gallery_urls) > 0)
                        @foreach($product->gallery_urls as $addImg)
                            <div class="thumb-box w-16 h-20 border border-gray-200 rounded-md cursor-pointer overflow-hidden p-1 bg-white hover:border-gray-400 transition" onclick="changeMainImage(this, '{{ $addImg }}')">
                                <img src="{{ $addImg }}" class="w-full h-full object-contain" alt="thumbnail">
                            </div>
                        @endforeach
                    @endif
                    @if($product->youtube_id)
                        <div class="thumb-box w-16 h-20 border-2 border-red-300 rounded-md cursor-pointer overflow-hidden relative bg-black group shadow-sm hover:border-red-600 transition" onclick="openYoutubeModal('https://www.youtube.com/embed/{{ $product->youtube_id }}')">
                            <img src="https://img.youtube.com/vi/{{ $product->youtube_id }}/hqdefault.jpg" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition" alt="video thumbnail">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/40 group-hover:bg-black/20 transition">
                                <i class="fa-brands fa-youtube text-red-600 text-2xl drop-shadow-md"></i>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Main Image -->
                <div id="mainImageContainer" class="flex-1 bg-gray-50 border border-gray-100 rounded-xl relative overflow-hidden flex items-center justify-center h-[480px]">
                    <img id="mainProductImage" src="{{ $product->image ?: '/cerceve.png' }}" alt="{{ $product->name }}" class="w-full h-full object-contain mix-blend-multiply p-4 transition-all duration-300 z-10">
                </div>
            </div>

            <!-- Middle Side: Info -->
            <div class="w-full lg:w-[30%] flex flex-col">
                <h1 class="text-2xl font-bold text-gray-800 mb-3 leading-tight">
                    {{ $product->name }}
                </h1>
                
                <hr class="border-gray-200 mb-4">

                <!-- Specs -->
                <div class="text-[13px] text-gray-700 mb-4 space-y-2">
                    @if(isset($product->features['color']) && $product->features['color'])
                        <p><span class="font-bold text-gray-800">Renk:</span> {{ $product->features['color'] }}</p>
                    @endif
                    @if(isset($product->features['size']) && $product->features['size'])
                        <p><span class="font-bold text-gray-800">Boyut/Ebat:</span> {{ $product->features['size'] }}</p>
                    @endif
                </div>
                
                <!-- Expanded Description Area -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-base font-bold text-gray-800 mb-2">Ürün Açıklaması</h3>
                    <div class="prose max-w-none text-gray-600 text-sm leading-relaxed mb-6">
                        <p>{{ $product->description ?: 'Özel ahşap işçiliği ile hazırlanmış yüksek kaliteli dekoratif ürün.' }}</p>
                    </div>
                    
                    <button type="button" onclick="openCustomizationModal()" class="bg-brand/5 border-2 border-brand/20 hover:bg-brand/10 text-brand font-bold py-4 px-6 rounded-xl transition-colors flex items-center justify-center gap-3 w-full md:w-auto shadow-sm">
                        <i class="fa-solid fa-sliders text-xl"></i>
                        Kişiselleştir ve Ön İzle
                    </button>
                </div>
            </div>

            <!-- Right Side: Buybox -->
            <div class="w-full lg:w-[25%]">
                <form id="addToCartForm" action="{{ url('/sepet/ekle') }}" method="POST" enctype="multipart/form-data" class="border border-gray-200 rounded-xl p-5 bg-white shadow-sm sticky top-24" onsubmit="return confirmAddToCart(event)">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <!-- Price -->
                    <div class="mb-6">
                        <div class="flex items-center gap-3">
                            <div class="text-3xl font-extrabold text-brand">{{ number_format($product->price, 2, ',', '.') }} TL</div>
                            @if($product->discount_percent > 0)
                                <span class="bg-red-600 text-white font-extrabold text-xs px-2.5 py-1 rounded-full shadow-sm">%{{ $product->discount_percent }} İNDİRİM</span>
                            @endif
                        </div>
                        @if($product->discount_percent > 0)
                            <div class="text-sm text-gray-400 line-through mt-1">{{ number_format($product->original_price, 2, ',', '.') }} TL</div>
                        @endif
                    </div>

                    <!-- Hidden Image Inputs (Ön Yüz & Arka Yüz Fotoğrafları) -->
                    <input type="file" id="customImageFrontInput" name="custom_image_front" accept="image/*" class="hidden" onchange="handleFrontImageUpload(event)">
                    <input type="file" id="customImageBackInput" name="custom_image_back" accept="image/*" class="hidden" onchange="handleBackImageUpload(event)">
                    <input type="file" id="customImageInput" name="custom_image" accept="image/*" class="hidden" onchange="handleImageUpload(event)">
                    <input type="hidden" name="custom_preview_base64" id="customPreviewBase64Input">

                    <!-- Stock Badge -->
                    <div class="mb-4">
                        @if($product->stock <= 0)
                            <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-extrabold px-3 py-1.5 rounded-full border border-red-200">
                                <i class="fa-solid fa-circle-xmark"></i> Stokta Yok
                            </span>
                        @elseif($product->stock <= 5)
                            <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 text-xs font-extrabold px-3 py-1.5 rounded-full border border-amber-200">
                                <i class="fa-solid fa-triangle-exclamation"></i> Son {{ $product->stock }} adet kaldı!
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full border border-green-200">
                                <i class="fa-solid fa-circle-check"></i> Stokta Mevcut
                            </span>
                        @endif
                    </div>

                    <!-- Hediye Paketi & Notu Seçeneği -->
                    <div class="mb-4 bg-amber-50/70 p-3.5 rounded-xl border border-amber-200/80">
                        <label class="flex items-center gap-2 text-xs font-bold text-amber-950 cursor-pointer select-none">
                            <input type="checkbox" name="is_gift" id="isGiftCheckbox" value="1" onchange="document.getElementById('giftNoteContainer').classList.toggle('hidden', !this.checked)" class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand">
                            <span class="flex items-center gap-1.5"><i class="fa-solid fa-gift text-brand text-sm"></i> Hediye Paketi İstiyorum</span>
                        </label>

                        <div id="giftNoteContainer" class="hidden mt-3 pt-2 border-t border-amber-200/60">
                            <label class="block text-[11px] font-extrabold text-amber-900 mb-1">🎁 Hediye Notunuz (Opsiyonel)</label>
                            <textarea name="gift_note" rows="2" maxlength="300" placeholder="Paketin içine eklenmesini istediğiniz özel notu yazınız..." class="w-full text-xs border border-amber-300 rounded-lg p-2 bg-white text-gray-800 outline-none focus:border-brand"></textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3 mb-6">
                        @if($product->stock <= 0)
                            <button type="button" disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3.5 px-4 rounded-lg text-base cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fa-solid fa-ban"></i>
                                Stokta Yok
                            </button>
                        @else
                            <button type="submit" class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3.5 px-4 rounded-lg transition text-base shadow-md flex items-center justify-center gap-2">
                                <i class="fa-solid fa-cart-shopping"></i>
                                Sepete Ekle
                            </button>
                        @endif
                        
                        <button type="button" onclick="toggleFavorite({{ $product->id }}, this)" class="w-full bg-rose-50 hover:bg-rose-100 border border-rose-200/80 text-rose-700 font-bold py-3 px-4 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-sm">
                            <i class="{{ $product->isFavoritedBy() ? 'fa-solid fa-heart text-red-500 text-lg drop-shadow-sm' : 'fa-regular fa-heart text-red-500 text-lg' }}"></i>
                            <span>{{ $product->isFavoritedBy() ? 'Favorilerinizde' : 'Favorilere Ekle' }}</span>
                        </button>
                    </div>

                    <!-- Delivery Info -->
                    <div class="flex items-center gap-3 text-[13px] text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <i class="fa-solid fa-truck text-brand text-lg"></i>
                        <div>
                            <div class="font-bold text-gray-800">Hızlı Kargo</div>
                            <div class="text-xs text-gray-500">Yarın kargoya verilir</div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <!-- Similar Products -->
        @if(isset($similarProducts) && $similarProducts->count() > 0)
        <div class="mt-16">
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-2">Benzer Ürünler</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach($similarProducts as $simProduct)
                <div class="bg-white border border-gray-200 rounded-xl p-3 hover:shadow-lg transition-all product-card flex flex-col h-full group relative">
                    <a href="{{ url('/urun/' . $simProduct->id) }}" class="block mb-3 relative overflow-hidden rounded-lg bg-gray-50 pt-[100%]">
                        <img src="{{ $simProduct->image ?: '/cerceve.png' }}" alt="{{ $simProduct->name }}" class="absolute inset-0 w-full h-full object-contain mix-blend-multiply p-2 transition-transform duration-500 group-hover:scale-105">
                    </a>
                    <div class="flex-grow flex flex-col">
                        <div class="text-xs text-brand font-semibold mb-1">{{ $simProduct->category->name ?? '' }}</div>
                        <h3 class="text-sm font-medium text-gray-800 mb-2 line-clamp-2 leading-tight group-hover:text-brand transition"><a href="{{ url('/urun/' . $simProduct->id) }}">{{ $simProduct->name }}</a></h3>
                        <div class="mt-auto pt-2 flex items-center justify-between">
                            <div class="text-lg font-extrabold text-brand">{{ number_format($simProduct->price, 2, ',', '.') }} TL</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recently Viewed Products -->
        @if(isset($recentlyViewed) && $recentlyViewed->count() > 0)
        <div class="mt-16">
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-2">Daha Önce Ziyaret Ettikleriniz</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach($recentlyViewed as $recentProduct)
                <div class="bg-white border border-gray-200 rounded-xl p-3 hover:shadow-lg transition-all product-card flex flex-col h-full group relative">
                    <a href="{{ url('/urun/' . $recentProduct->id) }}" class="block mb-3 relative overflow-hidden rounded-lg bg-gray-50 pt-[100%]">
                        <img src="{{ $recentProduct->image ?: '/cerceve.png' }}" alt="{{ $recentProduct->name }}" class="absolute inset-0 w-full h-full object-contain mix-blend-multiply p-2 transition-transform duration-500 group-hover:scale-105">
                    </a>
                    <div class="flex-grow flex flex-col">
                        <h3 class="text-sm font-medium text-gray-800 mb-2 line-clamp-2 leading-tight group-hover:text-brand transition"><a href="{{ url('/urun/' . $recentProduct->id) }}">{{ $recentProduct->name }}</a></h3>
                        <div class="mt-auto pt-2 flex items-center justify-between">
                            <div class="text-lg font-extrabold text-brand">{{ number_format($recentProduct->price, 2, ',', '.') }} TL</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

<!-- Fotoğraflar Yükleniyor Yükleme Animasyonu (Loading Overlay) -->
<div id="uploadLoadingOverlay" class="fixed inset-0 z-[100000] bg-black/80 backdrop-blur-md hidden flex-col items-center justify-center p-6 text-white text-center transition-all duration-300">
    <div class="bg-[#29221C] border border-[#C87A53]/50 p-8 rounded-3xl shadow-2xl flex flex-col items-center max-w-sm w-full relative">
        <div class="relative w-20 h-20 mb-5 flex items-center justify-center">
            <div class="absolute inset-0 rounded-full border-4 border-[#C87A53]/30 animate-ping"></div>
            <div class="w-16 h-16 rounded-full border-4 border-[#C87A53] border-t-transparent animate-spin"></div>
            <i class="fa-solid fa-cloud-arrow-up text-[#C87A53] text-2xl absolute"></i>
        </div>
        <h3 class="text-base font-extrabold text-white mb-1.5">Fotoğraflarınız Yükleniyor...</h3>
        <p class="text-xs text-gray-300 leading-relaxed">Yüksek kaliteli görselleriniz işlenip hazırlanıyor. Lütfen bekleyiniz...</p>
    </div>
</div>

<!-- 3D Customization Modal -->
<div id="customizationModal" class="fixed inset-0 z-[99999] bg-black/90 hidden flex-col items-center justify-center p-4 md:p-8 backdrop-blur-sm">
    <div class="bg-white w-full max-w-5xl rounded-2xl overflow-hidden flex flex-col h-[90vh] shadow-2xl relative">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-100 bg-gray-50">
            <div>
                <h3 class="text-lg md:text-xl font-bold text-gray-800" id="modalTitle">3D Çift Taraflı Kişiselleştirme & Ön İzleme</h3>
                <p class="text-xs md:text-sm text-gray-500" id="modalSubtitle">İç çerçevenin Ön Yüzü ve Arka Yüzü için ayrı fotoğraflar yükleyip 3D canlı model üzerinde inceleyebilirsiniz.</p>
            </div>
            <button type="button" onclick="closeCustomizationModal()" class="text-gray-400 hover:text-red-500 transition text-3xl leading-none">&times;</button>
        </div>
        
        <!-- Workspace (3D Canvas Container) -->
        <div class="flex-1 bg-stone-100 relative overflow-hidden flex items-center justify-center cursor-grab active:cursor-grabbing" id="workspaceContainer">
            <!-- Uploaded Photo Badges -->
            <div class="absolute top-4 left-4 z-30 flex flex-col gap-2">
                <div id="badgeFrontPhoto" class="hidden bg-orange-600 text-white font-extrabold text-xs px-3.5 py-2 rounded-xl shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> <span>Ön Yüz Fotoğrafı Yüklendi</span>
                </div>
                <div id="badgeBackPhoto" class="hidden bg-emerald-600 text-white font-extrabold text-xs px-3.5 py-2 rounded-xl shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> <span>Arka Yüz Fotoğrafı Yüklendi</span>
                </div>
            </div>

            <!-- Rotate 180 Floating Button -->
            <button type="button" onclick="rotateFrame180()" class="absolute bottom-4 right-4 z-30 bg-white/90 hover:bg-white text-gray-800 font-extrabold text-xs px-4 py-2.5 rounded-xl shadow-xl border border-gray-200 transition flex items-center gap-2 backdrop-blur-md">
                <i class="fa-solid fa-rotate text-[#C87A53] text-sm"></i>
                <span>180° Çerçeveyi Çevir (Ön / Arka Yüz)</span>
            </button>
        </div>
        
        <!-- Controls -->
        <div class="p-4 md:p-5 border-t border-gray-100 bg-white flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <button type="button" onclick="document.getElementById('customImageFrontInput').click()" class="bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold py-2.5 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2 text-xs w-full sm:w-auto">
                    <i class="fa-solid fa-camera text-sm"></i> 
                    <span id="btnFrontText">1. Ön Yüz Fotoğrafı Seç</span>
                </button>

                <button type="button" onclick="document.getElementById('customImageBackInput').click()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl transition shadow-sm flex items-center justify-center gap-2 text-xs w-full sm:w-auto">
                    <i class="fa-solid fa-camera text-sm"></i> 
                    <span id="btnBackText">2. Arka Yüz Fotoğrafı Seç</span>
                </button>
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                <button type="button" onclick="closeCustomizationModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 px-4 rounded-xl transition text-xs">
                    Kapat
                </button>
                <button type="button" onclick="submitCustomizedForm()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-xl transition shadow-md text-xs flex items-center justify-center gap-2 w-full sm:w-auto">
                    <i class="fa-solid fa-cart-shopping"></i> Onayla ve Sepete Ekle
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function changeMainImage(el, src) {
    document.getElementById('mainProductImage').src = src;
    document.getElementById('modalFrameImage').src = src;
    document.querySelectorAll('.thumb-box').forEach(box => {
        box.classList.remove('border-brand', 'border-2');
        box.classList.add('border-gray-200', 'border');
    });
    el.classList.remove('border-gray-200', 'border');
    el.classList.add('border-brand', 'border-2');
}

let currentStep = 1;
let activeElement = null; // maskBox or userImageBox

function openCustomizationModal() {
    const modal = document.getElementById('customizationModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    if (typeof initModal3D === 'function') {
        setTimeout(() => {
            initModal3D();
        }, 100);
    }
}

function closeCustomizationModal() {
    const modal = document.getElementById('customizationModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    if (typeof destroyModal3D === 'function') {
        destroyModal3D();
    }
}

@if($product->threeDTemplate)
let modalScene, modalCamera, modalRenderer, modalControls, modalAnimationId;
let modalModelGroup = new THREE.Group();
let modalOuterGroup, modalCustomRotatingFrame, modalCustomPhotoFront, modalCustomPhotoBack;

function initModal3D() {
    const container = document.getElementById('workspaceContainer');
    if (!container) return;

    // Clear previous canvas
    const oldCanvas = container.querySelector('canvas');
    if (oldCanvas) oldCanvas.remove();

    modalScene = new THREE.Scene();
    const rect = container.getBoundingClientRect();
    modalCamera = new THREE.PerspectiveCamera(40, rect.width / (rect.height || 480), 0.1, 1000);
    modalCamera.position.set(0, 0, 50);

    modalRenderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, preserveDrawingBuffer: true });
    modalRenderer.setSize(rect.width, rect.height || 480);
    modalRenderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    modalRenderer.shadowMap.enabled = true;
    container.appendChild(modalRenderer.domElement);

    modalControls = new THREE.OrbitControls(modalCamera, modalRenderer.domElement);
    modalControls.enableDamping = true;
    modalControls.dampingFactor = 0.05;
    modalControls.maxPolarAngle = Math.PI / 2 + 0.1;
    modalControls.minDistance = 15;
    modalControls.maxDistance = 75;

    // Clear old group children
    while(modalModelGroup.children.length > 0) {
        modalModelGroup.remove(modalModelGroup.children[0]);
    }
    modalScene.add(modalModelGroup);

    const ambient = new THREE.AmbientLight(0xffffff, 0.65);
    const keyLight = new THREE.DirectionalLight(0xffffff, 0.85);
    keyLight.position.set(25, 45, 30);
    modalScene.add(ambient, keyLight);

    // Build the frame inside modalModelGroup
    buildModalFrame();

    const animateModal = () => {
        modalAnimationId = requestAnimationFrame(animateModal);
        modalControls.update();
        modalRenderer.render(modalScene, modalCamera);
    };
    animateModal();
}

function destroyModal3D() {
    if (modalAnimationId) {
        cancelAnimationFrame(modalAnimationId);
        modalAnimationId = null;
    }
    if (modalRenderer) {
        modalRenderer.dispose();
        if (modalRenderer.domElement) {
            modalRenderer.domElement.remove();
        }
        modalRenderer = null;
    }
    modalScene = null;
    modalCamera = null;
    modalControls = null;
}

function buildModalFrame() {
    const width = {{ $product->threeDTemplate->width }};
    const height = {{ $product->threeDTemplate->height }};
    const depth = {{ $product->threeDTemplate->depth }};
    const thickness = {{ $product->threeDTemplate->thickness }};

    const innerW = {{ $product->threeDTemplate->inner_width }};
    const innerH = {{ $product->threeDTemplate->inner_height }};
    const innerD = {{ $product->threeDTemplate->inner_depth }};
    const innerB = {{ $product->threeDTemplate->inner_border }};

    const px = {{ $product->threeDTemplate->pos_x }};
    const py = {{ $product->threeDTemplate->pos_y }};

    const woodType = "{{ $product->threeDTemplate->wood_type }}";

    // Use modalRenderer for anisotropy
    const woodTexture = generateWoodTexture(woodType, modalRenderer);
    const bScale = {{ $product->threeDTemplate->bump_scale ?: 0.08 }};
    const materialObj = new THREE.MeshStandardMaterial({ 
        map: woodTexture, 
        bumpMap: woodTexture,
        bumpScale: bScale,
        roughness: 0.88,
        metalness: 0.0
    });

    const hasTop = {{ $product->threeDTemplate->has_top ? 'true' : 'false' }};
    const hasBottom = {{ $product->threeDTemplate->has_bottom ? 'true' : 'false' }};
    const hasLeft = {{ $product->threeDTemplate->has_left ? 'true' : 'false' }};
    const hasRight = {{ $product->threeDTemplate->has_right ? 'true' : 'false' }};

    modalOuterGroup = new THREE.Group();

    if(hasTop) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasLeft, hasRight), materialObj);
        mesh.position.y = height/2 - thickness/2;
        modalOuterGroup.add(mesh);
    }
    if(hasBottom) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasRight, hasLeft), materialObj);
        mesh.rotation.z = Math.PI;
        mesh.position.y = -height/2 + thickness/2;
        modalOuterGroup.add(mesh);
    }
    if(hasLeft) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasBottom, hasTop), materialObj);
        mesh.rotation.z = Math.PI / 2;
        mesh.position.x = -width/2 + thickness/2;
        modalOuterGroup.add(mesh);
    }
    if(hasRight) {
        const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasTop, hasBottom), materialObj);
        mesh.rotation.z = -Math.PI / 2;
        mesh.position.x = width/2 - thickness/2;
        modalOuterGroup.add(mesh);
    }

    modalModelGroup.add(modalOuterGroup);

    modalCustomRotatingFrame = new THREE.Group();
    modalCustomRotatingFrame.position.set(px, py, 0);

    const topIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), materialObj);
    topIn.position.y = innerH/2 - innerB/2;
    modalCustomRotatingFrame.add(topIn);

    const botIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), materialObj);
    botIn.rotation.z = Math.PI;
    botIn.position.y = -innerH/2 + innerB/2;
    modalCustomRotatingFrame.add(botIn);

    const leftIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), materialObj);
    leftIn.rotation.z = Math.PI / 2;
    leftIn.position.x = -innerW/2 + innerB/2;
    modalCustomRotatingFrame.add(leftIn);

    const rightIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), materialObj);
    rightIn.rotation.z = -Math.PI / 2;
    rightIn.position.x = innerW/2 - innerB/2;
    modalCustomRotatingFrame.add(rightIn);

    // Photo planes
    const photoW = innerW - innerB * 1.5;
    const photoH = innerH - innerB * 1.5;

    const photoMatFront = new THREE.MeshStandardMaterial({ 
        color: 0xefefef, 
        roughness: 0.35, 
        metalness: 0.1 
    });
    const photoMatBack = new THREE.MeshStandardMaterial({ 
        color: 0xefefef, 
        roughness: 0.35, 
        metalness: 0.1 
    });

    modalCustomPhotoFront = new THREE.Mesh(new THREE.PlaneGeometry(photoW, photoH), photoMatFront);
    modalCustomPhotoFront.position.z = 0.1;
    modalCustomRotatingFrame.add(modalCustomPhotoFront);

    modalCustomPhotoBack = new THREE.Mesh(new THREE.PlaneGeometry(photoW, photoH), photoMatBack);
    modalCustomPhotoBack.rotation.y = Math.PI;
    modalCustomPhotoBack.position.z = -0.1;
    modalCustomRotatingFrame.add(modalCustomPhotoBack);

    const backingGeom = new THREE.BoxGeometry(photoW, photoH, 0.08);
    const backing = new THREE.Mesh(backingGeom, materialObj);
    modalCustomRotatingFrame.add(backing);

    // Metallic Pivot Pins (Orta Dönme Pinleri)
    const pinMat = new THREE.MeshStandardMaterial({ color: 0xcccccc, metalness: 0.9, roughness: 0.2 });
    
    // Top Pin
    const innerEdgeTop = py + (innerH / 2);
    const outerTargetTop = (height / 2) - (thickness / 2);
    const lenTop = Math.max(0.15, outerTargetTop - innerEdgeTop);
    const localYTop = (innerH / 2) + (lenTop / 2);

    const pinTopGeo = new THREE.CylinderGeometry(0.18, 0.18, lenTop, 16);
    const pinTop = new THREE.Mesh(pinTopGeo, pinMat);
    pinTop.position.set(0, localYTop, 0);

    // Bottom Pin
    const innerEdgeBot = py - (innerH / 2);
    const outerTargetBot = -(height / 2) + (thickness / 2);
    const lenBot = Math.max(0.15, innerEdgeBot - outerTargetBot);
    const localYBot = -(innerH / 2) - (lenBot / 2);

    const pinBotGeo = new THREE.CylinderGeometry(0.18, 0.18, lenBot, 16);
    const pinBottom = new THREE.Mesh(pinBotGeo, pinMat);
    pinBottom.position.set(0, localYBot, 0);

    modalCustomRotatingFrame.add(pinTop, pinBottom);

    modalModelGroup.add(modalCustomRotatingFrame);

    // If an image is selected, load it onto the photo planes!
    const fileInput = document.getElementById('customImageInput');
    if (fileInput && fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const texture = new THREE.Texture(img);
                texture.needsUpdate = true;
                
                modalCustomPhotoFront.material.map = texture;
                modalCustomPhotoFront.material.color.set('#ffffff');
                modalCustomPhotoFront.material.needsUpdate = true;

                modalCustomPhotoBack.material.map = texture;
                modalCustomPhotoBack.material.color.set('#ffffff');
                modalCustomPhotoBack.material.needsUpdate = true;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
@endif

function resetBox(el) {
    if (!el) return;
    el.style.left = '50%';
    el.style.top = '50%';
    el.style.transform = 'translate(-50%, -50%)';
    el.style.width = '200px';
    el.style.height = '250px';
    el.dataset.rotation = '0';
}

function goToStep1() {
    currentStep = 1;
    if(document.getElementById('modalTitle')) document.getElementById('modalTitle').innerText = "Adım 1: Çerçeve Alanını Seçin";
    if(document.getElementById('modalSubtitle')) document.getElementById('modalSubtitle').innerText = "Beyaz kutuyu çerçevenin fotoğraf alanına sürükleyip boyutlandırın.";
    if(document.getElementById('modalHelpText')) document.getElementById('modalHelpText').innerHTML = "<i class='fa-solid fa-info-circle'></i> Mavi noktaları çekerek çerçeve içindeki alanı belirleyin.";
    
    const maskBox = document.getElementById('maskBox');
    if(maskBox) {
        maskBox.style.display = 'block';
        maskBox.classList.add('ring-2', 'ring-blue-500');
    }
    document.querySelectorAll('.mask-handle').forEach(h => h.style.display = 'block');
    
    const modalFrameImage = document.getElementById('modalFrameImage');
    if (modalFrameImage) modalFrameImage.style.display = 'block';

    const userImgBox = document.getElementById('userImageBox');
    if(userImgBox) userImgBox.style.display = 'none';
    
    document.getElementById('btnBack')?.classList.add('hidden');
    document.getElementById('btnNext')?.classList.remove('hidden');
    document.getElementById('btnFinish')?.classList.add('hidden');
    
    activeElement = maskBox;

    if (typeof destroyModal3D === 'function') {
        destroyModal3D();
    }
}

function rotateFrame180() {
    if (typeof modalCustomRotatingFrame !== 'undefined' && modalCustomRotatingFrame) {
        modalCustomRotatingFrame.rotation.y += Math.PI;
    }
}

// Strict Image File Security Validator
function isStrictValidImage(file) {
    if (!file) return false;
    const filename = file.name.toLowerCase();
    const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    // Extension check
    const ext = filename.split('.').pop();
    if (!allowedExtensions.includes(ext)) {
        showToast('Yalnızca JPG, JPEG, PNG, WEBP ve GIF formatında görseller yüklenebilir!', 'error');
        return false;
    }

    // Double extension & dangerous keyword checks (anti-virus/exploit protection)
    const dangerExtensions = ['.php', '.exe', '.zip', '.rar', '.sh', '.bat', '.py', '.js', '.html', '.htm', '.phtml', '.phps', '.jar'];
    for (let danger of dangerExtensions) {
        if (filename.includes(danger)) {
            showToast('Güvenlik nedeniyle şüpheli veya çift uzantılı dosyalar kabul edilmemektedir!', 'error');
            return false;
        }
    }

    // Mime type check
    if (!file.type.startsWith('image/')) {
        showToast('Seçilen dosya geçerli bir fotoğraf/görsel değil!', 'error');
        return false;
    }

    return true;
}

// Reset image inputs on page refresh so no stale data remains
window.addEventListener('load', function() {
    const frontInput = document.getElementById('customImageFrontInput');
    const backInput = document.getElementById('customImageBackInput');
    const singleInput = document.getElementById('customImageInput');
    const hiddenBase64 = document.getElementById('customPreviewBase64Input');

    if (frontInput) frontInput.value = '';
    if (backInput) backInput.value = '';
    if (singleInput) singleInput.value = '';
    if (hiddenBase64) hiddenBase64.value = '';
});

function handleFrontImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!isStrictValidImage(file)) {
        event.target.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const dataUrl = e.target.result;
        
        if (typeof modalCustomPhotoFront !== 'undefined' && modalCustomPhotoFront) {
            const img = new Image();
            img.onload = function() {
                const texture = new THREE.Texture(img);
                texture.needsUpdate = true;
                
                modalCustomPhotoFront.material.map = texture;
                modalCustomPhotoFront.material.color.set('#ffffff');
                modalCustomPhotoFront.material.needsUpdate = true;
            };
            img.src = dataUrl;
        }

        const badge = document.getElementById('badgeFrontPhoto');
        if (badge) badge.classList.remove('hidden');

        const btnText = document.getElementById('btnFrontText');
        if (btnText) btnText.innerText = "1. Ön Yüz Fotoğrafını Değiştir";
    };
    reader.readAsDataURL(file);
}

function handleBackImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!isStrictValidImage(file)) {
        event.target.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const dataUrl = e.target.result;
        
        if (typeof modalCustomPhotoBack !== 'undefined' && modalCustomPhotoBack) {
            const img = new Image();
            img.onload = function() {
                const texture = new THREE.Texture(img);
                texture.needsUpdate = true;
                
                modalCustomPhotoBack.material.map = texture;
                modalCustomPhotoBack.material.color.set('#ffffff');
                modalCustomPhotoBack.material.needsUpdate = true;
            };
            img.src = dataUrl;
        }

        const badge = document.getElementById('badgeBackPhoto');
        if (badge) badge.classList.remove('hidden');

        const btnText = document.getElementById('btnBackText');
        if (btnText) btnText.innerText = "2. Arka Yüz Fotoğrafını Değiştir (Opsiyonel)";
    };
    reader.readAsDataURL(file);
}

function handleImageUpload(event) {
    handleFrontImageUpload(event);
}

function capture3DSnapshot() {
    if (typeof modalRenderer !== 'undefined' && modalRenderer && modalScene && modalCamera) {
        try {
            modalRenderer.render(modalScene, modalCamera);
            const dataUrl = modalRenderer.domElement.toDataURL('image/png');
            const hiddenInput = document.getElementById('customPreviewBase64Input');
            if (hiddenInput) hiddenInput.value = dataUrl;
        } catch (e) {
            console.warn('3D snapshot capture error:', e);
        }
    }
}

function submitAddToCartAjax() {
    capture3DSnapshot();
    const frontInput = document.getElementById('customImageFrontInput');
    const singleInput = document.getElementById('customImageInput');
    
    // 1st Photo (Ön Yüz) is MANDATORY, 2nd & 3D preview are OPTIONAL
    const hasFrontPhoto = (frontInput && frontInput.files && frontInput.files.length > 0) ||
                          (singleInput && singleInput.files && singleInput.files.length > 0);

    if (!hasFrontPhoto) {
        showToast('Sipariş verebilmek için 1. Fotoğrafı (Ön Yüz) yüklemeniz zorunludur! (2. Fotoğraf opsiyoneldir)', 'error');
        openCustomizationModal();
        return false;
    }

    const overlay = document.getElementById('uploadLoadingOverlay');
    if (overlay) {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
    }

    const form = document.getElementById('addToCartForm');
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken || ''
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }

        if (data.status === 'success') {
            closeCustomizationModal();
            showToast(data.message || 'Kişiselleştirilmiş ürününüz sepete eklendi!', 'success');
            
            // Auto open Cart Drawer
            if (typeof openCartDrawer === 'function') {
                setTimeout(openCartDrawer, 300);
            }
        } else {
            showToast(data.message || 'Ürün sepete eklenirken bir hata oluştu.', 'error');
        }
    })
    .catch(err => {
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }
        console.error('Add to cart AJAX error:', err);
        showToast('Fotoğraflar yüklenirken sunucu hatası oluştu.', 'error');
    });
}

function submitCustomizedForm() {
    submitAddToCartAjax();
}

function confirmAddToCart(event) {
    if (event) event.preventDefault();
    submitAddToCartAjax();
    return false;
}

// Robust Drag, Resize & Rotate Engine
let isDragging = false;
let isResizing = false;
let isRotating = false;
let resizeDir = '';
let startX = 0, startY = 0;
let initialLeft = 0, initialTop = 0, initialWidth = 0, initialHeight = 0;
let currentRotation = 0;
let boxCenterX = 0, boxCenterY = 0;

const workspace = document.getElementById('workspaceContainer');

workspace.addEventListener('mousedown', startAction);
workspace.addEventListener('touchstart', startAction, {passive: false});

function startAction(e) {
    if (!activeElement) return;
    
    const target = e.target;
    const isRotate = target.classList.contains('rotate-handle');
    const isHandle = target.classList.contains('resize-handle');
    const isInsideActive = target === activeElement || activeElement.contains(target);
    
    if (!isRotate && !isHandle && !isInsideActive) return;
    
    e.preventDefault();
    
    const pointer = e.touches ? e.touches[0] : e;
    startX = pointer.clientX;
    startY = pointer.clientY;
    
    const rect = activeElement.getBoundingClientRect();
    boxCenterX = rect.left + rect.width / 2;
    boxCenterY = rect.top + rect.height / 2;
    
    currentRotation = parseFloat(activeElement.dataset.rotation || '0');

    if (isRotate) {
        isRotating = true;
    } else {
        const parentRect = workspace.getBoundingClientRect();
        
        if (activeElement.style.transform.includes('translate')) {
            initialLeft = rect.left - parentRect.left;
            initialTop = rect.top - parentRect.top;
            initialWidth = rect.width;
            initialHeight = rect.height;
            
            activeElement.style.transform = currentRotation ? `rotate(${currentRotation}deg)` : 'none';
            activeElement.style.left = initialLeft + 'px';
            activeElement.style.top = initialTop + 'px';
            activeElement.style.width = initialWidth + 'px';
            activeElement.style.height = initialHeight + 'px';
        } else {
            initialLeft = parseFloat(activeElement.style.left) || (rect.left - parentRect.left);
            initialTop = parseFloat(activeElement.style.top) || (rect.top - parentRect.top);
            initialWidth = activeElement.offsetWidth;
            initialHeight = activeElement.offsetHeight;
        }

        if (isHandle) {
            isResizing = true;
            resizeDir = target.getAttribute('data-dir');
        } else {
            isDragging = true;
        }
    }
    
    window.addEventListener('mousemove', handleMove, {passive: false});
    window.addEventListener('touchmove', handleMove, {passive: false});
    window.addEventListener('mouseup', handleEnd);
    window.addEventListener('touchend', handleEnd);
}

function handleMove(e) {
    if (!isDragging && !isResizing && !isRotating) return;
    e.preventDefault();
    
    const pointer = e.touches ? e.touches[0] : e;
    
    if (isRotating) {
        const radians = Math.atan2(pointer.clientY - boxCenterY, pointer.clientX - boxCenterX);
        let degrees = Math.round(radians * (180 / Math.PI) + 90);
        activeElement.dataset.rotation = degrees;
        activeElement.style.transform = `rotate(${degrees}deg)`;
    } else if (isDragging) {
        const dx = pointer.clientX - startX;
        const dy = pointer.clientY - startY;
        activeElement.style.left = (initialLeft + dx) + 'px';
        activeElement.style.top = (initialTop + dy) + 'px';
    } else if (isResizing) {
        const dx = pointer.clientX - startX;
        const dy = pointer.clientY - startY;
        if (resizeDir.includes('e')) {
            const newW = Math.max(30, initialWidth + dx);
            activeElement.style.width = newW + 'px';
        }
        if (resizeDir.includes('s')) {
            const newH = Math.max(30, initialHeight + dy);
            activeElement.style.height = newH + 'px';
        }
    }
}

function handleEnd() {
    isDragging = false;
    isResizing = false;
    isRotating = false;
    window.removeEventListener('mousemove', handleMove);
    window.removeEventListener('touchmove', handleMove);
    window.removeEventListener('mouseup', handleEnd);
    window.removeEventListener('touchend', handleEnd);
}

</script>

@if($product->threeDTemplate)
<script>
    function darkenColor(hex, percent) {
        if (!hex) return '#333333';
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
        if (hex.length !== 6) return '#333333';
        let num = parseInt(hex, 16);
        let amt = Math.round(2.55 * percent);
        let R = Math.max(0, (num >> 16) - amt);
        let G = Math.max(0, (num >> 8 & 0x00FF) - amt);
        let B = Math.max(0, (num & 0x0000FF) - amt);
        return '#' + (0x1000000 + R * 0x10000 + G * 0x100 + B).toString(16).slice(1);
    }

    // --- WOOD TEXTURE GENERATOR ---
    function generateWoodTexture(woodType, rendererInstance) {
        const canvas = document.createElement('canvas');
        canvas.width = 1024;
        canvas.height = 1024;
        const ctx = canvas.getContext('2d');

        let baseColor, lineColor, poreColor;
        if (woodType && (woodType.startsWith('#') || /^[0-9a-fA-F]{6}$/.test(woodType.replace('#','')))) {
            const hex = woodType.startsWith('#') ? woodType : '#' + woodType;
            baseColor = hex;
            lineColor = darkenColor(hex, 25);
            poreColor = darkenColor(hex, 45);
        } else if (woodType === 'Ceviz') {
            baseColor = '#4a3319';
            lineColor = '#2b1b0e';
            poreColor = '#1d1209';
        } else if (woodType === 'Meşe') {
            baseColor = '#a8896c';
            lineColor = '#72553b';
            poreColor = '#503a27';
        } else if (woodType === 'Çam') {
            baseColor = '#e3d3bd';
            lineColor = '#ba9e7d';
            poreColor = '#a68865';
        } else if (woodType === 'Kiraz') {
            baseColor = '#8c462b';
            lineColor = '#562512';
            poreColor = '#3c180a';
        } else {
            baseColor = woodType || '#ead9c3';
            lineColor = darkenColor(baseColor, 25);
            poreColor = darkenColor(baseColor, 45);
        }

        // Base color
        ctx.fillStyle = baseColor;
        ctx.fillRect(0, 0, 1024, 1024);

        // 1. Dark micro-pores (fibers)
        ctx.fillStyle = poreColor;
        ctx.globalAlpha = 0.25;
        for (let i = 0; i < 60000; i++) {
            let px = Math.random() * 1024;
            let py = Math.random() * 1024;
            let pw = 2 + Math.random() * 4;
            let ph = 1 + Math.random() * 1.2;
            ctx.fillRect(px, py, pw, ph);
        }

        // 2. Light wood fibers for realistic raw roughness
        ctx.fillStyle = '#ffffff';
        ctx.globalAlpha = 0.12;
        for (let i = 0; i < 25000; i++) {
            let px = Math.random() * 1024;
            let py = Math.random() * 1024;
            let pw = 3 + Math.random() * 6;
            let ph = 0.8 + Math.random() * 0.8;
            ctx.fillRect(px, py, pw, ph);
        }
        ctx.globalAlpha = 1.0;

        // Soft grain variation rings (horizontal waves)
        ctx.strokeStyle = poreColor;
        ctx.globalAlpha = 0.12;
        ctx.lineWidth = 16;
        for (let i = -200; i < 1224; i += 45) {
            ctx.beginPath();
            let y = i;
            ctx.moveTo(0, y);
            let freq = 0.003;
            let amp = 35;
            let phase = i * 0.05;
            for (let x = 0; x <= 1024; x += 30) {
                let offset = Math.sin(x * freq + phase) * amp;
                ctx.lineTo(x, y + offset);
            }
            ctx.stroke();
        }
        ctx.globalAlpha = 1.0;

        // Sharp horizontal grain lines
        ctx.strokeStyle = lineColor;
        ctx.lineWidth = 2.4;
        for (let i = -200; i < 1224; i += 12) {
            ctx.beginPath();
            let y = i;
            ctx.moveTo(0, y);
            
            let frequency = 0.004 + Math.random() * 0.003;
            let amplitude = 22 + Math.random() * 22;
            let phase = Math.random() * Math.PI;

            for (let x = 0; x <= 1024; x += 15) {
                let offset = Math.sin(x * frequency + phase) * amplitude;
                offset += (Math.random() - 0.5) * 1.5;
                ctx.lineTo(x, y + offset);
            }
            ctx.stroke();
        }

        // Horizontal knots (budaklar)
        ctx.lineWidth = 1.6;
        for (let k = 0; k < 3; k++) {
            let knotX = Math.random() * 624 + 200;
            let knotY = Math.random() * 624 + 200;
            let knotR = 30 + Math.random() * 40;
            
            for (let r = 8; r < knotR; r += 8) {
                ctx.beginPath();
                ctx.ellipse(knotX, knotY, r * 2.3, r, Math.PI / (6 + Math.random() * 4), 0, Math.PI * 2);
                ctx.stroke();
            }
        }

        const texture = new THREE.CanvasTexture(canvas);
        texture.wrapS = THREE.RepeatWrapping;
        texture.wrapT = THREE.RepeatWrapping;
        if (rendererInstance && rendererInstance.capabilities) {
            texture.anisotropy = rendererInstance.capabilities.getMaxAnisotropy();
        }
        return texture;
    }

    // --- 3D SHOWCASE ENGINE FOR PRODUCT PAGE ---
    let scene, camera, renderer, controls;
    let currentModelGroup = new THREE.Group();
    let outerGroup = null;
    let customRotatingFrame = null;
    let customPhotoFront = null;
    let customPhotoBack = null;

    const container3D = document.getElementById('productShowcase3D');

    if(container3D) {
        // Wait for page load and styling
        setTimeout(initProduct3D, 200);
    }

    function initProduct3D() {
        scene = new THREE.Scene();
        const rect = container3D.getBoundingClientRect();
        camera = new THREE.PerspectiveCamera(40, rect.width / (rect.height || 480), 0.1, 1000);
        camera.position.set(0, 0, 50);

        renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer.setSize(rect.width, rect.height || 480);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.shadowMap.enabled = true;
        container3D.appendChild(renderer.domElement);

        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.maxPolarAngle = Math.PI / 2 + 0.1;
        controls.minDistance = 15;
        controls.maxDistance = 75;

        scene.add(currentModelGroup);

        const ambient = new THREE.AmbientLight(0xffffff, 0.65);
        const keyLight = new THREE.DirectionalLight(0xffffff, 0.85);
        keyLight.position.set(25, 45, 30);
        keyLight.castShadow = true;
        scene.add(ambient, keyLight);

        buildProductFrame();

        const animate = () => {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        };
        animate();

        window.addEventListener('resize', () => {
            const r = container3D.getBoundingClientRect();
            camera.aspect = r.width / r.height;
            camera.updateProjectionMatrix();
            renderer.setSize(r.width, r.height);
        });

        // Spin decoration
        setTimeout(() => {
            if(customRotatingFrame) {
                let startRot = 0;
                const spin = () => {
                    if(startRot < Math.PI * 2) {
                        customRotatingFrame.rotation.y += 0.03;
                        startRot += 0.03;
                        requestAnimationFrame(spin);
                    }
                };
                spin();
            }
        }, 1200);
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

        const geom = new THREE.ExtrudeGeometry(shape, { depth: D, bevelEnabled: false, curveSegments: 1 });
        geom.computeBoundingBox();
        const zOffset = -0.5 * (geom.boundingBox.max.z - geom.boundingBox.min.z);
        geom.translate(0, 0, zOffset);
        return geom;
    }

    function buildProductFrame() {
        const width = {{ $product->threeDTemplate->width }};
        const height = {{ $product->threeDTemplate->height }};
        const depth = {{ $product->threeDTemplate->depth }};
        const thickness = {{ $product->threeDTemplate->thickness }};

        const innerW = {{ $product->threeDTemplate->inner_width }};
        const innerH = {{ $product->threeDTemplate->inner_height }};
        const innerD = {{ $product->threeDTemplate->inner_depth }};
        const innerB = {{ $product->threeDTemplate->inner_border }};

        const px = {{ $product->threeDTemplate->pos_x }};
        const py = {{ $product->threeDTemplate->pos_y }};

        const woodType = "{{ $product->threeDTemplate->wood_type }}";

        const woodTexture = generateWoodTexture(woodType, renderer);
        const bScale = {{ $product->threeDTemplate->bump_scale ?: 0.08 }};
        const materialObj = new THREE.MeshStandardMaterial({ 
            map: woodTexture, 
            bumpMap: woodTexture,
            bumpScale: bScale,
            roughness: 0.88,
            metalness: 0.0
        });

        const hasTop = {{ $product->threeDTemplate->has_top ? 'true' : 'false' }};
        const hasBottom = {{ $product->threeDTemplate->has_bottom ? 'true' : 'false' }};
        const hasLeft = {{ $product->threeDTemplate->has_left ? 'true' : 'false' }};
        const hasRight = {{ $product->threeDTemplate->has_right ? 'true' : 'false' }};

        outerGroup = new THREE.Group();

        if(hasTop) {
            const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasLeft, hasRight), materialObj);
            mesh.position.y = height/2 - thickness/2;
            outerGroup.add(mesh);
        }
        if(hasBottom) {
            const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasRight, hasLeft), materialObj);
            mesh.rotation.z = Math.PI;
            mesh.position.y = -height/2 + thickness/2;
            outerGroup.add(mesh);
        }
        if(hasLeft) {
            const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasBottom, hasTop), materialObj);
            mesh.rotation.z = Math.PI / 2;
            mesh.position.x = -width/2 + thickness/2;
            outerGroup.add(mesh);
        }
        if(hasRight) {
            const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasTop, hasBottom), materialObj);
            mesh.rotation.z = -Math.PI / 2;
            mesh.position.x = width/2 - thickness/2;
            outerGroup.add(mesh);
        }

        currentModelGroup.add(outerGroup);

        customRotatingFrame = new THREE.Group();
        customRotatingFrame.position.set(px, py, 0);

        const topIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), materialObj);
        topIn.position.y = innerH/2 - innerB/2;
        customRotatingFrame.add(topIn);

        const botIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), materialObj);
        botIn.rotation.z = Math.PI;
        botIn.position.y = -innerH/2 + innerB/2;
        customRotatingFrame.add(botIn);

        const leftIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), materialObj);
        leftIn.rotation.z = Math.PI / 2;
        leftIn.position.x = -innerW/2 + innerB/2;
        customRotatingFrame.add(leftIn);

        const rightIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), materialObj);
        rightIn.rotation.z = -Math.PI / 2;
        rightIn.position.x = innerW/2 - innerB/2;
        customRotatingFrame.add(rightIn);

        // Photo planes
        const photoW = innerW - innerB * 1.5;
        const photoH = innerH - innerB * 1.5;

        const photoMatFront = new THREE.MeshStandardMaterial({ 
            color: 0xefefef, 
            roughness: 0.35, 
            metalness: 0.1 
        });
        const photoMatBack = new THREE.MeshStandardMaterial({ 
            color: 0xefefef, 
            roughness: 0.35, 
            metalness: 0.1 
        });

        customPhotoFront = new THREE.Mesh(new THREE.PlaneGeometry(photoW, photoH), photoMatFront);
        customPhotoFront.position.z = 0.1;
        customRotatingFrame.add(customPhotoFront);

        customPhotoBack = new THREE.Mesh(new THREE.PlaneGeometry(photoW, photoH), photoMatBack);
        customPhotoBack.rotation.y = Math.PI;
        customPhotoBack.position.z = -0.1;
        customRotatingFrame.add(customPhotoBack);

        const backingGeom = new THREE.BoxGeometry(photoW, photoH, 0.08);
        const backing = new THREE.Mesh(backingGeom, materialObj);
        customRotatingFrame.add(backing);

        // Metallic Pivot Pins (Orta Dönme Pinleri)
        const pinMat = new THREE.MeshStandardMaterial({ color: 0xcccccc, metalness: 0.9, roughness: 0.2 });
        
        // Top Pin
        const innerEdgeTop = py + (innerH / 2);
        const outerTargetTop = (height / 2) - (thickness / 2);
        const lenTop = Math.max(0.15, outerTargetTop - innerEdgeTop);
        const localYTop = (innerH / 2) + (lenTop / 2);

        const pinTopGeo = new THREE.CylinderGeometry(0.18, 0.18, lenTop, 16);
        const pinTop = new THREE.Mesh(pinTopGeo, pinMat);
        pinTop.position.set(0, localYTop, 0);

        // Bottom Pin
        const innerEdgeBot = py - (innerH / 2);
        const outerTargetBot = -(height / 2) + (thickness / 2);
        const lenBot = Math.max(0.15, innerEdgeBot - outerTargetBot);
        const localYBot = -(innerH / 2) - (lenBot / 2);

        const pinBotGeo = new THREE.CylinderGeometry(0.18, 0.18, lenBot, 16);
        const pinBottom = new THREE.Mesh(pinBotGeo, pinMat);
        pinBottom.position.set(0, localYBot, 0);

        customRotatingFrame.add(pinTop, pinBottom);

        currentModelGroup.add(customRotatingFrame);
    }

    // Connect image upload handle to load image directly onto 3D model
    const originalHandleUpload = window.handleImageUpload;
    window.handleImageUpload = function(event) {
        if(originalHandleUpload) {
            originalHandleUpload(event);
        }
        
        const file = event.target.files[0];
        if (file && customPhotoFront && customPhotoBack) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const texture = new THREE.Texture(img);
                    texture.needsUpdate = true;
                    customPhotoFront.material.map = texture;
                    customPhotoFront.material.color.set('#ffffff');
                    customPhotoFront.material.needsUpdate = true;

                    customPhotoBack.material.map = texture;
                    customPhotoBack.material.color.set('#ffffff');
                    customPhotoBack.material.needsUpdate = true;
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    };
</script>
@endif

<!-- YouTube Video Modal -->
<div id="youtubeVideoModal" class="fixed inset-0 z-[999999] bg-black/90 hidden items-center justify-center p-4 backdrop-blur-md" onclick="closeYoutubeModal(event)">
    <div class="relative w-full max-w-4xl aspect-video bg-black rounded-2xl overflow-hidden shadow-2xl border border-gray-800" onclick="event.stopPropagation()">
        <button type="button" onclick="closeYoutubeModal()" class="absolute top-4 right-4 text-white text-3xl font-bold z-20 hover:text-red-500 transition leading-none bg-black/50 w-10 h-10 rounded-full flex items-center justify-center">&times;</button>
        <iframe id="youtubeIframe" src="" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>

<script>
function openYoutubeModal(embedUrl) {
    const iframe = document.getElementById('youtubeIframe');
    if (iframe) iframe.src = embedUrl + '?autoplay=1';
    const modal = document.getElementById('youtubeVideoModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeYoutubeModal(e) {
    const modal = document.getElementById('youtubeVideoModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    const iframe = document.getElementById('youtubeIframe');
    if (iframe) iframe.src = '';
}
</script>
@endsection
