<?php $__env->startSection('header', 'Ürünü Düzenle'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm max-w-4xl">
    <div class="mb-6 pb-4 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Ürün Bilgileri</h3>
            <p class="text-xs text-gray-500 mt-1">Düzenlenen ürünün temel, indirim, galeri ve 3D özelliklerini güncelleyin.</p>
        </div>
        <a href="<?php echo e(route('admin.products.index')); ?>" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Geri Dön
        </a>
    </div>

    <form action="<?php echo e(route('admin.products.update', $product->id)); ?>" method="POST" enctype="multipart/form-data" onsubmit="preventSpamSubmit(this)">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Left Side: Basic Info & Pricing -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ürün Adı *</label>
                    <input type="text" name="name" id="productNameInput" required value="<?php echo e(old('name', $product->name)); ?>" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: 360 Dönen Masif Çerçeve" oninput="autoGenerateSlug(this.value)">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center justify-between">
                        <span>URL Adresi (SEO Slug) *</span>
                        <a href="<?php echo e($product->url); ?>" target="_blank" class="text-[11px] font-bold text-[#C87A53] hover:underline">
                            Ürünü Sitede Gör <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-0.5"></i>
                        </a>
                    </label>
                    <div class="flex items-center">
                        <span class="bg-gray-100 text-gray-500 text-xs px-3 py-2.5 border border-r-0 border-gray-300 rounded-l-lg font-mono shrink-0">/urun/</span>
                        <input type="text" name="slug" id="productSlugInput" value="<?php echo e(old('slug', $product->slug)); ?>" class="w-full text-sm border-gray-300 rounded-r-lg p-2.5 border focus:border-brand focus:ring-0 outline-none font-mono text-gray-700" placeholder="360-donen-masif-cerceve" oninput="isSlugManuallyEdited = true">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori *</label>
                        <select name="category_id" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none bg-white">
                            <option value="">Seçin...</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id', $product->category_id) == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Stok Adedi *</label>
                        <input type="number" name="stock" required value="<?php echo e(old('stock', $product->stock)); ?>" min="0" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Sıralama (Sıra)</label>
                        <input type="number" name="sort_order" value="<?php echo e(old('sort_order', $product->sort_order)); ?>" min="1" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" title="Ürünün sitedeki sıralama pozisyonu">
                    </div>
                </div>

                <?php
                    $isDiscounted = old('has_discount', $product->original_price > $product->price);
                    $normalPriceVal = $isDiscounted ? $product->original_price : $product->price;
                    $discountedPriceVal = $isDiscounted ? $product->price : '';
                ?>

                <!-- Price & Discount Section -->
                <div class="p-4 bg-red-50/40 border border-red-200/50 rounded-xl space-y-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Normal Fiyat (TL) *</label>
                        <input type="number" id="normalPrice" name="price" required step="0.01" value="<?php echo e(old('price', $normalPriceVal)); ?>" min="0" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: 500.00" oninput="calculateDiscount()">
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="has_discount" id="hasDiscount" value="1" <?php echo e($isDiscounted ? 'checked' : ''); ?> onchange="toggleDiscountBlock()" class="rounded text-red-600 focus:ring-red-500 w-4 h-4 cursor-pointer">
                        <label for="hasDiscount" class="text-sm font-bold text-red-700 cursor-pointer select-none">Bu Üründe İndirim Var</label>
                    </div>

                    <div id="discountBlock" class="<?php echo e($isDiscounted ? '' : 'hidden'); ?> space-y-2 pt-1 border-t border-red-100">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-semibold text-gray-700">İndirimli Satış Fiyatı (TL) *</label>
                            <span id="discountBadge" class="hidden text-xs bg-red-600 text-white font-extrabold px-2 py-0.5 rounded-full"></span>
                        </div>
                        <input type="number" id="discountedPrice" name="discounted_price" step="0.01" value="<?php echo e(old('discounted_price', $discountedPriceVal)); ?>" min="0" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: 350.00" oninput="calculateDiscount()">
                        <p class="text-[11px] text-gray-500">Müşteriye <strong>Normal Fiyat</strong> çizili olarak, <strong>İndirimli Fiyat</strong> ve indirim oranı rozeti olarak gösterilecektir.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ahşap Rengi/Türü</label>
                        <input type="text" name="color" value="<?php echo e(old('color', $product->features['color'] ?? 'Ceviz')); ?>" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: Masif Meşe">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ölçü/Boyut</label>
                        <input type="text" name="size" value="<?php echo e(old('size', $product->features['size'] ?? '20x25 cm')); ?>" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: 15x21 cm">
                    </div>
                </div>
            </div>

            <!-- Right Side: Media & 3D Settings -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Görseli Değiştir (Ana Görsel)</label>
                    <input type="file" name="image" class="w-full text-sm border-gray-300 rounded-lg p-2 border focus:border-brand focus:ring-0 outline-none bg-gray-50">
                    
                    <?php if($product->image): ?>
                        <div class="mt-2 flex items-center gap-3 bg-gray-50 p-2 rounded-lg border border-gray-150 w-fit">
                            <img src="<?php echo e($product->image); ?>" class="h-12 w-10 object-contain" alt="old image">
                            <span class="text-xs text-gray-500 font-semibold">Mevcut ana görsel saklanıyor.</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ek Ürün Görselleri (Galeri Ekle/Yönet)</label>
                    <input type="file" name="gallery[]" multiple accept="image/*" class="w-full text-sm border-gray-300 rounded-lg p-2 border focus:border-brand focus:ring-0 outline-none bg-gray-50">
                    
                    <?php if(isset($product->features['images']) && is_array($product->features['images']) && count($product->features['images']) > 0): ?>
                        <div class="mt-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Mevcut Galeri Görselleri (Silmek istediklerinizi işaretleyin):</label>
                            <div class="grid grid-cols-4 gap-2">
                                <?php $__currentLoopData = $product->features['images']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gImg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="relative group border border-gray-200 rounded-lg p-1 bg-white flex flex-col items-center">
                                        <img src="<?php echo e(str_starts_with($gImg, 'http') ? $gImg : url($gImg)); ?>" class="h-16 w-full object-contain rounded" alt="gallery image">
                                        <label class="mt-1 flex items-center gap-1 text-[11px] text-red-600 font-bold cursor-pointer">
                                            <input type="checkbox" name="remove_gallery[]" value="<?php echo e($gImg); ?>" class="rounded text-red-600 focus:ring-red-500">
                                            Sil
                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fa-brands fa-youtube text-red-600 text-base"></i> YouTube Tanıtım Video Linki (Opsiyonel)
                    </label>
                    <input type="url" name="youtube_url" value="<?php echo e(old('youtube_url', $product->features['youtube_url'] ?? '')); ?>" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: https://www.youtube.com/watch?v=XXXXXX">
                    <p class="text-[10px] text-gray-500 mt-1">Eklenirse ürün detay galerisine YouTube video butonu eklenir.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fa-brands fa-instagram text-pink-600 text-base"></i> Instagram Video / Reel Linki (Opsiyonel)
                    </label>
                    <input type="url" name="instagram_url" value="<?php echo e(old('instagram_url', $product->features['instagram_url'] ?? '')); ?>" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: https://www.instagram.com/reel/CsqVN6MuKfV/">
                    <p class="text-[10px] text-gray-500 mt-1">Eklenirse ürün detay galerisinde Instagram Reel rozeti ve pop-up oynatıcı gösterilir.</p>
                </div>

                <!-- 3D Model Entegrasyonu (Arka plana alındı / Pasif) -->
                <div class="hidden bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h4 class="text-sm font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-cube text-brand"></i> 3D Model Entegrasyonu (Şablon)
                    </h4>
                    
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Ürün 3D Şablonu</label>
                        <select name="three_d_template_id" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none bg-white">
                            <option value="">-- Şablon Seçimi Yok (Pasif) --</option>
                            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tpl->id); ?>" <?php echo e(old('three_d_template_id', $product->three_d_template_id) == $tpl->id ? 'selected' : ''); ?>><?php echo e($tpl->name); ?> (<?php echo e($tpl->wood_type); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Ürün Açıklaması</label>
            <textarea name="description" rows="5" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Ürünün ahşap kalitesi, özellikleri ve el işçiliği hakkında detaylı bilgi yazın."><?php echo e(old('description', $product->description)); ?></textarea>
        </div>

        <div class="flex items-center gap-2 mb-6">
            <input type="checkbox" name="is_active" id="isActive" value="1" <?php echo e(old('is_active', $product->is_active) ? 'checked' : ''); ?> class="rounded text-[#C87A53] focus:ring-[#C87A53] w-4 h-4">
            <label for="isActive" class="text-sm font-semibold text-gray-700 cursor-pointer">Bu ürünü mağazada hemen satışa aç (Aktif)</label>
        </div>

        <button type="submit" class="py-3 px-8 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-lg text-sm transition">Değişiklikleri Kaydet</button>
    </form>
</div>

<script>
let isSlugManuallyEdited = false;

function autoGenerateSlug(title) {
    if (isSlugManuallyEdited) return;
    
    let slug = title.toLowerCase()
        .replace(/ğ/g, 'g')
        .replace(/ü/g, 'u')
        .replace(/ş/g, 's')
        .replace(/ı/g, 'i')
        .replace(/ö/g, 'o')
        .replace(/ç/g, 'c')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
        
    document.getElementById('productSlugInput').value = slug;
}

function toggleDiscountBlock() {
    const hasDiscount = document.getElementById('hasDiscount').checked;
    const block = document.getElementById('discountBlock');
    if (hasDiscount) {
        block.classList.remove('hidden');
    } else {
        block.classList.add('hidden');
    }
    calculateDiscount();
}

function calculateDiscount() {
    const hasDiscount = document.getElementById('hasDiscount').checked;
    const normalPrice = parseFloat(document.getElementById('normalPrice').value) || 0;
    const discountedPrice = parseFloat(document.getElementById('discountedPrice').value) || 0;
    const badge = document.getElementById('discountBadge');

    if (hasDiscount && normalPrice > 0 && discountedPrice > 0 && discountedPrice < normalPrice) {
        const percent = Math.round((1 - (discountedPrice / normalPrice)) * 100);
        badge.innerText = '%' + percent + ' İNDİRİM';
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', calculateDiscount);
function preventSpamSubmit(form) {
    const btn = form.querySelector('button[type="submit"]');
    if (btn && !btn.disabled) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Kaydediliyor...';
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u111121823/domains/ahsapevimmanisa.com/public_html/resources/views/admin/products/edit.blade.php ENDPATH**/ ?>