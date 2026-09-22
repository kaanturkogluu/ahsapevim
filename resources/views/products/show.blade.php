@extends('layouts.app')

@section('title', ($product->name ?? 'Ürün Detayı') . ' — Ahşap Evim Manisa')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?: ($product->name . ' - Kişiye özel el işçiliği masif ahşap çerçeve.')), 155))
@section('meta_image', $product->image ? (str_starts_with($product->image, 'http') ? $product->image : url($product->image)) : 'https://ahsapevimmanisa.com/ahsaplogo_org.png')

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

        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
            
            <!-- Left Side: Images -->
            <div class="w-full lg:w-[52%] flex flex-col md:flex-row gap-3 md:gap-4">
                <!-- Desktop Thumbnails (Sol Taraf Dikey) -->
                <div class="hidden md:flex flex-col gap-2.5 w-20 md:w-22 flex-shrink-0">
                    <div class="thumb-box w-20 md:w-22 h-24 md:h-28 border-2 border-brand rounded-xl cursor-pointer overflow-hidden p-1 bg-white shadow-xs transition hover:scale-105" onclick="changeMainImage(this, '{{ $product->image ?: '/cerceve.png' }}')">
                        <img src="{{ $product->image ?: '/cerceve.png' }}" class="w-full h-full object-contain" alt="thumbnail" loading="lazy" decoding="async">
                    </div>
                    @if(count($product->gallery_urls) > 0)
                        @foreach($product->gallery_urls as $addImg)
                            <div class="thumb-box w-20 md:w-22 h-24 md:h-28 border border-gray-200 rounded-xl cursor-pointer overflow-hidden p-1 bg-white hover:border-brand transition hover:scale-105" onclick="changeMainImage(this, '{{ $addImg }}')">
                                <img src="{{ $addImg }}" class="w-full h-full object-contain" alt="thumbnail" loading="lazy" decoding="async">
                            </div>
                        @endforeach
                    @endif
                    @if($product->youtube_id)
                        <div class="thumb-box w-20 md:w-22 h-24 md:h-28 border-2 border-red-400 rounded-xl cursor-pointer overflow-hidden relative bg-black group shadow-xs hover:border-red-600 transition" onclick="openYoutubeModal('https://www.youtube.com/embed/{{ $product->youtube_id }}')">
                            <img src="https://img.youtube.com/vi/{{ $product->youtube_id }}/hqdefault.jpg" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition" alt="video thumbnail" loading="lazy" decoding="async">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/40 group-hover:bg-black/20 transition">
                                <i class="fa-brands fa-youtube text-red-600 text-2xl drop-shadow-md"></i>
                            </div>
                        </div>
                    @endif
                    @if($product->instagram_code)
                        <div class="thumb-box w-20 md:w-22 h-24 md:h-28 border-2 border-pink-400 rounded-xl cursor-pointer overflow-hidden relative bg-gradient-to-tr from-amber-500 via-rose-500 to-purple-600 group shadow-xs hover:border-pink-600 transition" onclick="openInstagramModal('{{ $product->instagram_embed_url }}')">
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/30 group-hover:bg-black/10 transition">
                                <i class="fa-brands fa-instagram text-white text-2xl drop-shadow-md"></i>
                                <span class="text-[9px] font-extrabold text-white mt-1 uppercase tracking-tighter">REEL</span>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Main Image Container -->
                <div class="flex-1 flex flex-col w-full">
                    <div id="mainImageContainer" class="-mx-4 w-[calc(100%+2rem)] sm:mx-0 sm:w-full bg-white sm:bg-gray-50/80 border-0 sm:border sm:border-gray-200/80 rounded-none sm:rounded-2xl relative overflow-hidden flex items-center justify-center h-[460px] xs:h-[500px] sm:h-[540px] md:h-[580px] lg:h-[620px]">
                        <img id="mainProductImage" src="{{ $product->image ?: '/cerceve.png' }}" alt="{{ $product->name }}" class="w-full h-full object-contain mix-blend-multiply p-0 sm:p-2 md:p-3 transition-all duration-300 z-10" loading="eager" decoding="async" fetchpriority="high">
                    </div>

                    <!-- Mobile Thumbnail Gallery -->
                    <div class="flex md:hidden items-center gap-2.5 overflow-x-auto -mx-4 px-4 pt-3 pb-1 max-w-[calc(100%+2rem)] text-center scrollbar-none">
                        <div class="thumb-box w-16 h-20 border-2 border-brand rounded-xl cursor-pointer overflow-hidden p-0.5 bg-white shrink-0 shadow-xs transition" onclick="changeMainImage(this, '{{ $product->image ?: '/cerceve.png' }}')">
                            <img src="{{ $product->image ?: '/cerceve.png' }}" class="w-full h-full object-contain" alt="thumbnail" loading="lazy" decoding="async">
                        </div>
                        @if(count($product->gallery_urls) > 0)
                            @foreach($product->gallery_urls as $addImg)
                                <div class="thumb-box w-16 h-20 border border-gray-200 rounded-xl cursor-pointer overflow-hidden p-0.5 bg-white hover:border-brand shrink-0 shadow-xs transition" onclick="changeMainImage(this, '{{ $addImg }}')">
                                    <img src="{{ $addImg }}" class="w-full h-full object-contain" alt="thumbnail" loading="lazy" decoding="async">
                                </div>
                            @endforeach
                        @endif
                        @if($product->youtube_id)
                            <div class="thumb-box w-16 h-20 border-2 border-red-400 rounded-xl cursor-pointer overflow-hidden relative bg-black shrink-0 shadow-xs hover:border-red-600 transition" onclick="openYoutubeModal('https://www.youtube.com/embed/{{ $product->youtube_id }}')">
                                <img src="https://img.youtube.com/vi/{{ $product->youtube_id }}/hqdefault.jpg" class="w-full h-full object-cover opacity-80" alt="video thumbnail" loading="lazy" decoding="async">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                                    <i class="fa-brands fa-youtube text-red-600 text-xl"></i>
                                </div>
                            </div>
                        @endif
                        @if($product->instagram_code)
                            <div class="thumb-box w-16 h-20 border-2 border-pink-400 rounded-xl cursor-pointer overflow-hidden relative bg-gradient-to-tr from-amber-500 via-rose-500 to-purple-600 shrink-0 shadow-xs hover:border-pink-600 transition" onclick="openInstagramModal('{{ $product->instagram_embed_url }}')">
                                <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/30">
                                    <i class="fa-brands fa-instagram text-white text-xl"></i>
                                    <span class="text-[8px] font-extrabold text-white mt-0.5 uppercase tracking-tighter">REEL</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Temsili Görsel Uyarısı -->
                    <div class="mt-3.5 p-3.5 bg-amber-50/90 border border-amber-200/90 rounded-xl text-amber-950 text-xs flex items-start gap-2.5 shadow-2xs">
                        <i class="fa-solid fa-circle-info text-amber-600 text-base shrink-0 mt-0.5"></i>
                        <div class="leading-relaxed">
                            <strong class="font-black text-amber-900 block mb-0.5">📌 Bilgilendirme: Ürün Görselleri Temsilidir</strong>
                            Gönderilecek ahşap çerçevede buradaki örnek fotoğraflar değil, <strong>sipariş verirken yükleyeceğiniz kendi özel fotoğrafınız</strong> yer alacaktır.
                        </div>
                    </div>
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
                </div>
            </div>

            <!-- Right Side: Buybox -->
            <div class="w-full lg:w-[28%]">
                <form id="addToCartForm" action="{{ url('/sepet/ekle') }}" method="POST" enctype="multipart/form-data" class="border border-gray-200 rounded-2xl p-5 bg-white shadow-sm sticky top-24 space-y-4" onsubmit="return confirmAddToCart(event)">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <!-- Price -->
                    <div>
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

                    <!-- FOTOĞRAF YÜKLEME ALANI (ZORUNLU) -->
                    <div class="bg-blue-50/70 border-2 border-dashed border-blue-300 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-black text-blue-950 uppercase tracking-wide flex items-center gap-1.5">
                                <i class="fa-solid fa-camera text-brand text-sm"></i>
                                <span>Fotoğrafınızı Yükleyin *</span>
                            </label>
                            <span class="text-[10px] font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded-full uppercase">Zorunlu</span>
                        </div>

                        <p class="text-[11px] text-gray-600 leading-snug">
                            Ahşap çerçevenize basılmasını istediğiniz fotoğrafı(ları) aşağıdan seçiniz. <i>(Ürün görselindeki fotoğraf temsilidir)</i>
                        </p>

                        <!-- Hidden File Inputs -->
                        <input type="file" id="customImageFrontInput" name="custom_image_front" accept="image/*" class="hidden" onchange="handleFrontImageUpload(event)">
                        <input type="file" id="customImageBackInput" name="custom_image_back" accept="image/*" class="hidden" onchange="handleBackImageUpload(event)">
                        <input type="file" id="customImageInput" name="custom_image" accept="image/*" class="hidden" onchange="handleFrontImageUpload(event)">

                        <!-- Visible Photo Upload Buttons & Previews -->
                        <div class="space-y-2">
                            <!-- 1. Fotoğraf / Ön Yüz -->
                            <div onclick="document.getElementById('customImageFrontInput').click();" 
                                 class="flex items-center justify-between p-3 bg-white rounded-xl border border-blue-200 hover:border-brand cursor-pointer transition shadow-2xs group">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                                        <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-gray-800" id="frontPhotoTitle">1. Fotoğraf (Ön Yüz) *</div>
                                        <div class="text-[10px] text-gray-400" id="frontPhotoStatus">Dosya seçilmedi</div>
                                    </div>
                                </div>
                                <div id="frontPhotoPreview" class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden hidden border border-gray-200">
                                    <img src="" class="w-full h-full object-cover">
                                </div>
                                <span class="text-xs font-bold text-brand bg-brand/10 px-2.5 py-1 rounded-lg group-hover:bg-brand group-hover:text-white transition">Seç</span>
                            </div>

                            <!-- 2. Fotoğraf / Arka Yüz (Çift taraflı çerçeveler için) -->
                            <div onclick="document.getElementById('customImageBackInput').click();" 
                                 class="flex items-center justify-between p-3 bg-white rounded-xl border border-dashed border-gray-300 hover:border-emerald-500 cursor-pointer transition shadow-2xs group">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                                        <i class="fa-solid fa-images text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-gray-800 flex items-center gap-1">
                                            <span>2. Fotoğraf (Arka Yüz)</span>
                                            <span class="text-[10px] text-gray-400 font-normal">(Opsiyonel)</span>
                                        </div>
                                        <div class="text-[10px] text-gray-400" id="backPhotoStatus">Çift taraflı çerçeveler için</div>
                                    </div>
                                </div>
                                <div id="backPhotoPreview" class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden hidden border border-gray-200">
                                    <img src="" class="w-full h-full object-cover">
                                </div>
                                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition">Seç</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hediye Paketi & Hediye Notu Alanı -->
                    <div class="bg-amber-50/60 border border-amber-200/80 rounded-2xl p-3.5 space-y-2.5 transition">
                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                            <input type="checkbox" name="is_gift" id="isGiftCheckbox" value="1" onchange="toggleGiftNote(this)" class="rounded text-brand focus:ring-brand w-4 h-4">
                            <span class="text-xs font-bold text-amber-950 flex items-center gap-1.5">
                                <i class="fa-solid fa-gift text-brand"></i>
                                <span>Hediye Paketi Yapılsın</span>
                            </span>
                        </label>
                        
                        <div id="giftNoteContainer" class="hidden pt-1.5">
                            <label class="block text-[11px] font-bold text-amber-900 mb-1">🎁 Hediye Notunuz (Ahşap kutu içerisine eklenecektir)</label>
                            <textarea name="gift_note" id="giftNoteInput" rows="2" maxlength="250" placeholder="Sevdiklerinize iletmek istediğiniz özel notu buraya yazabilirsiniz..." class="w-full p-2.5 text-xs bg-white border border-amber-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-brand text-gray-800"></textarea>
                            <span class="text-[10px] text-gray-400 block text-right">Maks. 250 karakter</span>
                        </div>
                    </div>

                    <!-- Add to Cart Button -->
                    <button type="submit" id="mainAddToCartBtn" class="w-full bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold py-4 px-6 rounded-xl transition-all shadow-md flex items-center justify-center gap-2.5 text-sm uppercase tracking-wide">
                        <i class="fa-solid fa-cart-shopping text-base"></i>
                        <span>Fotoğraflı Siparişi Sepete Ekle</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- Similar Products -->
        @if(isset($similarProducts) && $similarProducts->count() > 0)
            <div class="mt-16 border-t border-gray-200 pt-10">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Benzer Ürünler</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                    @foreach($similarProducts as $similar)
                        <div class="bg-white rounded-2xl border border-gray-200/80 p-3 hover:border-brand transition shadow-xs group flex flex-col justify-between">
                            <a href="{{ $similar->url }}" class="block overflow-hidden rounded-xl bg-gray-50 h-44 flex items-center justify-center p-2 mb-3">
                                <img src="{{ $similar->image ?: '/cerceve.png' }}" alt="{{ $similar->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition duration-300" loading="lazy" decoding="async">
                            </a>
                            <div>
                                <a href="{{ $similar->url }}" class="text-xs md:text-sm font-bold text-gray-800 group-hover:text-brand line-clamp-2 mb-1">{{ $similar->name }}</a>
                                <div class="text-sm font-extrabold text-brand">{{ number_format($similar->price, 2, ',', '.') }} TL</div>
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

<!-- YouTube Video Modal -->
<div id="youtubeVideoModal" class="fixed inset-0 z-[999999] bg-black/90 hidden items-center justify-center p-4 backdrop-blur-md" onclick="closeYoutubeModal(event)">
    <div class="relative w-full max-w-4xl aspect-video bg-black rounded-2xl overflow-hidden shadow-2xl border border-gray-800" onclick="event.stopPropagation()">
        <button type="button" onclick="closeYoutubeModal()" class="absolute top-4 right-4 text-white text-3xl font-bold z-20 hover:text-red-500 transition leading-none bg-black/50 w-10 h-10 rounded-full flex items-center justify-center">&times;</button>
        <iframe id="youtubeIframe" src="" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>

<!-- Instagram Video / Reel Modal -->
<div id="instagramVideoModal" class="fixed inset-0 z-[999999] bg-black/90 hidden items-center justify-center p-4 backdrop-blur-md" onclick="closeInstagramModal(event)">
    <div class="relative w-full max-w-md h-[85vh] max-h-[720px] bg-black rounded-2xl overflow-hidden shadow-2xl border border-gray-800 flex flex-col" onclick="event.stopPropagation()">
        <button type="button" onclick="closeInstagramModal()" class="absolute top-3 right-3 text-white text-2xl font-bold z-30 hover:text-pink-500 transition leading-none bg-black/60 w-9 h-9 rounded-full flex items-center justify-center">&times;</button>
        <iframe id="instagramIframe" src="" class="w-full h-full rounded-2xl" frameborder="0" scrolling="no" allowtransparency="true"></iframe>
    </div>
</div>

<script>
function changeMainImage(el, src) {
    document.getElementById('mainProductImage').src = src;
    document.querySelectorAll('.thumb-box').forEach(box => {
        box.classList.remove('border-brand', 'border-2');
        box.classList.add('border-gray-200', 'border');
    });
    el.classList.remove('border-gray-200', 'border');
    el.classList.add('border-brand', 'border-2');
}

function toggleGiftNote(checkbox) {
    const container = document.getElementById('giftNoteContainer');
    if (checkbox.checked) {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
        document.getElementById('giftNoteInput').value = '';
    }
}

// Strict Image File Security Validator
function isStrictValidImage(file) {
    if (!file) return false;
    const filename = file.name.toLowerCase();
    const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    const ext = filename.split('.').pop();
    if (!allowedExtensions.includes(ext)) {
        showToast('Yalnızca JPG, JPEG, PNG, WEBP ve GIF formatında görseller yüklenebilir!', 'error');
        return false;
    }

    const dangerExtensions = ['.php', '.exe', '.zip', '.rar', '.sh', '.bat', '.py', '.js', '.html', '.htm', '.phtml', '.phps', '.jar'];
    for (let danger of dangerExtensions) {
        if (filename.includes(danger)) {
            showToast('Güvenlik nedeniyle şüpheli dosyalar kabul edilmemektedir!', 'error');
            return false;
        }
    }

    if (!file.type.startsWith('image/')) {
        showToast('Seçilen dosya geçerli bir fotoğraf/görsel değil!', 'error');
        return false;
    }

    return true;
}

// Reset image inputs on load
window.addEventListener('load', function() {
    const frontInput = document.getElementById('customImageFrontInput');
    const backInput = document.getElementById('customImageBackInput');
    const singleInput = document.getElementById('customImageInput');

    if (frontInput) frontInput.value = '';
    if (backInput) backInput.value = '';
    if (singleInput) singleInput.value = '';
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
        
        const status = document.getElementById('frontPhotoStatus');
        if (status) { status.innerText = file.name; status.className = "text-[10px] text-emerald-600 font-bold"; }
        const prevBox = document.getElementById('frontPhotoPreview');
        if (prevBox) {
            prevBox.classList.remove('hidden');
            const imgEl = prevBox.querySelector('img');
            if (imgEl) imgEl.src = dataUrl;
        }
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
        
        const status = document.getElementById('backPhotoStatus');
        if (status) { status.innerText = file.name; status.className = "text-[10px] text-purple-600 font-bold"; }
        const prevBox = document.getElementById('backPhotoPreview');
        if (prevBox) {
            prevBox.classList.remove('hidden');
            const imgEl = prevBox.querySelector('img');
            if (imgEl) imgEl.src = dataUrl;
        }
    };
    reader.readAsDataURL(file);
}

function submitAddToCartAjax() {
    const frontInput = document.getElementById('customImageFrontInput');
    const singleInput = document.getElementById('customImageInput');
    
    const hasFrontPhoto = (frontInput && frontInput.files && frontInput.files.length > 0) ||
                          (singleInput && singleInput.files && singleInput.files.length > 0);

    if (!hasFrontPhoto) {
        showToast('Sipariş verebilmek için 1. Fotoğrafı (Ön Yüz) yüklemeniz zorunludur!', 'error');
        const uploadBox = document.getElementById('frontPhotoTitle');
        if (uploadBox) {
            uploadBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
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
            showToast(data.message || 'Kişiselleştirilmiş ürününüz sepete eklendi!', 'success');
            
            // Meta Pixel AddToCart Event
            if (typeof window.fbPixelTrack === 'function') {
                window.fbPixelTrack('AddToCart', {
                    content_name: @json($product->name),
                    content_ids: [@json((string)($product->sku ?? $product->id))],
                    content_type: 'product',
                    value: {{ (float)$product->price }},
                    currency: 'TRY'
                });
            }

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
        console.error('Add to cart error:', err);
        showToast('Fotoğraflar yüklenirken sunucu hatası oluştu.', 'error');
    });
}

function confirmAddToCart(event) {
    if (event) event.preventDefault();

    const fileFront = document.getElementById('customImageFrontInput');
    const fileBack = document.getElementById('customImageBackInput');
    const fileMain = document.getElementById('customImageInput');

    const hasFront = fileFront && fileFront.files && fileFront.files.length > 0;
    const hasBack = fileBack && fileBack.files && fileBack.files.length > 0;
    const hasMain = fileMain && fileMain.files && fileMain.files.length > 0;

    if (!hasFront && !hasBack && !hasMain) {
        showToast('⚠️ Lütfen siparişinizi tamamlamadan önce ahşap çerçevenize basılacak fotoğrafınızı yükleyiniz.', 'error');
        const uploadBox = document.getElementById('frontPhotoTitle');
        if (uploadBox) {
            uploadBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
    }

    submitAddToCartAjax();
    return false;
}

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

function openInstagramModal(embedUrl) {
    const iframe = document.getElementById('instagramIframe');
    if (iframe) iframe.src = embedUrl;
    const modal = document.getElementById('instagramVideoModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeInstagramModal(e) {
    const modal = document.getElementById('instagramVideoModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    const iframe = document.getElementById('instagramIframe');
    if (iframe) iframe.src = '';
}

// Meta Pixel ViewContent Event
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.fbPixelTrack === 'function') {
        window.fbPixelTrack('ViewContent', {
            content_name: @json($product->name),
            content_ids: [@json((string)($product->sku ?? $product->id))],
            content_type: 'product',
            value: {{ (float)$product->price }},
            currency: 'TRY'
        });
    }
});
</script>
@endsection
