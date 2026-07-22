@extends('layouts.app')

@section('title', ($product->name ?? 'Ürün Detayı') . ' - AhşapEvim')

@section('content')
<div class="bg-white pb-12">
    <div class="container mx-auto px-4 py-4">
        <!-- Breadcrumb -->
        <nav class="flex text-[13px] text-gray-500 mb-6">
            <a href="/" class="hover:underline">Anasayfa</a>
            <span class="mx-2">></span>
            <a href="/products?category={{ $product->category->slug ?? '' }}" class="hover:underline">{{ $product->category->name ?? 'Kategori' }}</a>
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
                    @if(isset($product->features['images']) && is_array($product->features['images']))
                        @foreach($product->features['images'] as $addImg)
                            <div class="thumb-box w-16 h-20 border border-gray-200 rounded-md cursor-pointer overflow-hidden p-1 bg-white hover:border-gray-400 transition" onclick="changeMainImage(this, '{{ $addImg }}')">
                                <img src="{{ $addImg }}" class="w-full h-full object-contain" alt="thumbnail">
                            </div>
                        @endforeach
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
                
                <!-- Rating -->
                <div class="flex items-center gap-3 text-[13px] mb-4">
                    <div class="flex items-center gap-1">
                        <span class="font-bold text-gray-700">4.8</span>
                        <div class="flex text-yellow-400 text-xs">
                            <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                        </div>
                    </div>
                    <a href="#" class="text-blue-600 hover:underline">15 Değerlendirme</a>
                </div>

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
                    
                    <button type="button" onclick="openCustomizationModal()" class="bg-blue-50 border-2 border-blue-100 hover:bg-blue-100 text-brand font-bold py-4 px-6 rounded-xl transition-colors flex items-center justify-center gap-3 w-full md:w-auto shadow-sm">
                        <i class="fa-solid fa-wand-magic-sparkles text-xl"></i>
                        Kişiselleştir ve Ön İzle
                    </button>
                </div>
            </div>

            <!-- Right Side: Buybox -->
            <div class="w-full lg:w-[25%]">
                <form id="addToCartForm" action="/cart/add" method="POST" enctype="multipart/form-data" class="border border-gray-200 rounded-xl p-5 bg-white shadow-sm sticky top-24" onsubmit="return checkCustomization(event)">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <!-- Price -->
                    <div class="mb-6">
                        <div class="text-3xl font-extrabold text-brand">{{ number_format($product->price, 2, ',', '.') }} TL</div>
                        @if($product->original_price > $product->price)
                            <div class="text-sm text-gray-400 line-through mt-1">{{ number_format($product->original_price, 2, ',', '.') }} TL</div>
                        @endif
                    </div>

                    <!-- Hidden Image Input -->
                    <input type="file" id="customImageInput" name="custom_image" accept="image/*" class="hidden" onchange="handleImageUpload(event)">

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3 mb-6">
                        <button type="submit" class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3.5 px-4 rounded-lg transition text-base shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-cart-shopping"></i>
                            Sepete Ekle
                        </button>
                        
                        <button type="button" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-lg transition text-sm flex items-center justify-center gap-2">
                            <i class="fa-regular fa-heart text-red-500 text-base"></i>
                            Favorilere Ekle
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
                    <a href="/product/{{ $simProduct->id }}" class="block mb-3 relative overflow-hidden rounded-lg bg-gray-50 pt-[100%]">
                        <img src="{{ $simProduct->image ?: '/cerceve.png' }}" alt="{{ $simProduct->name }}" class="absolute inset-0 w-full h-full object-contain mix-blend-multiply p-2 transition-transform duration-500 group-hover:scale-105">
                    </a>
                    <div class="flex-grow flex flex-col">
                        <div class="text-xs text-brand font-semibold mb-1">{{ $simProduct->category->name ?? '' }}</div>
                        <h3 class="text-sm font-medium text-gray-800 mb-2 line-clamp-2 leading-tight group-hover:text-brand transition"><a href="/product/{{ $simProduct->id }}">{{ $simProduct->name }}</a></h3>
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
                    <a href="/product/{{ $recentProduct->id }}" class="block mb-3 relative overflow-hidden rounded-lg bg-gray-50 pt-[100%]">
                        <img src="{{ $recentProduct->image ?: '/cerceve.png' }}" alt="{{ $recentProduct->name }}" class="absolute inset-0 w-full h-full object-contain mix-blend-multiply p-2 transition-transform duration-500 group-hover:scale-105">
                    </a>
                    <div class="flex-grow flex flex-col">
                        <h3 class="text-sm font-medium text-gray-800 mb-2 line-clamp-2 leading-tight group-hover:text-brand transition"><a href="/product/{{ $recentProduct->id }}">{{ $recentProduct->name }}</a></h3>
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

<!-- 2-Step Customization Modal -->
<div id="customizationModal" class="fixed inset-0 z-[99999] bg-black/90 hidden flex-col items-center justify-center p-4 md:p-8 backdrop-blur-sm">
    <div class="bg-white w-full max-w-4xl rounded-2xl overflow-hidden flex flex-col h-[90vh] shadow-2xl">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50">
            <div>
                <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Adım 1: Çerçeve Alanını Seçin</h3>
                <p class="text-sm text-gray-500" id="modalSubtitle">Beyaz kutuyu çerçevenin fotoğraf alanına sürükleyip boyutlandırın.</p>
            </div>
            <button type="button" onclick="closeCustomizationModal()" class="text-gray-400 hover:text-red-500 transition text-3xl leading-none">&times;</button>
        </div>
        
        <!-- Workspace -->
        <div class="flex-1 bg-gray-200 relative overflow-hidden flex items-center justify-center touch-none" id="workspaceContainer">
            
            <!-- Frame Image (Background) -->
            <img id="modalFrameImage" src="{{ $product->image ?: '/cerceve.png' }}" class="absolute max-w-full max-h-full object-contain pointer-events-none z-10 mix-blend-multiply opacity-90">
            
            <!-- STEP 1: Mask Box (White Area) -->
            <div id="maskBox" class="absolute z-20 cursor-move shadow-lg ring-2 ring-blue-500 bg-white" style="width: 200px; height: 250px; top: 50%; left: 50%; transform: translate(-50%, -50%);" data-rotation="0">
                <!-- Rotate Handle -->
                <div class="rotate-handle mask-handle w-7 h-7 bg-green-600 hover:bg-green-700 text-white rounded-full absolute -top-9 left-1/2 -translate-x-1/2 cursor-grab active:cursor-grabbing flex items-center justify-center text-xs shadow-lg z-30" title="Açıyı Döndür">
                    <i class="fa-solid fa-rotate-right pointer-events-none"></i>
                </div>
                <!-- Resize Handles for Mask -->
                <div class="resize-handle mask-handle w-6 h-6 bg-blue-500 absolute -right-3 top-1/2 -translate-y-1/2 cursor-e-resize rounded-full shadow z-30" data-dir="e"></div>
                <div class="resize-handle mask-handle w-6 h-6 bg-blue-500 absolute -bottom-3 left-1/2 -translate-x-1/2 cursor-s-resize rounded-full shadow z-30" data-dir="s"></div>
                <div class="resize-handle mask-handle w-6 h-6 bg-blue-500 absolute -right-3 -bottom-3 cursor-se-resize rounded-full shadow z-30" data-dir="se"></div>
            </div>
            
            <!-- STEP 2: User Image (Covers old photo with solid opacity & corner vignette) -->
            <div id="userImageBox" class="absolute z-20 hidden overflow-hidden pointer-events-none bg-white shadow-md" style="width: 200px; height: 250px; top: 50%; left: 50%; transform: translate(-50%, -50%);" data-rotation="0">
                <img id="modalUserImage" src="" class="w-full h-full object-fill opacity-[0.96]" style="image-rendering: -webkit-optimize-contrast; image-rendering: high-quality; transform: translateZ(0);">
                <!-- Corner Darkening / Vignette Shadow for Realistic Frame Effect -->
                <div class="absolute inset-0 pointer-events-none shadow-[inset_0_0_20px_rgba(0,0,0,0.4)] ring-1 ring-black/10"></div>
            </div>

        </div>
        
        <!-- Controls -->
        <div class="p-5 border-t border-gray-100 bg-white flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button type="button" id="btnBack" onclick="goToStep1()" class="hidden bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2.5 px-5 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Geri (Alanı Değiştir)
                </button>
                <button type="button" onclick="document.getElementById('customImageInput').click()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2.5 px-5 rounded-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-camera"></i> <span id="uploadTextModal">Fotoğraf Seç/Değiştir</span>
                </button>
            </div>
            
            <div class="text-sm text-gray-500 hidden md:block" id="modalHelpText">
                <i class="fa-solid fa-info-circle"></i> Beyaz alanı sürükleyip kenarlarından çekerek çerçevenin içine oturtun.
            </div>
            
            <button type="button" id="btnNext" onclick="goToStep2()" class="bg-brand hover:bg-brand-dark text-white font-bold py-2.5 px-8 rounded-lg transition shadow-md w-full sm:w-auto flex items-center justify-center gap-2">
                İleri <i class="fa-solid fa-arrow-right"></i>
            </button>
            
            <button type="button" id="btnFinish" onclick="saveCustomization()" class="hidden bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-8 rounded-lg transition shadow-md w-full sm:w-auto flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i> Onayla ve Kapat
            </button>
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
    
    // Reset positions after modal is rendered
    setTimeout(() => {
        resetBox(document.getElementById('maskBox'));
        resetBox(document.getElementById('userImageBox'));
        goToStep1();
    }, 50);
}

function closeCustomizationModal() {
    const modal = document.getElementById('customizationModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

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
    
    const userImgBox = document.getElementById('userImageBox');
    if(userImgBox) userImgBox.style.display = 'none';
    
    document.getElementById('btnBack')?.classList.add('hidden');
    document.getElementById('btnNext')?.classList.remove('hidden');
    document.getElementById('btnFinish')?.classList.add('hidden');
    
    activeElement = maskBox;
}

function goToStep2() {
    if (!document.getElementById('customImageInput').files.length) {
        document.getElementById('customImageInput').click();
        return;
    }
    showStep2UI();
}

function showStep2UI() {
    currentStep = 2;
    if(document.getElementById('modalTitle')) document.getElementById('modalTitle').innerText = "Adım 2: Çerçeve Önizlemesi";
    if(document.getElementById('modalSubtitle')) document.getElementById('modalSubtitle').innerText = "Fotoğrafınız belirlediğiniz alana doğrudan yerleştirildi.";
    if(document.getElementById('modalHelpText')) document.getElementById('modalHelpText').innerHTML = "<i class='fa-solid fa-info-circle'></i> Alanı veya açıyı değiştirmek isterseniz Geri butonunu kullanabilirsiniz.";
    
    const maskBox = document.getElementById('maskBox');
    if(maskBox) {
        maskBox.style.display = 'none';
    }
    
    const userImgBox = document.getElementById('userImageBox');
    if(userImgBox) {
        userImgBox.style.display = 'block';
    }
    
    document.getElementById('btnBack')?.classList.remove('hidden');
    document.getElementById('btnNext')?.classList.add('hidden');
    document.getElementById('btnFinish')?.classList.remove('hidden');
    
    activeElement = null; // Lock user image, no dragging or extra handles in Step 2
}

function handleImageUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('modalUserImage').src = e.target.result;
        
        // Directly sync userImageBox position, dimensions AND rotation to maskBox
        const mask = document.getElementById('maskBox');
        const userImg = document.getElementById('userImageBox');
        
        userImg.style.width = mask.style.width;
        userImg.style.height = mask.style.height;
        userImg.style.left = mask.style.left;
        userImg.style.top = mask.style.top;
        userImg.style.transform = mask.style.transform;
        userImg.dataset.rotation = mask.dataset.rotation || '0';
        
        showStep2UI();
    }
    reader.readAsDataURL(file);
}

function saveCustomization() {
    closeCustomizationModal();
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
function checkCustomization(e) {
    if (!document.getElementById('customImageInput').files.length) {
        e.preventDefault();
        
        // Show a custom toast/alert (you can use standard alert or the toast setup)
        alert('Lütfen sepete eklemeden önce "Kişiselleştir ve Ön İzle" butonuna tıklayarak ürününüzü kişiselleştirin.');
        
        openCustomizationModal();
        return false;
    }
    return true;
}
</script>
@endsection
