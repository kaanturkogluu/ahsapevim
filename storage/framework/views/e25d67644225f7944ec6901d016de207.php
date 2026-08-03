<?php $__env->startSection('title', 'Siparişiniz Alındı - AhşapEvim'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-[#F7F5F0] py-12 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto bg-white rounded-3xl shadow-lg border border-amber-100 p-6 md:p-10 text-center">
            <?php if(session('status') === 'success'): ?>
                <!-- Success State Header -->
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-5 border border-emerald-200 shadow-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-black text-gray-800 mb-2 font-serif">Siparişiniz Başarıyla Alındı!</h1>
                <p class="text-xs md:text-sm text-gray-600 mb-6 leading-relaxed max-w-lg mx-auto">
                    Ödemeniz güvenle onaylandı ve masif ahşap el işçiliği ürünleriniz hazırlık sırasına alındı.
                </p>

                <!-- Queue Email Confirmation Callout -->
                <div class="bg-stone-50 border border-stone-200 rounded-2xl p-4 mb-8 text-left flex items-center gap-3 text-xs text-stone-700">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-[#C87A53] flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-envelope-circle-check"></i>
                    </div>
                    <div>
                        <strong class="font-bold text-gray-800 block">Sipariş Onay Maili Kuyruğa Gönderildi</strong>
                        <span>Sipariş detaylarınız ve faturanız belirtmiş olduğunuz e-posta adresine gönderilmek üzere sıraya alındı.</span>
                    </div>
                </div>

                <?php
                    $resultOrder = \App\Models\Order::with('items.product')->find(session('order_id'));
                    $trackingCode = $resultOrder ? ($resultOrder->tracking_code ?: 'AHS-'.$resultOrder->id) : null;
                ?>

                <!-- Order Details Card -->
                <?php if($resultOrder): ?>
                    <div class="bg-amber-50/50 rounded-2xl p-6 mb-8 border border-amber-200/80 text-left space-y-4">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pb-4 border-b border-amber-200/60">
                            <div>
                                <span class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider block">Sipariş Numarası</span>
                                <h3 class="text-lg font-extrabold text-gray-800 font-mono">#<?php echo e($resultOrder->id); ?></h3>
                            </div>
                            <div class="text-right">
                                <?php if(session('is_eft') || str_contains($resultOrder->payment_id, 'EFT')): ?>
                                    <span class="px-3 py-1 bg-amber-100 text-amber-900 font-extrabold text-xs rounded-full inline-flex items-center gap-1 border border-amber-300">
                                        <i class="fa-solid fa-clock text-[10px]"></i> Havale / EFT Ödemesi Bekleniyor
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 font-extrabold text-xs rounded-full inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Ödendi / Hazırlanıyor
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- EFT Instructions Callout -->
                        <?php if(session('is_eft') || str_contains($resultOrder->payment_id, 'EFT')): ?>
                            <div class="bg-amber-50/90 border border-amber-300/80 rounded-2xl p-4 text-xs space-y-3 shadow-sm">
                                <div class="flex items-center gap-2 text-[#C87A53] font-black text-sm">
                                    <i class="fa-solid fa-building-columns"></i>
                                    <span>Havale / EFT Ödeme Yönergesi</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <div class="bg-white p-3 rounded-xl border border-amber-200/60">
                                        <span class="text-gray-400 font-bold block text-[10px] uppercase">Alıcı Ad Soyad</span>
                                        <span class="font-extrabold text-gray-900 text-sm">Mete Sapmaz</span>
                                    </div>
                                    <div class="bg-white p-3 rounded-xl border border-amber-200/60 flex items-center justify-between gap-2">
                                        <div>
                                            <span class="text-gray-400 font-bold block text-[10px] uppercase">IBAN Numarası</span>
                                            <span class="font-mono font-extrabold text-[#C87A53] text-sm tracking-wider">TR00 0000 0000 0000 0000</span>
                                        </div>
                                        <button type="button" onclick="navigator.clipboard.writeText('TR0000000000000000'); showToast('IBAN kopyalandı!', 'info');" class="px-2.5 py-1.5 bg-amber-100 text-amber-900 hover:bg-amber-200 rounded-lg font-bold text-[11px] transition shrink-0">
                                            <i class="fa-solid fa-copy"></i> Kopyala
                                        </button>
                                    </div>
                                </div>
                                <div class="p-3 bg-white border border-amber-200 rounded-xl text-xs text-stone-800 leading-relaxed font-semibold">
                                    <i class="fa-solid fa-circle-info text-[#C87A53] mr-1"></i>
                                    Banka uygulamanızdan Havale/EFT yaparken <strong>açıklama kısmına</strong> mutlaka <span class="text-[#C87A53] font-bold font-mono"><?php echo e($resultOrder->name); ?> - #<?php echo e($resultOrder->id); ?></span> (Müşteri Adı Soyadı - Sipariş Numarası) yazarak ücreti göndermeniz gerekmektedir. Ödemeniz kontrol edildikten sonra siparişiniz kargoya verilecektir.
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Guest Tracking Code Callout -->
                        <?php if(!auth()->check() && $trackingCode): ?>
                            <div class="bg-white p-3.5 rounded-xl border border-amber-200 flex items-center justify-between gap-3 shadow-sm">
                                <div>
                                    <span class="text-[10px] text-gray-500 font-extrabold uppercase block">Üyeliksiz Sipariş Takip Kodunuz</span>
                                    <span class="text-base font-black text-[#C87A53] font-mono"><?php echo e($trackingCode); ?></span>
                                </div>
                                <button type="button" onclick="navigator.clipboard.writeText('<?php echo e($trackingCode); ?>'); showToast('Takip kodunuz kopyalandı: <?php echo e($trackingCode); ?>', 'info');" class="bg-[#C87A53] hover:bg-[#A65F38] text-white text-xs font-bold py-2 px-3.5 rounded-xl transition shadow-sm flex items-center gap-1.5">
                                    <i class="fa-solid fa-copy"></i> Kopyala
                                </button>
                            </div>
                        <?php endif; ?>

                        <!-- Product Items Breakdown -->
                        <div class="pt-2">
                            <h4 class="text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-3">Sipariş Edilen Ürünler</h4>
                            <div class="space-y-2">
                                <?php $__currentLoopData = $resultOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-gray-150 text-xs">
                                        <div class="flex items-center gap-3">
                                            <?php if($item->product && $item->product->image): ?>
                                                <img src="<?php echo e(url($item->product->image)); ?>" class="w-10 h-10 object-cover rounded-lg border border-gray-200">
                                            <?php endif; ?>
                                            <div>
                                                <span class="font-bold text-gray-800 block"><?php echo e($item->product ? $item->product->name : 'Ahşap Ürün'); ?></span>
                                                <span class="text-[11px] text-gray-500"><?php echo e($item->quantity); ?> Adet × ₺<?php echo e(number_format($item->price, 2, ',', '.')); ?></span>
                                            </div>
                                        </div>
                                        <span class="font-extrabold text-gray-800">₺<?php echo e(number_format($item->price * $item->quantity, 2, ',', '.')); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <!-- Summary Footer -->
                        <div class="pt-3 border-t border-amber-200/60 flex justify-between items-center text-xs">
                            <span class="font-bold text-gray-600">Toplam Ödenen Tutar:</span>
                            <span class="text-xl font-black text-[#C87A53]">₺<?php echo e(number_format($resultOrder->total_amount, 2, ',', '.')); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(url('/hesabim?tab=siparisler')); ?>" class="bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold py-3.5 px-6 rounded-xl transition text-xs shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-box-open"></i> Siparişlerimi Görüntüle
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('order.tracking')); ?>" class="bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold py-3.5 px-6 rounded-xl transition text-xs shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-truck-fast"></i> Siparişimi Takip Et
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(url('/')); ?>" class="bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold py-3.5 px-6 rounded-xl transition text-xs border border-stone-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-store"></i> Alışverişe Devam Et
                    </a>
                </div>
            <?php else: ?>
                <!-- Error State -->
                <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center text-red-500 text-4xl mx-auto mb-6 border border-red-100">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-4 font-serif">Ödeme Başarısız Oldu</h1>
                <p class="text-gray-600 mb-8 leading-relaxed text-sm">
                    Siparişinizin ödeme işlemi gerçekleştirilirken bir hata oluştu:<br>
                    <span class="text-red-600 font-semibold block mt-2 bg-red-50 py-2.5 px-4 rounded border border-red-100 inline-block text-xs"><?php echo e(session('message', 'Beklenmeyen bir hata oluştu.')); ?></span>
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo e(route('cart.index')); ?>" class="bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold py-3.5 px-8 rounded-xl transition text-xs border border-stone-200">
                        Sepete Geri Dön
                    </a>
                    <a href="<?php echo e(route('checkout.index')); ?>" class="bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold py-3.5 px-8 rounded-xl transition text-xs shadow-md">
                        Tekrar Dene
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\cerceve\resources\views/checkout/result.blade.php ENDPATH**/ ?>