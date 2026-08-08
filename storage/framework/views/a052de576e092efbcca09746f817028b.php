<?php $__env->startSection('title', 'El Yapımı Ürünler — AhşapEvim | Masif Ahşap Çerçeve Koleksiyonu'); ?>

<?php $__env->startSection('content'); ?>


<div class="relative w-full overflow-hidden bg-transparent">
    <picture class="w-full h-auto block">
        <?php if(file_exists(public_path('images/hero-banner-desktop.png'))): ?>
            <source media="(min-width: 768px)" srcset="<?php echo e(url('/images/hero-banner-desktop.png')); ?>">
        <?php endif; ?>
        
        <?php if(file_exists(public_path('images/hero-banner-mobile.png'))): ?>
            <img src="<?php echo e(url('/images/hero-banner-mobile.png')); ?>" 
                 alt="AhşapEvim — Masif Ahşap El İşçiliği" 
                 class="w-full h-auto block">
        <?php else: ?>
            <img src="<?php echo e(url('/images/hero-banner.png')); ?>" 
                 alt="AhşapEvim — Masif Ahşap El İşçiliği" 
                 class="w-full h-auto block">
        <?php endif; ?>
    </picture>
</div>


<div class="min-h-screen pb-16 pt-8">
    <div class="container mx-auto px-4">

        
        <div class="w-full">

            
            <div class="flex justify-between items-center mb-5 bg-white px-5 py-3.5 rounded-2xl shadow-sm">
                <div class="flex items-center gap-2.5">
                    <span class="text-base font-extrabold text-gray-900">
                        <?php echo e(request('category') ? ($categories->where('slug', request('category'))->first()->name ?? 'Ürünler') : 'Tüm El Yapımı Ürünler'); ?>

                    </span>
                    <span class="text-xs bg-brand/10 text-brand font-bold px-2 py-0.5 rounded-full"><?php echo e($products->total()); ?> ürün</span>
                </div>
                <div class="hidden sm:flex items-center gap-1.5 text-xs text-gray-500 font-semibold">
                    <i class="fa-solid fa-clock text-brand"></i> Sipariş anında üretim başlar
                </div>
            </div>

            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $allImages = array_values(array_filter(array_merge([$product->image], $product->gallery_urls)));
                    ?>

                    
                    <a href="<?php echo e(url('/urun/' . $product->id)); ?>" class="product-card group flex flex-col rounded-2xl overflow-hidden transition-all duration-300 relative">
                        
                        
                        <button type="button" class="absolute top-3 right-3 z-30 w-9 h-9 bg-white/95 backdrop-blur-sm rounded-full shadow-md border border-gray-100 flex items-center justify-center hover:scale-110 transition duration-200" onclick="event.preventDefault(); event.stopPropagation(); toggleFavorite(<?php echo e($product->id); ?>, this);" title="Favorilere Ekle">
                            <i class="<?php echo e($product->isFavoritedBy() ? 'fa-solid fa-heart text-red-500 text-base drop-shadow-sm scale-110' : 'fa-regular fa-heart text-gray-500 text-base hover:text-red-500'); ?>"></i>
                        </button>

                        
                        <div class="relative pt-[115%] w-full bg-[#fdf6ec] overflow-hidden"
                             onmousemove="hoverCardImage(event, this)"
                             onmouseleave="resetCardImage(this)">
                            <img src="<?php echo e($allImages[0]); ?>" alt="<?php echo e($product->name); ?>"
                                 class="card-preview-img absolute inset-0 w-full h-full object-contain p-3 transition-all duration-300 group-hover:scale-105"
                                 data-images="<?php echo e(json_encode($allImages)); ?>"
                                 data-default="<?php echo e($allImages[0]); ?>">
                            
                            <?php if(count($allImages) > 1): ?>
                                <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                    <?php $__currentLoopData = $allImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="dot-indicator w-1.5 h-1.5 rounded-full <?php echo e($idx === 0 ? 'bg-brand' : 'bg-gray-300'); ?> transition-colors"></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>

                            
                            <div class="absolute top-3 left-3 bg-brand/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-full tracking-wide">
                                El Yapımı
                            </div>

                            <?php if($product->discount_percent > 0): ?>
                                <div class="absolute top-3 right-12 bg-red-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow z-10">
                                    %<?php echo e($product->discount_percent); ?> İndirim
                                </div>
                            <?php endif; ?>

                            <?php if($product->stock > 0): ?>
                                <div class="absolute bottom-0 left-0 bg-green-600/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-tr-xl">
                                    ✓ Stokta
                                </div>
                            <?php endif; ?>
                        </div>

                        
                        <div class="p-3.5 flex flex-col flex-grow">
                            <div class="text-[12px] text-brand font-bold uppercase tracking-wider mb-1">AhşapEvim Atölyesi</div>
                            <div class="text-[13px] font-bold text-gray-900 leading-snug mb-2 h-10 overflow-hidden">
                                <?php echo e($product->name); ?>

                            </div>

                            
                            <div class="mt-auto flex items-end justify-between">
                                <div>
                                    <div class="text-brand font-extrabold text-lg leading-none"><?php echo e(number_format($product->price, 2, ',', '.')); ?> ₺</div>
                                    <?php if($product->original_price > $product->price): ?>
                                        <div class="text-xs text-gray-400 line-through mt-0.5"><?php echo e(number_format($product->original_price, 2, ',', '.')); ?> ₺</div>
                                    <?php endif; ?>
                                </div>
                                <div class="w-8 h-8 rounded-xl bg-brand/10 group-hover:bg-brand flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-cart-plus text-brand group-hover:text-white text-sm transition-colors"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full py-16 text-center">
                        <i class="fa-solid fa-tree text-6xl text-brand/35 mb-4 block"></i>
                        <div class="text-gray-500 font-medium">Bu kategoride henüz ürün bulunmamaktadır.</div>
                        <a href="<?php echo e(url('/urunler')); ?>" class="inline-flex items-center gap-2 mt-4 text-sm text-brand font-bold hover:underline">
                            <i class="fa-solid fa-arrow-left"></i> Tüm ürünlere dön
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="mt-8">
                <?php echo e($products->links()); ?>

            </div>
        </div>
    </div>
</div>

<script>
function hoverCardImage(e, container) {
    const img = container.querySelector('.card-preview-img');
    if (!img) return;
    const images = JSON.parse(img.dataset.images || '[]');
    if (images.length <= 1) return;
    const rect = container.getBoundingClientRect();
    const x = Math.max(0, e.clientX - rect.left);
    const index = Math.min(Math.floor((x / rect.width) * images.length), images.length - 1);
    if (images[index] && img.src !== images[index]) {
        img.src = images[index];
        const dots = container.querySelectorAll('.dot-indicator');
        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-brand', i === index);
            dot.classList.toggle('bg-gray-300', i !== index);
        });
    }
}

function resetCardImage(container) {
    const img = container.querySelector('.card-preview-img');
    if (!img) return;
    img.src = img.dataset.default;
    const dots = container.querySelectorAll('.dot-indicator');
    dots.forEach((dot, i) => {
        dot.classList.toggle('bg-brand', i === 0);
        dot.classList.toggle('bg-gray-300', i !== 0);
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/products/index.blade.php ENDPATH**/ ?>