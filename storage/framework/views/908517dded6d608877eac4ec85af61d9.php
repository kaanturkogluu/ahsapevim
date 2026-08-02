<?php $__env->startSection('header', 'Sipariş Yönetimi'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Sipariş Listesi</h3>
            <p class="text-xs text-gray-500 mt-1">Müşterileriniz tarafından verilen tüm siparişleri ve özelleştirilmiş fotoğrafları inceleyin.</p>
        </div>

        <!-- Filter & Search Form -->
        <form action="<?php echo e(route('admin.orders.index')); ?>" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select name="status" onchange="this.form.submit()" class="text-xs border-gray-300 rounded-lg p-2 border focus:border-brand focus:ring-0 outline-none">
                <option value="">Tüm Durumlar</option>
                <option value="paid" <?php echo e(request('status') === 'paid' ? 'selected' : ''); ?>>Ödendi / Hazırlanıyor</option>
                <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Ödeme Bekliyor</option>
                <option value="shipped" <?php echo e(request('status') === 'shipped' ? 'selected' : ''); ?>>Kargolandı</option>
                <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Tamamlandı</option>
                <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>İptal Edildi</option>
            </select>

            <div class="flex items-center">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Sipariş No, Ad veya Telefon..." class="text-xs border-gray-300 rounded-l-lg p-2 border focus:border-brand focus:ring-0 outline-none">
                <button type="submit" class="bg-[#C87A53] hover:bg-[#A65F38] text-white px-3 py-2 text-xs rounded-r-lg font-bold transition">
                    <i class="fa-solid fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-xs font-bold mb-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <th class="pb-3 w-20 text-center">Sipariş #</th>
                    <th class="pb-3">Müşteri Bilgileri</th>
                    <th class="pb-3">Ürünler / Fotoğraflar</th>
                    <th class="pb-3 text-center">Sipariş Tarihi</th>
                    <th class="pb-3 text-right">Toplam Tutar</th>
                    <th class="pb-3 text-center">Durum</th>
                    <th class="pb-3 w-24 text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="py-4 text-center font-black text-gray-800">
                            #<?php echo e($order->id); ?>

                        </td>
                        <td class="py-4">
                            <div class="font-bold text-gray-800"><?php echo e($order->name); ?></div>
                            <div class="text-xs text-gray-500 font-mono mt-0.5"><?php echo e($order->phone); ?></div>
                            <div class="text-[11px] text-gray-400 truncate max-w-[180px]"><?php echo e($order->email); ?></div>
                        </td>
                        <td class="py-4">
                            <div class="text-xs font-semibold text-gray-700">
                                <?php echo e($order->items->count()); ?> Kalem Ürün
                            </div>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $hasFront = !empty($item->features['front_image']);
                                        $hasBack = !empty($item->features['back_image']);
                                        $hasSingle = !empty($item->features['custom_image']);
                                    ?>
                                    <?php if($hasFront || $hasBack || $hasSingle): ?>
                                        <span class="px-2 py-0.5 bg-orange-100 text-[#C87A53] rounded text-[10px] font-bold">
                                            <i class="fa-solid fa-camera mr-0.5"></i> Çift Yüzlü Fotoğraf
                                        </span>
                                        <?php break; ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </td>
                        <td class="py-4 text-center text-xs text-gray-500">
                            <?php echo e($order->created_at->format('d.m.Y H:i')); ?>

                        </td>
                        <td class="py-4 text-right font-black text-[#C87A53]">
                            ₺<?php echo e(number_format($order->total_amount, 2, ',', '.')); ?>

                        </td>
                        <td class="py-4 text-center">
                            <?php if($order->status === 'paid' || $order->status === 'preparing'): ?>
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full font-bold text-[10px]">Ödendi / Hazırlanıyor</span>
                            <?php elseif($order->status === 'shipped'): ?>
                                <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full font-bold text-[10px]">Kargolandı</span>
                            <?php elseif($order->status === 'completed'): ?>
                                <span class="px-2.5 py-1 bg-purple-100 text-purple-700 rounded-full font-bold text-[10px]">Tamamlandı</span>
                            <?php elseif($order->status === 'pending'): ?>
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full font-bold text-[10px]">Beklemede</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-full font-bold text-[10px]">İptal / Başarısız</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 text-right">
                            <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="py-1.5 px-3 bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold text-xs rounded-lg transition inline-flex items-center gap-1">
                                <i class="fa-solid fa-eye"></i> İncele
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-500">Henüz veritabanında kaydedilmiş bir sipariş bulunmamaktadır.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <?php echo e($orders->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>