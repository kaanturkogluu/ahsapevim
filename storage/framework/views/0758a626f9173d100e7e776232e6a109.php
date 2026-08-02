<?php $__env->startSection('header', 'Sipariş Detayı #' . $order->id); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm max-w-5xl">
    <!-- Header -->
    <div class="mb-6 pb-4 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Sipariş #<?php echo e($order->id); ?> Detayı</h3>
            <p class="text-xs text-gray-500 mt-1">Sipariş tarihi: <?php echo e($order->created_at->format('d.m.Y H:i')); ?></p>
        </div>
        <a href="<?php echo e(route('admin.orders.index')); ?>" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Sipariş Listesine Dön
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-xs font-bold mb-6">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Müşteri Bilgileri -->
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <i class="fa-solid fa-user text-[#C87A53]"></i> Müşteri Bilgileri
            </h4>
            <div class="text-xs space-y-1.5 text-gray-700">
                <p><strong>Ad Soyad:</strong> <?php echo e($order->name); ?></p>
                <p><strong>E-Posta:</strong> <?php echo e($order->email); ?></p>
                <p><strong>Telefon:</strong> <?php echo e($order->phone); ?></p>
                <?php if($order->identity_number): ?>
                    <p><strong>T.C. Kimlik No:</strong> <?php echo e($order->identity_number); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Teslimat Adresi -->
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <i class="fa-solid fa-truck text-blue-600"></i> Teslimat Adresi
            </h4>
            <div class="text-xs text-gray-700 space-y-1.5">
                <p class="whitespace-pre-line"><?php echo e($order->address); ?></p>
                <p><strong>Şehir / İlçe:</strong> <?php echo e($order->city ?: 'Manisa'); ?> / <?php echo e($order->district ?: 'Merkez'); ?></p>
            </div>
        </div>

        <!-- Durum Güncelleme Formu -->
        <div class="bg-orange-50/50 p-4 rounded-xl border border-orange-100">
            <h4 class="text-xs font-bold text-[#C87A53] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <i class="fa-solid fa-sliders"></i> Sipariş Durumu Güncelle
            </h4>
            <form action="<?php echo e(route('admin.orders.update', $order->id)); ?>" method="POST" class="space-y-3">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <select name="status" class="w-full text-xs font-bold border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none">
                    <option value="paid" <?php echo e($order->status === 'paid' ? 'selected' : ''); ?>>Ödendi / Hazırlanıyor</option>
                    <option value="pending" <?php echo e($order->status === 'pending' ? 'selected' : ''); ?>>Ödeme Bekliyor</option>
                    <option value="shipped" <?php echo e($order->status === 'shipped' ? 'selected' : ''); ?>>Kargolandı</option>
                    <option value="completed" <?php echo e($order->status === 'completed' ? 'selected' : ''); ?>>Tamamlandı</option>
                    <option value="cancelled" <?php echo e($order->status === 'cancelled' ? 'selected' : ''); ?>>İptal Edildi</option>
                    <option value="failed" <?php echo e($order->status === 'failed' ? 'selected' : ''); ?>>Başarısız</option>
                </select>
                <button type="submit" class="w-full py-2 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-lg text-xs transition shadow-sm">
                    <i class="fa-solid fa-save mr-1"></i> Durumu Güncelle
                </button>
            </form>
        </div>
    </div>

    <!-- Sipariş Kalemleri ve Yüklenen Fotoğraflar -->
    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200/80 mb-6">
        <h4 class="text-sm font-extrabold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-boxes-packing text-[#C87A53]"></i> Sipariş Edilen Ürünler ve Fotoğraflar
        </h4>

        <div class="divide-y divide-gray-200/80">
            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="py-4 first:pt-0 last:pb-0 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <?php if($item->product && $item->product->image): ?>
                            <img src="<?php echo e(url($item->product->image)); ?>" class="w-16 h-16 object-cover rounded-xl border border-gray-200 shrink-0">
                        <?php else: ?>
                            <div class="w-16 h-16 bg-amber-100 rounded-xl border border-amber-200 flex items-center justify-center text-amber-700 text-2xl shrink-0">
                                <i class="fa-solid fa-cube"></i>
                            </div>
                        <?php endif; ?>

                        <div>
                            <h5 class="text-sm font-bold text-gray-800"><?php echo e($item->product ? $item->product->name : 'Ahşap Ürün'); ?></h5>
                            <div class="text-xs text-gray-500 mt-1">
                                Adet: <strong class="text-gray-800"><?php echo e($item->quantity); ?></strong> × ₺<?php echo e(number_format($item->price, 2, ',', '.')); ?>

                            </div>

                            <!-- Yüklenen Özel Ön Yüz & Arka Yüz Fotoğrafları -->
                            <?php
                                $fImg = $item->features['front_image'] ?? ($item->features['custom_image'] ?? null);
                                $bImg = $item->features['back_image'] ?? null;
                            ?>
                            <?php if($fImg || $bImg): ?>
                                <div class="mt-3 bg-white p-3 rounded-xl border border-orange-200/80 space-y-2">
                                    <span class="block text-[11px] font-extrabold text-[#C87A53] uppercase tracking-wider">Müşteri Tarafından Yüklenen Fotoğraflar:</span>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <?php if($fImg): ?>
                                            <div class="flex items-center gap-2 bg-orange-50 p-1.5 rounded-lg border border-orange-200">
                                                <img src="<?php echo e(str_starts_with($fImg, 'http') ? $fImg : url($fImg)); ?>" class="w-10 h-10 object-cover rounded border border-orange-300">
                                                <div>
                                                    <span class="block text-[10px] font-bold text-orange-900">1. Ön Yüz Fotoğrafı</span>
                                                    <a href="<?php echo e(str_starts_with($fImg, 'http') ? $fImg : url($fImg)); ?>" target="_blank" class="text-[10px] text-blue-600 hover:underline font-bold">
                                                        <i class="fa-solid fa-download"></i> Orijinali İndir
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if($bImg): ?>
                                            <div class="flex items-center gap-2 bg-emerald-50 p-1.5 rounded-lg border border-emerald-200">
                                                <img src="<?php echo e(str_starts_with($bImg, 'http') ? $bImg : url($bImg)); ?>" class="w-10 h-10 object-cover rounded border border-emerald-300">
                                                <div>
                                                    <span class="block text-[10px] font-bold text-emerald-900">2. Arka Yüz Fotoğrafı</span>
                                                    <a href="<?php echo e(str_starts_with($bImg, 'http') ? $bImg : url($bImg)); ?>" target="_blank" class="text-[10px] text-blue-600 hover:underline font-bold">
                                                        <i class="fa-solid fa-download"></i> Orijinali İndir
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="text-right font-black text-sm text-gray-800">
                        ₺<?php echo e(number_format($item->price * $item->quantity, 2, ',', '.')); ?>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Toplam Tutar -->
    <div class="bg-gray-100 p-4 rounded-xl flex items-center justify-between font-bold">
        <span class="text-sm text-gray-700">Genel Toplam Tutar:</span>
        <span class="text-xl font-black text-[#C87A53]">₺<?php echo e(number_format($order->total_amount, 2, ',', '.')); ?></span>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>