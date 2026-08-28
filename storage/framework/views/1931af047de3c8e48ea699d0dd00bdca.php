<?php $__env->startSection('title', ($product->name ?? 'Ürün Detayı') . ' — Ahşap Evim Manisa'); ?>
<?php $__env->startSection('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?: ($product->name . ' - Kişiye özel el işçiliği masif ahşap çerçeve.')), 155)); ?>
<?php $__env->startSection('meta_image', $product->image ? (str_starts_with($product->image, 'http') ? $product->image : url($product->image)) : 'https://ahsapevimmanisa.com/ahsaplogo_org.png'); ?>

<?php if($product->threeDTemplate): ?>
    <?php $__env->startPush('head_scripts'); ?>
        <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white pb-12">
    <div class="container mx-auto px-4 py-4">
        <!-- Breadcrumb -->
        <nav class="flex text-[13px] text-gray-500 mb-6">
            <a href="<?php echo e(url('/')); ?>" class="hover:underline">Anasayfa</a>
            <span class="mx-2">></span>
            <a href="<?php echo e(url('/urunler')); ?>?category=<?php echo e($product->category->slug ?? ''); ?>" class="hover:underline"><?php echo e($product->category->name ?? 'Kategori'); ?></a>
            <span class="mx-2">></span>
            <span class="text-gray-800"><?php echo e($product->name); ?></span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
            
            <!-- Left Side: Images -->
            <div class="w-full lg:w-[52%] flex flex-col md:flex-row gap-3 md:gap-4">
                <!-- Desktop Thumbnails (Sol Taraf Dikey) -->
                <div class="hidden md:flex flex-col gap-2.5 w-20 md:w-22 flex-shrink-0">
                    <div class="thumb-box w-20 md:w-22 h-24 md:h-28 border-2 border-brand rounded-xl cursor-pointer overflow-hidden p-1 bg-white shadow-xs transition hover:scale-105" onclick="changeMainImage(this, '<?php echo e($product->image ?: '/cerceve.png'); ?>')">
                        <img src="<?php echo e($product->image ?: '/cerceve.png'); ?>" class="w-full h-full object-contain" alt="thumbnail">
                    </div>
                    <?php if(count($product->gallery_urls) > 0): ?>
                        <?php $__currentLoopData = $product->gallery_urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addImg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="thumb-box w-20 md:w-22 h-24 md:h-28 border border-gray-200 rounded-xl cursor-pointer overflow-hidden p-1 bg-white hover:border-brand transition hover:scale-105" onclick="changeMainImage(this, '<?php echo e($addImg); ?>')">
                                <img src="<?php echo e($addImg); ?>" class="w-full h-full object-contain" alt="thumbnail">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <?php if($product->youtube_id): ?>
                        <div class="thumb-box w-20 md:w-22 h-24 md:h-28 border-2 border-red-400 rounded-xl cursor-pointer overflow-hidden relative bg-black group shadow-xs hover:border-red-600 transition" onclick="openYoutubeModal('https://www.youtube.com/embed/<?php echo e($product->youtube_id); ?>')">
                            <img src="https://img.youtube.com/vi/<?php echo e($product->youtube_id); ?>/hqdefault.jpg" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition" alt="video thumbnail">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/40 group-hover:bg-black/20 transition">
                                <i class="fa-brands fa-youtube text-red-600 text-2xl drop-shadow-md"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($product->instagram_code): ?>
                        <div class="thumb-box w-20 md:w-22 h-24 md:h-28 border-2 border-pink-400 rounded-xl cursor-pointer overflow-hidden relative bg-gradient-to-tr from-amber-500 via-rose-500 to-purple-600 group shadow-xs hover:border-pink-600 transition" onclick="openInstagramModal('<?php echo e($product->instagram_embed_url); ?>')">
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/30 group-hover:bg-black/10 transition">
                                <i class="fa-brands fa-instagram text-white text-2xl drop-shadow-md"></i>
                                <span class="text-[9px] font-extrabold text-white mt-1 uppercase tracking-tighter">REEL</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Main Image Container (Mobilde Çerçeve Kaldırıldı ve Ekrana Uçtan Uca %100 Yayıldı) -->
                <div class="flex-1 flex flex-col w-full">
                    <div id="mainImageContainer" class="-mx-4 w-[calc(100%+2rem)] sm:mx-0 sm:w-full bg-white sm:bg-gray-50/80 border-0 sm:border sm:border-gray-200/80 rounded-none sm:rounded-2xl relative overflow-hidden flex items-center justify-center h-[460px] xs:h-[500px] sm:h-[540px] md:h-[580px] lg:h-[620px]">
                        <img id="mainProductImage" src="<?php echo e($product->image ?: '/cerceve.png'); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-full object-contain mix-blend-multiply p-0 sm:p-2 md:p-3 transition-all duration-300 z-10">
                    </div>

                    <!-- Mobile Thumbnail Gallery (Mobil Görsel Galerisi Yatay Kaydırmalı & Kenarlara Sıfırlanmış) -->
                    <div class="flex md:hidden items-center gap-2.5 overflow-x-auto -mx-4 px-4 pt-3 pb-1 max-w-[calc(100%+2rem)] text-center scrollbar-none">
                        <div class="thumb-box w-16 h-20 border-2 border-brand rounded-xl cursor-pointer overflow-hidden p-0.5 bg-white shrink-0 shadow-xs transition" onclick="changeMainImage(this, '<?php echo e($product->image ?: '/cerceve.png'); ?>')">
                            <img src="<?php echo e($product->image ?: '/cerceve.png'); ?>" class="w-full h-full object-contain" alt="thumbnail">
                        </div>
                        <?php if(count($product->gallery_urls) > 0): ?>
                            <?php $__currentLoopData = $product->gallery_urls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addImg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="thumb-box w-16 h-20 border border-gray-200 rounded-xl cursor-pointer overflow-hidden p-0.5 bg-white hover:border-brand shrink-0 shadow-xs transition" onclick="changeMainImage(this, '<?php echo e($addImg); ?>')">
                                    <img src="<?php echo e($addImg); ?>" class="w-full h-full object-contain" alt="thumbnail">
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if($product->youtube_id): ?>
                            <div class="thumb-box w-16 h-20 border-2 border-red-400 rounded-xl cursor-pointer overflow-hidden relative bg-black shrink-0 shadow-xs hover:border-red-600 transition" onclick="openYoutubeModal('https://www.youtube.com/embed/<?php echo e($product->youtube_id); ?>')">
                                <img src="https://img.youtube.com/vi/<?php echo e($product->youtube_id); ?>/hqdefault.jpg" class="w-full h-full object-cover opacity-80" alt="video thumbnail">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                                    <i class="fa-brands fa-youtube text-red-600 text-xl"></i>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if($product->instagram_code): ?>
                            <div class="thumb-box w-16 h-20 border-2 border-pink-400 rounded-xl cursor-pointer overflow-hidden relative bg-gradient-to-tr from-amber-500 via-rose-500 to-purple-600 shrink-0 shadow-xs hover:border-pink-600 transition" onclick="openInstagramModal('<?php echo e($product->instagram_embed_url); ?>')">
                                <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/30">
                                    <i class="fa-brands fa-instagram text-white text-xl"></i>
                                    <span class="text-[8px] font-extrabold text-white mt-0.5 uppercase tracking-tighter">REEL</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Temsili Görsel Uyarısı (Ürün Görselinin Altında) -->
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
                    <?php echo e($product->name); ?>

                </h1>
                
                <hr class="border-gray-200 mb-4">

                <!-- Specs -->
                <div class="text-[13px] text-gray-700 mb-4 space-y-2">
                    <?php if(isset($product->features['color']) && $product->features['color']): ?>
                        <p><span class="font-bold text-gray-800">Renk:</span> <?php echo e($product->features['color']); ?></p>
                    <?php endif; ?>
                    <?php if(isset($product->features['size']) && $product->features['size']): ?>
                        <p><span class="font-bold text-gray-800">Boyut/Ebat:</span> <?php echo e($product->features['size']); ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Expanded Description Area -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-base font-bold text-gray-800 mb-2">Ürün Açıklaması</h3>
                    <div class="prose max-w-none text-gray-600 text-sm leading-relaxed mb-6">
                        <p><?php echo e($product->description ?: 'Özel ahşap işçiliği ile hazırlanmış yüksek kaliteli dekoratif ürün.'); ?></p>
                    </div>
                    
                    <!-- 3D Kişiselleştirme Butonu (İstek üzerine pasife alındı, kodlar saklanıyor) -->
                    <button type="button" onclick="openCustomizationModal()" class="hidden bg-brand/5 border-2 border-brand/20 hover:bg-brand/10 text-brand font-bold py-4 px-6 rounded-xl transition-colors items-center justify-center gap-3 w-full md:w-auto shadow-sm">
                        <i class="fa-solid fa-sliders text-xl"></i>
                        Kişiselleştir ve Ön İzle
                    </button>
                </div>
            </div>

            <!-- Right Side: Buybox -->
            <div class="w-full lg:w-[28%]">
                <form id="addToCartForm" action="<?php echo e(url('/sepet/ekle')); ?>" method="POST" enctype="multipart/form-data" class="border border-gray-200 rounded-2xl p-5 bg-white shadow-sm sticky top-24 space-y-4" onsubmit="return confirmAddToCart(event)">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                    
                    <!-- Price -->
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="text-3xl font-extrabold text-brand"><?php echo e(number_format($product->price, 2, ',', '.')); ?> TL</div>
                            <?php if($product->discount_percent > 0): ?>
                                <span class="bg-red-600 text-white font-extrabold text-xs px-2.5 py-1 rounded-full shadow-sm">%<?php echo e($product->discount_percent); ?> İNDİRİM</span>
                            <?php endif; ?>
                        </div>
                        <?php if($product->discount_percent > 0): ?>
                            <div class="text-sm text-gray-400 line-through mt-1"><?php echo e(number_format($product->original_price, 2, ',', '.')); ?> TL</div>
                        <?php endif; ?>
                    </div>

                    <!-- FOTOĞRAF YÜKLEME ALANI (ZORUNLU) -->
                    <div class="bg-blue-50/70 border-2 border-dashed border-blue-300 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-black text-blue-950 uppercase tracking-wide flex items-center gap-1.5">
                                <i class="fa-solid fa-[#C87A53] fa-camera text-brand text-sm"></i>
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
                        <input type="file" id="customImageInput" name="custom_image" accept="image/*" class="hidden" onchange="handleImageUpload(event)">
                        <input type="hidden" name="custom_preview_base64" id="customPreviewBase64Input">

                        <!-- Visible Photo Upload Buttons & Previews -->
                        <div class="space-y-2">
                            <!-- 1. Fotoğraf / Ön Yüz -->
                            <div onclick="document.getElementById('customImageFrontInput').click(); document.getElementById('customImageInput').click();" 
                                 class="flex items-center justify-between p-3 bg-white rounded-xl border border-blue-200 hover:border-brand cursor-pointer transition shadow-2xs group">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                                        <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-gray-800" id="frontPhotoTitle">1. Fotoğraf (Ön Yüz)</div>
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
                                 class="flex items-center justify-between p-3 bg-white rounded-xl border border-blue-200 hover:border-brand cursor-pointer transition shadow-2xs group">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                                        <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-gray-800" id="backPhotoTitle">2. Fotoğraf (Arka Yüz)</div>
                                        <div class="text-[10px] text-gray-400" id="backPhotoStatus">Çift taraflı için (Opsiyonel)</div>
                                    </div>
                                </div>
                                <div id="backPhotoPreview" class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden hidden border border-gray-200">
                                    <img src="" class="w-full h-full object-cover">
                                </div>
                                <span class="text-xs font-bold text-brand bg-brand/10 px-2.5 py-1 rounded-lg group-hover:bg-brand group-hover:text-white transition">Seç</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Badge -->
                    <div class="mb-3">
                        <?php if($product->stock <= 0): ?>
                            <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-extrabold px-3 py-1.5 rounded-full border border-red-200">
                                <i class="fa-solid fa-circle-xmark"></i> Stokta Yok
                            </span>
                        <?php elseif($product->stock <= 5): ?>
                            <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 text-xs font-extrabold px-3 py-1.5 rounded-full border border-amber-200">
                                <i class="fa-solid fa-triangle-exclamation"></i> Son <?php echo e($product->stock); ?> adet kaldı!
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full border border-green-200">
                                <i class="fa-solid fa-circle-check"></i> Stokta Mevcut
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Kargo Teslimat Bilgisi (16:00 Kuralı - Türkiye Saati) -->
                    <?php
                        $nowIstanbul = \Carbon\Carbon::now('Europe/Istanbul');
                        $currentHour = (int) $nowIstanbul->format('H');
                        $isSameDayShipping = $currentHour < 16;
                    ?>
                    <div class="mb-4 rounded-xl p-3.5 border transition-all shadow-sm <?php echo e($isSameDayShipping ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : 'bg-amber-50 border-amber-300 text-amber-950'); ?>">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl <?php echo e($isSameDayShipping ? 'bg-emerald-600' : 'bg-amber-600'); ?> text-white flex items-center justify-center shrink-0 shadow-sm">
                                <i class="fa-solid <?php echo e($isSameDayShipping ? 'fa-bolt text-lg' : 'fa-truck-fast text-base'); ?>"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs font-black tracking-wide uppercase <?php echo e($isSameDayShipping ? 'text-emerald-700' : 'text-amber-700'); ?>">
                                    <?php echo e($isSameDayShipping ? '⚡ AYNI GÜN KARGODA!' : '📦 YARIN KARGODA'); ?>

                                </div>
                                <p class="text-[12px] font-bold leading-tight mt-0.5 <?php echo e($isSameDayShipping ? 'text-emerald-900' : 'text-amber-900'); ?>">
                                    <?php if($isSameDayShipping): ?>
                                        Saat 16:00'a kadar verilen siparişler <strong>aynı gün kargoda!</strong>
                                    <?php else: ?>
                                        Saat 16:00 sonrası verilen siparişler <strong>yarın kargoda!</strong>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
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
                    <div class="flex flex-col gap-3 mb-2">
                        <?php if($product->stock <= 0): ?>
                            <button type="button" disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3.5 px-4 rounded-lg text-base cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fa-solid fa-ban"></i>
                                Stokta Yok
                            </button>
                        <?php else: ?>
                            <button type="submit" class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3.5 px-4 rounded-lg transition text-base shadow-md flex items-center justify-center gap-2">
                                <i class="fa-solid fa-cart-shopping"></i>
                                Sepete Ekle
                            </button>
                        <?php endif; ?>
                        
                        <button type="button" onclick="toggleFavorite(<?php echo e($product->id); ?>, this)" class="w-full bg-rose-50 hover:bg-rose-100 border border-rose-200/80 text-rose-700 font-bold py-3 px-4 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-sm">
                            <i class="<?php echo e($product->isFavoritedBy() ? 'fa-solid fa-heart text-red-500 text-lg drop-shadow-sm' : 'fa-regular fa-heart text-red-500 text-lg'); ?>"></i>
                            <span><?php echo e($product->isFavoritedBy() ? 'Favorilerinizde' : 'Favorilere Ekle'); ?></span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Similar Products -->
        <?php if(isset($similarProducts) && $similarProducts->count() > 0): ?>
        <div class="mt-16">
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-2">Benzer Ürünler</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <?php $__currentLoopData = $similarProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $simProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white border border-gray-200 rounded-xl p-3 hover:shadow-lg transition-all product-card flex flex-col h-full group relative">
                    <a href="<?php echo e($simProduct->url); ?>" class="block mb-3 relative overflow-hidden rounded-lg bg-gray-50 pt-[100%]">
                        <img src="<?php echo e($simProduct->image ?: '/cerceve.png'); ?>" alt="<?php echo e($simProduct->name); ?>" class="absolute inset-0 w-full h-full object-contain mix-blend-multiply p-2 transition-transform duration-500 group-hover:scale-105">
                    </a>
                    <div class="flex-grow flex flex-col">
                        <div class="text-xs text-brand font-semibold mb-1"><?php echo e($simProduct->category->name ?? ''); ?></div>
                        <h3 class="text-sm font-medium text-gray-800 mb-2 line-clamp-2 leading-tight group-hover:text-brand transition"><a href="<?php echo e($simProduct->url); ?>"><?php echo e($simProduct->name); ?></a></h3>
                        <div class="mt-auto pt-2 flex items-center justify-between">
                            <div class="text-lg font-extrabold text-brand"><?php echo e(number_format($simProduct->price, 2, ',', '.')); ?> TL</div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recently Viewed Products -->
        <?php if(isset($recentlyViewed) && $recentlyViewed->count() > 0): ?>
        <div class="mt-16">
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-2">Daha Önce Ziyaret Ettikleriniz</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <?php $__currentLoopData = $recentlyViewed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recentProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white border border-gray-200 rounded-xl p-3 hover:shadow-lg transition-all product-card flex flex-col h-full group relative">
                    <a href="<?php echo e($recentProduct->url); ?>" class="block mb-3 relative overflow-hidden rounded-lg bg-gray-50 pt-[100%]">
                        <img src="<?php echo e($recentProduct->image ?: '/cerceve.png'); ?>" alt="<?php echo e($recentProduct->name); ?>" class="absolute inset-0 w-full h-full object-contain mix-blend-multiply p-2 transition-transform duration-500 group-hover:scale-105">
                    </a>
                    <div class="flex-grow flex flex-col">
                        <h3 class="text-sm font-medium text-gray-800 mb-2 line-clamp-2 leading-tight group-hover:text-brand transition"><a href="<?php echo e($recentProduct->url); ?>"><?php echo e($recentProduct->name); ?></a></h3>
                        <div class="mt-auto pt-2 flex items-center justify-between">
                            <div class="text-lg font-extrabold text-brand"><?php echo e(number_format($recentProduct->price, 2, ',', '.')); ?> TL</div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

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
        <div class="flex-1 relative overflow-hidden flex items-center justify-center cursor-grab active:cursor-grabbing" id="workspaceContainer" style="background-image: url('/images/template-bg.jpg'); background-position: center center; background-size: cover; background-repeat: no-repeat;">
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

<?php if($product->threeDTemplate): ?>
let modalScene, modalCamera, modalRenderer, modalControls, modalAnimationId;
let modalModelGroup = new THREE.Group();
let modalOuterGroup, modalCustomRotatingFrame, modalCustomPhotoFront, modalCustomPhotoBack;

function attachInnerFrameDragController(renderer, camera, controls, rotatingFrameGroup) {
    if (!renderer || !renderer.domElement || !camera || !rotatingFrameGroup) return;

    const canvas = renderer.domElement;
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();

    let isDraggingInner = false;
    let previousMouseX = 0;
    let previousMouseY = 0;

    function getCanvasRelativeMouse(e) {
        const rect = canvas.getBoundingClientRect();
        return {
            x: ((e.clientX - rect.left) / rect.width) * 2 - 1,
            y: -((e.clientY - rect.top) / rect.height) * 2 + 1
        };
    }

    function checkHitInnerFrame(e) {
        if (!camera || !rotatingFrameGroup) return false;
        const coords = getCanvasRelativeMouse(e);
        mouse.x = coords.x;
        mouse.y = coords.y;

        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(rotatingFrameGroup.children, true);
        return intersects.length > 0;
    }

    canvas.addEventListener('pointermove', function(e) {
        if (!isDraggingInner) {
            if (checkHitInnerFrame(e)) {
                canvas.style.cursor = 'grab';
            } else {
                canvas.style.cursor = 'default';
            }
        }
    });

    canvas.addEventListener('pointerdown', function(e) {
        if (e.button !== 0 && e.pointerType === 'mouse') return;

        if (checkHitInnerFrame(e)) {
            isDraggingInner = true;
            previousMouseX = e.clientX;
            previousMouseY = e.clientY;
            if (controls) controls.enabled = false;
            canvas.style.cursor = 'grabbing';
            e.stopPropagation();
        }
    });

    window.addEventListener('pointermove', function(e) {
        if (isDraggingInner) {
            const deltaX = e.clientX - previousMouseX;
            rotatingFrameGroup.rotation.y += deltaX * 0.012;
            previousMouseX = e.clientX;
            previousMouseY = e.clientY;
        }
    });

    const releaseInnerDrag = function() {
        if (isDraggingInner) {
            isDraggingInner = false;
            if (controls) controls.enabled = true;
            canvas.style.cursor = 'default';
        }
    };

    window.addEventListener('pointerup', releaseInnerDrag);
    window.addEventListener('pointercancel', releaseInnerDrag);
}

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
    if (typeof attachInnerFrameDragController === 'function') {
        attachInnerFrameDragController(modalRenderer, modalCamera, modalControls, modalCustomRotatingFrame);
    }

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
    const width = <?php echo e($product->threeDTemplate->width); ?>;
    const height = <?php echo e($product->threeDTemplate->height); ?>;
    const depth = <?php echo e($product->threeDTemplate->depth); ?>;
    const thickness = <?php echo e($product->threeDTemplate->thickness); ?>;

    const innerW = <?php echo e($product->threeDTemplate->inner_width); ?>;
    const innerH = <?php echo e($product->threeDTemplate->inner_height); ?>;
    const innerD = <?php echo e($product->threeDTemplate->inner_depth); ?>;
    const innerB = <?php echo e($product->threeDTemplate->inner_border); ?>;

    const px = <?php echo e($product->threeDTemplate->pos_x); ?>;
    const py = <?php echo e($product->threeDTemplate->pos_y); ?>;

    const woodType = "<?php echo e($product->threeDTemplate->wood_type); ?>";

    // Use modalRenderer for anisotropy
    const woodTextures = generateWoodTextures(woodType, modalRenderer);
    const bScaleRaw = <?php echo e($product->threeDTemplate->bump_scale ?: 0.28); ?>;
    const bScale = bScaleRaw < 0.12 ? 0.28 : bScaleRaw;

    const hasTop = <?php echo e($product->threeDTemplate->has_top ? 'true' : 'false'); ?>;
    const hasBottom = <?php echo e($product->threeDTemplate->has_bottom ? 'true' : 'false'); ?>;
    const hasLeft = <?php echo e($product->threeDTemplate->has_left ? 'true' : 'false'); ?>;
    const hasRight = <?php echo e($product->threeDTemplate->has_right ? 'true' : 'false'); ?>;

    modalOuterGroup = new THREE.Group();

    if(hasTop) {
        const mat = createPieceMaterial(woodTextures, bScale, 'top');
        const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasLeft, hasRight), mat);
        mesh.position.y = height/2 - thickness/2;
        modalOuterGroup.add(mesh);
    }
    if(hasBottom) {
        const mat = createPieceMaterial(woodTextures, bScale, 'bottom');
        const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasRight, hasLeft), mat);
        mesh.rotation.z = Math.PI;
        mesh.position.y = -height/2 + thickness/2;
        modalOuterGroup.add(mesh);
    }
    if(hasLeft) {
        const mat = createPieceMaterial(woodTextures, bScale, 'left');
        const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasBottom, hasTop), mat);
        mesh.rotation.z = Math.PI / 2;
        mesh.position.x = -width/2 + thickness/2;
        modalOuterGroup.add(mesh);
    }
    if(hasRight) {
        const mat = createPieceMaterial(woodTextures, bScale, 'right');
        const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasTop, hasBottom), mat);
        mesh.rotation.z = -Math.PI / 2;
        mesh.position.x = width/2 - thickness/2;
        modalOuterGroup.add(mesh);
    }

    modalModelGroup.add(modalOuterGroup);

    modalCustomRotatingFrame = new THREE.Group();
    modalCustomRotatingFrame.position.set(px, py, 0);

    const matInTop = createPieceMaterial(woodTextures, bScale, 'inner_top');
    const topIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), matInTop);
    topIn.position.y = innerH/2 - innerB/2;
    modalCustomRotatingFrame.add(topIn);

    const matInBot = createPieceMaterial(woodTextures, bScale, 'inner_bottom');
    const botIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), matInBot);
    botIn.rotation.z = Math.PI;
    botIn.position.y = -innerH/2 + innerB/2;
    modalCustomRotatingFrame.add(botIn);

    const matInLeft = createPieceMaterial(woodTextures, bScale, 'inner_left');
    const leftIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), matInLeft);
    leftIn.rotation.z = Math.PI / 2;
    leftIn.position.x = -innerW/2 + innerB/2;
    modalCustomRotatingFrame.add(leftIn);

    const matInRight = createPieceMaterial(woodTextures, bScale, 'inner_right');
    const rightIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), matInRight);
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

    const matBacking = createPieceMaterial(woodTextures, bScale, 'backing');
    const backingGeom = new THREE.BoxGeometry(photoW, photoH, 0.08);
    const backing = new THREE.Mesh(backingGeom, matBacking);
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

    // Render 3D Accessory / Object (Nostaljik Sokak Lambası)
    const modalHasAccessory = <?php echo e($product->threeDTemplate->has_accessory ? 'true' : 'false'); ?>;
    const modalAccessoryType = "<?php echo e($product->threeDTemplate->accessory_type ?? 'street_lamp'); ?>";
    const modalAccessoryPos = "<?php echo e($product->threeDTemplate->accessory_position ?? 'right'); ?>";
    const modalAccOffsetX = <?php echo e($product->threeDTemplate->accessory_offset_x ?? 0); ?>;
    const modalAccOffsetY = <?php echo e($product->threeDTemplate->accessory_offset_y ?? 0); ?>;
    const modalAccOffsetZ = <?php echo e($product->threeDTemplate->accessory_offset_z ?? 0); ?>;
    const modalAccScale = <?php echo e($product->threeDTemplate->accessory_scale ?? 1.0); ?>;

    if (modalHasAccessory && modalAccessoryType === 'street_lamp') {
        const lampHeight = Math.min(height * 0.65, 18) * modalAccScale;
        const lampGroup = createStreetLampGroup(lampHeight);
        
        const bottomBoardY = -height/2 + thickness;
        let posX = 0;
        if (modalAccessoryPos === 'right') {
            posX = width/2 - thickness * 2.2 + modalAccOffsetX;
        } else if (modalAccessoryPos === 'left') {
            posX = -width/2 + thickness * 2.2 + modalAccOffsetX;
        } else {
            posX = modalAccOffsetX;
        }

        lampGroup.position.set(posX, bottomBoardY + modalAccOffsetY, (depth * 0.1) + modalAccOffsetZ);
        modalModelGroup.add(lampGroup);
    }

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
<?php endif; ?>

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

        // Buybox preview & status update
        const status = document.getElementById('frontPhotoStatus');
        if (status) { status.innerText = file.name; status.className = "text-[10px] text-emerald-600 font-bold"; }
        const prevBox = document.getElementById('frontPhotoPreview');
        if (prevBox) {
            prevBox.classList.remove('hidden');
            const imgEl = prevBox.querySelector('img');
            if (imgEl) imgEl.src = dataUrl;
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

        // Buybox preview & status update
        const status = document.getElementById('backPhotoStatus');
        if (status) { status.innerText = file.name; status.className = "text-[10px] text-purple-600 font-bold"; }
        const prevBox = document.getElementById('backPhotoPreview');
        if (prevBox) {
            prevBox.classList.remove('hidden');
            const imgEl = prevBox.querySelector('img');
            if (imgEl) imgEl.src = dataUrl;
        }

        const badge = document.getElementById('badgeBackPhoto');
        if (badge) badge.classList.remove('hidden');

        const btnText = document.getElementById('btnBackText');
        if (btnText) btnText.innerText = "2. Arka Yüz Fotoğrafını Değiştir";
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

    const fileFront = document.getElementById('customImageFrontInput');
    const fileBack = document.getElementById('customImageBackInput');
    const fileMain = document.getElementById('customImageInput');

    const hasFront = fileFront && fileFront.files && fileFront.files.length > 0;
    const hasBack = fileBack && fileBack.files && fileBack.files.length > 0;
    const hasMain = fileMain && fileMain.files && fileMain.files.length > 0;

    if (!hasFront && !hasBack && !hasMain) {
        alert('⚠️ Lütfen siparişinizi tamamlamadan önce ahşap çerçevenize basılacak fotoğrafınızı yükleyiniz.');
        const uploadBox = document.getElementById('frontPhotoTitle');
        if (uploadBox) {
            uploadBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
    }

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

<?php if($product->threeDTemplate): ?>
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

    function generateWoodTextures(woodType, rendererInstance) {
        const size = 1024;
        const colorCanvas = document.createElement('canvas'); colorCanvas.width = size; colorCanvas.height = size;
        const colorCtx = colorCanvas.getContext('2d');
        const bumpCanvas = document.createElement('canvas'); bumpCanvas.width = size; bumpCanvas.height = size;
        const bumpCtx = bumpCanvas.getContext('2d');
        const roughCanvas = document.createElement('canvas'); roughCanvas.width = size; roughCanvas.height = size;
        const roughCtx = roughCanvas.getContext('2d');

        let baseColor, lineColor, poreColor;
        if (woodType && (woodType.startsWith('#') || /^[0-9a-fA-F]{6}$/.test(woodType.replace('#','')))) {
            const hex = woodType.startsWith('#') ? woodType : '#' + woodType;
            baseColor = hex;
            lineColor = darkenColor(hex, 28);
            poreColor = darkenColor(hex, 48);
        } else if (woodType === 'Ceviz') {
            baseColor = '#4a3319'; lineColor = '#2b1b0e'; poreColor = '#1d1209';
        } else if (woodType === 'Meşe') {
            baseColor = '#a8896c'; lineColor = '#72553b'; poreColor = '#503a27';
        } else if (woodType === 'Çam') {
            baseColor = '#e3d3bd'; lineColor = '#ba9e7d'; poreColor = '#a68865';
        } else if (woodType === 'Kiraz') {
            baseColor = '#8c462b'; lineColor = '#562512'; poreColor = '#3c180a';
        } else {
            baseColor = woodType || '#ead9c3';
            lineColor = darkenColor(baseColor, 28);
            poreColor = darkenColor(baseColor, 48);
        }

        colorCtx.fillStyle = baseColor; colorCtx.fillRect(0, 0, size, size);
        bumpCtx.fillStyle = '#808080'; bumpCtx.fillRect(0, 0, size, size);
        roughCtx.fillStyle = '#d8d8d8'; roughCtx.fillRect(0, 0, size, size);

        for (let y = -50; y < size + 50; y += 7) {
            let freq = 0.003 + (y % 13) * 0.0003;
            let amp = 15 + (y % 17) * 1.5;
            let phase = y * 0.02;
            colorCtx.beginPath(); bumpCtx.beginPath(); roughCtx.beginPath();
            colorCtx.moveTo(0, y); bumpCtx.moveTo(0, y); roughCtx.moveTo(0, y);
            for (let x = 0; x <= size; x += 20) {
                let dy = Math.sin(x * freq + phase) * amp;
                colorCtx.lineTo(x, y + dy); bumpCtx.lineTo(x, y + dy); roughCtx.lineTo(x, y + dy);
            }
            colorCtx.strokeStyle = (y % 21 === 0) ? lineColor : poreColor;
            colorCtx.globalAlpha = (y % 21 === 0) ? 0.35 : 0.18;
            colorCtx.lineWidth = (y % 21 === 0) ? 2.8 : 1.4;
            colorCtx.stroke();
            bumpCtx.strokeStyle = (y % 21 === 0) ? '#101010' : '#404040';
            bumpCtx.globalAlpha = (y % 21 === 0) ? 0.45 : 0.2;
            bumpCtx.lineWidth = (y % 21 === 0) ? 3.0 : 1.5;
            bumpCtx.stroke();
            roughCtx.strokeStyle = '#d5d5d5'; roughCtx.globalAlpha = 0.2; roughCtx.lineWidth = 2.0; roughCtx.stroke();
        }
        colorCtx.globalAlpha = 1.0; bumpCtx.globalAlpha = 1.0; roughCtx.globalAlpha = 1.0;

        let knotX = size * 0.4;
        let knotY = size * 0.5;
        for (let r = 10; r < 45; r += 7) {
            colorCtx.beginPath(); bumpCtx.beginPath();
            colorCtx.ellipse(knotX, knotY, r * 2.5, r, Math.PI / 12, 0, Math.PI * 2);
            bumpCtx.ellipse(knotX, knotY, r * 2.5, r, Math.PI / 12, 0, Math.PI * 2);
            colorCtx.strokeStyle = lineColor; colorCtx.lineWidth = 1.8; colorCtx.globalAlpha = 0.25; colorCtx.stroke();
            bumpCtx.strokeStyle = '#151515'; bumpCtx.lineWidth = 2.0; bumpCtx.globalAlpha = 0.3; bumpCtx.stroke();
        }
        colorCtx.globalAlpha = 1.0; bumpCtx.globalAlpha = 1.0;

        const colorTex = new THREE.CanvasTexture(colorCanvas);
        const bumpTex = new THREE.CanvasTexture(bumpCanvas);
        const roughTex = new THREE.CanvasTexture(roughCanvas);
        colorTex.wrapS = THREE.ClampToEdgeWrapping; colorTex.wrapT = THREE.ClampToEdgeWrapping;
        bumpTex.wrapS = THREE.ClampToEdgeWrapping; bumpTex.wrapT = THREE.ClampToEdgeWrapping;
        roughTex.wrapS = THREE.ClampToEdgeWrapping; roughTex.wrapT = THREE.ClampToEdgeWrapping;

        if (rendererInstance && rendererInstance.capabilities) {
            const maxAniso = rendererInstance.capabilities.getMaxAnisotropy();
            colorTex.anisotropy = bumpTex.anisotropy = roughTex.anisotropy = maxAniso;
        }

        return { colorMap: colorTex, bumpMap: bumpTex, roughnessMap: roughTex };
    }

    function createPieceMaterial(woodTextures, bScale, pieceName) {
        const colorMap = woodTextures.colorMap.clone();
        const bumpMap = woodTextures.bumpMap.clone();
        const roughnessMap = woodTextures.roughnessMap.clone();

        colorMap.needsUpdate = bumpMap.needsUpdate = roughnessMap.needsUpdate = true;
        colorMap.wrapS = colorMap.wrapT = bumpMap.wrapS = bumpMap.wrapT = roughnessMap.wrapS = roughnessMap.wrapT = THREE.ClampToEdgeWrapping;

        const ry = parseFloat((Math.random() * 0.4).toFixed(3));
        colorMap.offset.set(0, ry); bumpMap.offset.set(0, ry); roughnessMap.offset.set(0, ry);

        return new THREE.MeshStandardMaterial({
            map: colorMap, bumpMap: bumpMap, bumpScale: bScale, roughnessMap: roughnessMap, roughness: 0.88, metalness: 0.0
        });
    }

    function createStreetLampGroup(targetHeight) {
        const lampGroup = new THREE.Group();
        const scale = (targetHeight || 14) / 16.0;

        const metalMat = new THREE.MeshStandardMaterial({ color: 0x222222, metalness: 0.8, roughness: 0.3 });
        const glassMat = new THREE.MeshStandardMaterial({ color: 0xfff2a3, transparent: true, opacity: 0.7, emissive: 0xffaa00, emissiveIntensity: 0.6 });

        // Base
        const base = new THREE.Mesh(new THREE.CylinderGeometry(1.2 * scale, 1.8 * scale, 0.8 * scale, 12), metalMat);
        base.position.y = 0.4 * scale;
        lampGroup.add(base);

        // Pole
        const pole = new THREE.Mesh(new THREE.CylinderGeometry(0.3 * scale, 0.5 * scale, 10 * scale, 12), metalMat);
        pole.position.y = 5.8 * scale;
        lampGroup.add(pole);

        // Lamp Housing / Head Base
        const headBase = new THREE.Mesh(new THREE.CylinderGeometry(1.4 * scale, 0.8 * scale, 0.6 * scale, 6), metalMat);
        headBase.position.y = 11.1 * scale;
        lampGroup.add(headBase);

        // Glass
        const glass = new THREE.Mesh(new THREE.CylinderGeometry(1.2 * scale, 0.8 * scale, 2.5 * scale, 6), glassMat);
        glass.position.y = 12.5 * scale;
        lampGroup.add(glass);

        // Roof
        const roof = new THREE.Mesh(new THREE.ConeGeometry(1.6 * scale, 1.2 * scale, 6), metalMat);
        roof.position.y = 14.3 * scale;
        lampGroup.add(roof);

        return lampGroup;
    }

    function createWoodenClockGroup(targetHeight) {
        const clockGroup = new THREE.Group();
        const scale = (targetHeight || 14) / 22.0;
        const woodMat = new THREE.MeshStandardMaterial({ color: 0x5a3d28, roughness: 0.5, metalness: 0.05 });
        const brassMat = new THREE.MeshStandardMaterial({ color: 0xc8a257, roughness: 0.3, metalness: 0.85 });
        const dialMat = new THREE.MeshStandardMaterial({ color: 0xfaf4e8, roughness: 0.2, metalness: 0.1 });
        const darkMat = new THREE.MeshStandardMaterial({ color: 0x111111, roughness: 0.4 });
        const bodyWidth = 4.5 * scale; const bodyHeight = 7.0 * scale; const bodyDepth = 2.2 * scale;
        const bodyMesh = new THREE.Mesh(new THREE.BoxGeometry(bodyWidth, bodyHeight, bodyDepth), woodMat);
        bodyMesh.position.y = bodyHeight / 2; clockGroup.add(bodyMesh);
        const archMesh = new THREE.Mesh(new THREE.CylinderGeometry(bodyWidth / 2, bodyWidth / 2, bodyDepth, 16, 1, false, 0, Math.PI), woodMat);
        archMesh.rotation.x = Math.PI / 2; archMesh.rotation.z = Math.PI / 2; archMesh.position.y = bodyHeight; clockGroup.add(archMesh);
        const bezelMesh = new THREE.Mesh(new THREE.TorusGeometry(1.6 * scale, 0.18 * scale, 8, 24), brassMat);
        bezelMesh.position.set(0, bodyHeight * 0.62, bodyDepth / 2 + 0.05 * scale); clockGroup.add(bezelMesh);
        const dialMesh = new THREE.Mesh(new THREE.CircleGeometry(1.55 * scale, 24), dialMat);
        dialMesh.position.set(0, bodyHeight * 0.62, bodyDepth / 2 + 0.08 * scale); clockGroup.add(dialMesh);
        const handH = new THREE.Mesh(new THREE.BoxGeometry(0.12 * scale, 0.7 * scale, 0.04 * scale), darkMat);
        handH.rotation.z = -Math.PI / 4; handH.position.set(0.2 * scale, bodyHeight * 0.62 + 0.2 * scale, bodyDepth / 2 + 0.12 * scale); clockGroup.add(handH);
        const handM = new THREE.Mesh(new THREE.BoxGeometry(0.08 * scale, 1.1 * scale, 0.04 * scale), darkMat);
        handM.rotation.z = Math.PI / 6; handM.position.set(-0.25 * scale, bodyHeight * 0.62 + 0.35 * scale, bodyDepth / 2 + 0.12 * scale); clockGroup.add(handM);
        const rodMesh = new THREE.Mesh(new THREE.CylinderGeometry(0.06 * scale, 0.06 * scale, 2.0 * scale, 8), brassMat);
        rodMesh.position.set(0, bodyHeight * 0.32, bodyDepth / 2 + 0.08 * scale); clockGroup.add(rodMesh);
        const bobMesh = new THREE.Mesh(new THREE.CylinderGeometry(0.6 * scale, 0.6 * scale, 0.15 * scale, 16), brassMat);
        bobMesh.rotation.x = Math.PI / 2; bobMesh.position.set(0, bodyHeight * 0.22, bodyDepth / 2 + 0.1 * scale); clockGroup.add(bobMesh);
        return clockGroup;
    }

    function createFlowerVaseGroup(targetHeight) {
        const plantGroup = new THREE.Group();
        const scale = (targetHeight || 14) / 22.0;
        const potMat = new THREE.MeshStandardMaterial({ color: 0xc46d4e, roughness: 0.6, metalness: 0.05 });
        const soilMat = new THREE.MeshStandardMaterial({ color: 0x3d2716, roughness: 0.9 });
        const leafMat = new THREE.MeshStandardMaterial({ color: 0x2e7d32, roughness: 0.5 });
        const flowerMat1 = new THREE.MeshStandardMaterial({ color: 0xe91e63, roughness: 0.4 });
        const flowerMat2 = new THREE.MeshStandardMaterial({ color: 0xffeb3b, roughness: 0.4 });
        const potMesh = new THREE.Mesh(new THREE.CylinderGeometry(1.6 * scale, 1.1 * scale, 3.2 * scale, 16), potMat);
        potMesh.position.y = 1.6 * scale; plantGroup.add(potMesh);
        const rimMesh = new THREE.Mesh(new THREE.TorusGeometry(1.65 * scale, 0.18 * scale, 8, 16), potMat);
        rimMesh.rotation.x = Math.PI / 2; rimMesh.position.y = 3.2 * scale; plantGroup.add(rimMesh);
        const soilMesh = new THREE.Mesh(new THREE.CylinderGeometry(1.5 * scale, 1.5 * scale, 0.2 * scale, 16), soilMat);
        soilMesh.position.y = 3.1 * scale; plantGroup.add(soilMesh);
        for (let i = 0; i < 9; i++) {
            const leafMesh = new THREE.Mesh(new THREE.SphereGeometry(0.75 * scale, 8, 8), leafMat);
            const angle = (i / 9) * Math.PI * 2; const rad = 0.6 * scale;
            const lx = Math.cos(angle) * rad; const lz = Math.sin(angle) * rad; const ly = 3.8 * scale + Math.sin(i) * 0.4 * scale;
            leafMesh.position.set(lx, ly, lz); leafMesh.scale.set(1.2, 0.4, 0.8); leafMesh.rotation.y = angle; plantGroup.add(leafMesh);
        }
        for (let i = 0; i < 5; i++) {
            const mat = i % 2 === 0 ? flowerMat1 : flowerMat2;
            const flowerMesh = new THREE.Mesh(new THREE.SphereGeometry(0.55 * scale, 10, 10), mat);
            const angle = (i / 5) * Math.PI * 2 + 0.3; const rad = 0.7 * scale;
            const fx = Math.cos(angle) * rad; const fz = Math.sin(angle) * rad; const fy = 4.7 * scale + (i % 3) * 0.4 * scale;
            flowerMesh.position.set(fx, fy, fz); plantGroup.add(flowerMesh);
        }
        return plantGroup;
    }

    function createMiniBookshelfGroup(targetHeight) {
        const bookGroup = new THREE.Group();
        const scale = (targetHeight || 14) / 22.0;
        const woodMat = new THREE.MeshStandardMaterial({ color: 0x4a321f, roughness: 0.6 });
        const goldMat = new THREE.MeshStandardMaterial({ color: 0xd4af37, metalness: 0.8, roughness: 0.3 });
        const bookColors = [0x8b0000, 0x1b4d3e, 0x1f305e, 0x704214, 0x4b0082, 0x800020];
        const shelfMesh = new THREE.Mesh(new THREE.BoxGeometry(5.5 * scale, 0.4 * scale, 2.2 * scale), woodMat);
        shelfMesh.position.y = 0.2 * scale; bookGroup.add(shelfMesh);
        const endGeo = new THREE.BoxGeometry(0.4 * scale, 4.2 * scale, 2.2 * scale);
        const leftEnd = new THREE.Mesh(endGeo, woodMat); leftEnd.position.set(-2.55 * scale, 2.1 * scale, 0); bookGroup.add(leftEnd);
        const rightEnd = new THREE.Mesh(endGeo, woodMat); rightEnd.position.set(2.55 * scale, 2.1 * scale, 0); bookGroup.add(rightEnd);
        let startX = -2.1 * scale;
        for (let i = 0; i < 5; i++) {
            const bWidth = 0.75 * scale; const bHeight = (3.2 + (i % 3) * 0.4) * scale; const bDepth = 1.9 * scale;
            const bMat = new THREE.MeshStandardMaterial({ color: bookColors[i % bookColors.length], roughness: 0.4 });
            const bookMesh = new THREE.Mesh(new THREE.BoxGeometry(bWidth, bHeight, bDepth), bMat);
            bookMesh.position.set(startX + bWidth / 2, bHeight / 2 + 0.4 * scale, 0);
            if (i === 4) { bookMesh.rotation.z = -0.15; bookMesh.position.x += 0.1 * scale; }
            bookGroup.add(bookMesh);
            const ribMesh = new THREE.Mesh(new THREE.BoxGeometry(bWidth * 1.02, 0.08 * scale, bDepth * 1.02), goldMat);
            ribMesh.position.set(bookMesh.position.x, bookMesh.position.y + bHeight * 0.25, 0); bookGroup.add(ribMesh);
            startX += bWidth + 0.05 * scale;
        }
        return bookGroup;
    }

    function createCandleHolderGroup(targetHeight, c1, c2, c3) {
        const group = new THREE.Group();
        const scale = (targetHeight || 14) / 22.0;
        const brassMat = new THREE.MeshStandardMaterial({ color: c1 || 0xc8a257, metalness: 0.85, roughness: 0.25 });
        const accentMat = new THREE.MeshStandardMaterial({ color: c2 || 0xe5c158, metalness: 0.9, roughness: 0.2 });
        const candleMat = new THREE.MeshStandardMaterial({ color: 0xfffcf5, roughness: 0.6 });
        const flameMat = new THREE.MeshStandardMaterial({ color: c3 || 0xff9900, emissive: c3 || 0xff8800, emissiveIntensity: 3.0, roughness: 0.1 });

        const base = new THREE.Mesh(new THREE.CylinderGeometry(1.8 * scale, 2.2 * scale, 0.6 * scale, 16), brassMat); base.position.y = 0.3 * scale; group.add(base);
        const baseRing = new THREE.Mesh(new THREE.TorusGeometry(1.9 * scale, 0.15 * scale, 8, 16), accentMat); baseRing.rotation.x = Math.PI/2; baseRing.position.y = 0.6 * scale; group.add(baseRing);
        const stem = new THREE.Mesh(new THREE.CylinderGeometry(0.35 * scale, 0.7 * scale, 6.0 * scale, 16), brassMat); stem.position.y = 3.6 * scale; group.add(stem);
        const cup = new THREE.Mesh(new THREE.CylinderGeometry(1.2 * scale, 0.6 * scale, 1.2 * scale, 16), accentMat); cup.position.y = 7.2 * scale; group.add(cup);
        const candle = new THREE.Mesh(new THREE.CylinderGeometry(0.7 * scale, 0.7 * scale, 4.5 * scale, 16), candleMat); candle.position.y = 9.8 * scale; group.add(candle);
        const wick = new THREE.Mesh(new THREE.CylinderGeometry(0.06 * scale, 0.06 * scale, 0.6 * scale, 8), new THREE.MeshBasicMaterial({ color: 0x111111 })); wick.position.y = 12.35 * scale; group.add(wick);
        const flame = new THREE.Mesh(new THREE.ConeGeometry(0.35 * scale, 1.1 * scale, 12), flameMat); flame.position.y = 13.0 * scale; group.add(flame);
        const flameLight = new THREE.PointLight(c3 || 0xffaa33, 2.5, 25 * scale, 1.5); flameLight.position.y = 13.0 * scale; group.add(flameLight);
        return group;
    }

    function createAbstractSculptureGroup(targetHeight, c1, c2) {
        const group = new THREE.Group();
        const scale = (targetHeight || 14) / 22.0;
        const baseMat = new THREE.MeshStandardMaterial({ color: c1 || 0x222222, roughness: 0.3 });
        const metalMat = new THREE.MeshStandardMaterial({ color: c2 || 0xd4af37, metalness: 0.9, roughness: 0.15 });

        const pedestal = new THREE.Mesh(new THREE.BoxGeometry(3.5 * scale, 2.5 * scale, 3.5 * scale), baseMat); pedestal.position.y = 1.25 * scale; group.add(pedestal);
        const knot = new THREE.Mesh(new THREE.TorusKnotGeometry(1.8 * scale, 0.45 * scale, 64, 16), metalMat); knot.position.y = 5.2 * scale; group.add(knot);
        return group;
    }

    function applySinglePieceTimberUVs(geometry, L, T, D) {
        geometry.computeVertexNormals();
        const pos = geometry.attributes.position;
        const norm = geometry.attributes.normal;
        const uv = geometry.attributes.uv;
        if (!pos || !uv) return;

        const depthVal = D || T;

        for (let i = 0; i < pos.count; i++) {
            const x = pos.getX(i);
            const y = pos.getY(i);
            const z = pos.getZ(i);

            let nx = norm ? Math.abs(norm.getX(i)) : 0;
            let ny = norm ? Math.abs(norm.getY(i)) : 0;
            let nz = norm ? Math.abs(norm.getZ(i)) : 1;

            let u, v;
            if (nz >= nx && nz >= ny) {
                // Front & Back Faces (XY plane)
                u = (x + L / 2) / L;
                v = (y + T / 2) / T;
            } else if (ny >= nx && ny >= nz) {
                // Top & Bottom Faces (XZ plane)
                u = (x + L / 2) / L;
                v = (z + depthVal / 2) / depthVal;
            } else {
                // Side & Miter Faces (ZY plane)
                u = (z + depthVal / 2) / depthVal;
                v = (y + T / 2) / T;
            }

            u = Math.max(0.001, Math.min(0.999, u));
            v = Math.max(0.001, Math.min(0.999, v));
            uv.setXY(i, u, v);
        }
        uv.needsUpdate = true;
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

        applySinglePieceTimberUVs(geom, L, T, D);

        return geom;
    }

    function buildProductFrame() {
        const width = <?php echo e($product->threeDTemplate->width); ?>;
        const height = <?php echo e($product->threeDTemplate->height); ?>;
        const depth = <?php echo e($product->threeDTemplate->depth); ?>;
        const thickness = <?php echo e($product->threeDTemplate->thickness); ?>;

        const innerW = <?php echo e($product->threeDTemplate->inner_width); ?>;
        const innerH = <?php echo e($product->threeDTemplate->inner_height); ?>;
        const innerD = <?php echo e($product->threeDTemplate->inner_depth); ?>;
        const innerB = <?php echo e($product->threeDTemplate->inner_border); ?>;

        const px = <?php echo e($product->threeDTemplate->pos_x); ?>;
        const py = <?php echo e($product->threeDTemplate->pos_y); ?>;

        const woodType = "<?php echo e($product->threeDTemplate->wood_type); ?>";

        const woodTextures = generateWoodTextures(woodType, renderer);
        const bScaleRaw = <?php echo e($product->threeDTemplate->bump_scale ?: 0.28); ?>;
        const bScale = bScaleRaw < 0.12 ? 0.28 : bScaleRaw;

        const hasTop = <?php echo e($product->threeDTemplate->has_top ? 'true' : 'false'); ?>;
        const hasBottom = <?php echo e($product->threeDTemplate->has_bottom ? 'true' : 'false'); ?>;
        const hasLeft = <?php echo e($product->threeDTemplate->has_left ? 'true' : 'false'); ?>;
        const hasRight = <?php echo e($product->threeDTemplate->has_right ? 'true' : 'false'); ?>;

        outerGroup = new THREE.Group();

        if(hasTop) {
            const mat = createPieceMaterial(woodTextures, bScale, 'top');
            const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasLeft, hasRight), mat);
            mesh.position.y = height/2 - thickness/2;
            outerGroup.add(mesh);
        }
        if(hasBottom) {
            const mat = createPieceMaterial(woodTextures, bScale, 'bottom');
            const mesh = new THREE.Mesh(createMiteredFramePiece(width, thickness, depth, hasRight, hasLeft), mat);
            mesh.rotation.z = Math.PI;
            mesh.position.y = -height/2 + thickness/2;
            outerGroup.add(mesh);
        }
        if(hasLeft) {
            const mat = createPieceMaterial(woodTextures, bScale, 'left');
            const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasBottom, hasTop), mat);
            mesh.rotation.z = Math.PI / 2;
            mesh.position.x = -width/2 + thickness/2;
            outerGroup.add(mesh);
        }
        if(hasRight) {
            const mat = createPieceMaterial(woodTextures, bScale, 'right');
            const mesh = new THREE.Mesh(createMiteredFramePiece(height, thickness, depth, hasTop, hasBottom), mat);
            mesh.rotation.z = -Math.PI / 2;
            mesh.position.x = width/2 - thickness/2;
            outerGroup.add(mesh);
        }

        currentModelGroup.add(outerGroup);

        customRotatingFrame = new THREE.Group();
        customRotatingFrame.position.set(px, py, 0);

        const matInTop = createPieceMaterial(woodTextures, bScale, 'inner_top');
        const topIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), matInTop);
        topIn.position.y = innerH/2 - innerB/2;
        customRotatingFrame.add(topIn);

        const matInBot = createPieceMaterial(woodTextures, bScale, 'inner_bottom');
        const botIn = new THREE.Mesh(createMiteredFramePiece(innerW, innerB, innerD, true, true), matInBot);
        botIn.rotation.z = Math.PI;
        botIn.position.y = -innerH/2 + innerB/2;
        customRotatingFrame.add(botIn);

        const matInLeft = createPieceMaterial(woodTextures, bScale, 'inner_left');
        const leftIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), matInLeft);
        leftIn.rotation.z = Math.PI / 2;
        leftIn.position.x = -innerW/2 + innerB/2;
        customRotatingFrame.add(leftIn);

        const matInRight = createPieceMaterial(woodTextures, bScale, 'inner_right');
        const rightIn = new THREE.Mesh(createMiteredFramePiece(innerH, innerB, innerD, true, true), matInRight);
        rightIn.rotation.z = -Math.PI / 2;
        rightIn.position.x = innerW/2 - innerB/2;
        customRotatingFrame.add(rightIn);

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

        const matBacking = createPieceMaterial(woodTextures, bScale, 'backing');
        const backingGeom = new THREE.BoxGeometry(photoW, photoH, 0.08);
        const backing = new THREE.Mesh(backingGeom, matBacking);
        customRotatingFrame.add(backing);

        const pinMat = new THREE.MeshStandardMaterial({ color: 0xcccccc, metalness: 0.9, roughness: 0.2 });
        
        const innerEdgeTop = py + (innerH / 2);
        const outerTargetTop = (height / 2) - (thickness / 2);
        const lenTop = Math.max(0.15, outerTargetTop - innerEdgeTop);
        const localYTop = (innerH / 2) + (lenTop / 2);

        const pinTopGeo = new THREE.CylinderGeometry(0.18, 0.18, lenTop, 16);
        const pinTop = new THREE.Mesh(pinTopGeo, pinMat);
        pinTop.position.set(0, localYTop, 0);

        const innerEdgeBot = py - (innerH / 2);
        const outerTargetBot = -(height / 2) + (thickness / 2);
        const lenBot = Math.max(0.15, innerEdgeBot - outerTargetBot);
        const localYBot = -(innerH / 2) - (lenBot / 2);

        const pinBotGeo = new THREE.CylinderGeometry(0.18, 0.18, lenBot, 16);
        const pinBottom = new THREE.Mesh(pinBotGeo, pinMat);
        pinBottom.position.set(0, localYBot, 0);

        customRotatingFrame.add(pinTop, pinBottom);
        currentModelGroup.add(customRotatingFrame);

        // Render 3D Accessory / Object
        const hasAccessory = <?php echo e($product->threeDTemplate->has_accessory ? 'true' : 'false'); ?>;
        const accessoryType = "<?php echo e($product->threeDTemplate->accessory_type ?? 'street_lamp'); ?>";
        const accessoryPos = "<?php echo e($product->threeDTemplate->accessory_position ?? 'right'); ?>";
        const accOffsetX = <?php echo e($product->threeDTemplate->accessory_offset_x ?? 0); ?>;
        const accOffsetY = <?php echo e($product->threeDTemplate->accessory_offset_y ?? 0); ?>;
        const accOffsetZ = <?php echo e($product->threeDTemplate->accessory_offset_z ?? 0); ?>;
        const accScale = <?php echo e($product->threeDTemplate->accessory_scale ?? 1.0); ?>;

        if (hasAccessory) {
            const targetH = Math.min(height * 0.65, 18) * accScale;
            let accGroup = null;

            if (accessoryType === 'street_lamp') {
                accGroup = createStreetLampGroup(targetH);
            } else if (accessoryType === 'wooden_clock') {
                accGroup = createWoodenClockGroup(targetH);
            } else if (accessoryType === 'flower_vase') {
                accGroup = createFlowerVaseGroup(targetH);
            } else if (accessoryType === 'mini_bookshelf') {
                accGroup = createMiniBookshelfGroup(targetH);
            } else if (accessoryType === 'candle_holder') {
                accGroup = createCandleHolderGroup(targetH);
            } else if (accessoryType === 'abstract_sculpture') {
                accGroup = createAbstractSculptureGroup(targetH);
            }

            if (accGroup) {
                const bottomBoardY = -height/2 + thickness;
                let posX = 0;
                if (accessoryPos === 'right') {
                    posX = width/2 - thickness * 2.2 + accOffsetX;
                } else if (accessoryPos === 'left') {
                    posX = -width/2 + thickness * 2.2 + accOffsetX;
                } else {
                    posX = accOffsetX;
                }

                accGroup.position.set(posX, bottomBoardY + accOffsetY, (depth * 0.1) + accOffsetZ);
                currentModelGroup.add(accGroup);
            }
        }

        if (typeof attachInnerFrameDragController === 'function') {
            attachInnerFrameDragController(renderer, camera, controls, customRotatingFrame);
        }
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
<?php endif; ?>

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
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views\products\show.blade.php ENDPATH**/ ?>