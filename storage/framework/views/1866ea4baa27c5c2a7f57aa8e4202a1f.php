<?php $__env->startSection('header', 'Ürün Yönetimi'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Ürün Listesi</h3>
            <p class="text-xs text-gray-500 mt-1">Mağazadaki tüm aktif ve pasif ürünleri yönetin.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="<?php echo e(route('seo.sitemap')); ?>" target="_blank" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs transition flex items-center gap-1.5 border border-gray-200">
                <i class="fa-solid fa-sitemap text-amber-700"></i> sitemap.xml
            </a>
            <a href="<?php echo e(route('seo.urunler_xml')); ?>" target="_blank" class="py-2 px-3 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold rounded-lg text-xs transition flex items-center gap-1.5 border border-amber-200">
                <i class="fa-solid fa-file-code text-amber-700"></i> urunler.xml
            </a>
            <a href="<?php echo e(route('admin.products.create')); ?>" class="py-2 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold rounded-lg text-xs transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-plus"></i> Yeni Ürün Ekle
            </a>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="w-full text-left border-collapse responsive-stack" style="min-width: 600px">
            <thead>
                <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <th class="pb-3 w-16 text-center">Görsel</th>
                    <th class="pb-3">Ürün Adı</th>
                    <th class="pb-3">Kategori</th>
                    <th class="pb-3">3D Model Şablonu</th>
                    <th class="pb-3 text-right">Fiyat</th>
                    <th class="pb-3 text-center w-20">Stok</th>
                    <th class="pb-3 text-center w-20">Durum</th>
                    <th class="pb-3 w-32 text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="py-3.5 text-center" data-label="Görsel">
                            <div class="w-12 h-14 bg-gray-50 rounded-lg border border-gray-150 overflow-hidden flex items-center justify-center p-1">
                                <img src="<?php echo e($product->image ?: '/cerceve.png'); ?>" class="max-w-full max-h-full object-contain" alt="product">
                            </div>
                        </td>
                        <td class="py-3.5" data-label="Ürün">
                            <div class="font-bold text-gray-800"><?php echo e($product->name); ?></div>
                            <div class="text-[11px] font-mono text-gray-400 flex items-center gap-1 mt-0.5">
                                <a href="<?php echo e($product->url); ?>" target="_blank" class="hover:text-brand hover:underline flex items-center gap-0.5">
                                    /urun/<?php echo e($product->slug ?: $product->id); ?>

                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                </a>
                            </div>
                            <?php if($product->discount_percent > 0): ?>
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 font-extrabold text-[10px] rounded-full">%<?php echo e($product->discount_percent); ?> İNDİRİMLİ</span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5" data-label="Kategori">
                            <span class="px-2.5 py-1 bg-stone-100 text-stone-700 rounded-md font-semibold text-xs"><?php echo e($product->category->name ?? 'Kategorisiz'); ?></span>
                        </td>
                        <td class="py-3.5" data-label="3D Şablon">
                            <?php if($product->threeDTemplate): ?>
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-800 rounded-md font-bold text-xs border border-amber-200/50 flex items-center gap-1.5 w-max">
                                    <i class="fa-solid fa-cube text-amber-600"></i> <?php echo e($product->threeDTemplate->name); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-xs text-gray-400 font-semibold"><i class="fa-solid fa-ban mr-1"></i> Yok (Sadece 2D)</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 text-right font-bold text-gray-900" data-label="Fiyat">
                            <?php if($product->discount_percent > 0): ?>
                                <div class="text-xs text-gray-400 line-through"><?php echo e(number_format($product->original_price, 2, ',', '.')); ?> TL</div>
                            <?php endif; ?>
                            <div class="text-[#C87A53]"><?php echo e(number_format($product->price, 2, ',', '.')); ?> TL</div>
                        </td>
                        <td class="py-3.5 text-center font-semibold text-gray-600" data-label="Stok"><?php echo e($product->stock); ?></td>
                        <td class="py-3.5 text-center" data-label="Durum">
                            <?php if($product->is_active): ?>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-bold text-[10px]">Aktif</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-gray-150 text-gray-500 rounded-full font-bold text-[10px]">Pasif</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 text-right space-x-2 whitespace-nowrap" data-label="İşlem">
                            <a href="<?php echo e(route('admin.products.edit', $product->id)); ?>" class="text-blue-600 hover:text-blue-800 font-bold text-xs"><i class="fa-solid fa-edit"></i> Düzenle</a>
                            <form action="<?php echo e(route('admin.products.destroy', $product->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs"><i class="fa-solid fa-trash"></i> Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="py-12 text-center text-gray-500">Mağazaya henüz ürün eklenmemiş.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($products->hasPages()): ?>
        <div class="mt-6">
            <?php echo e($products->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/admin/products/index.blade.php ENDPATH**/ ?>