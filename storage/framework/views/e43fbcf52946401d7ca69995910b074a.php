<?php $__env->startSection('title', 'Sipariş Sonucu - AhşapEvim'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-[#F7F5F0] pb-12 min-h-screen flex items-center">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12 text-center">
            <?php if(session('status') === 'success'): ?>
                <!-- Success State -->
                <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center text-green-500 text-4xl mx-auto mb-6 border border-green-100">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-4 font-serif">Siparişiniz Başarıyla Alındı!</h1>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Ödemeniz güvenle tamamlandı ve siparişiniz hazırlık sırasına alındı. Sipariş detaylarınız e-posta adresinize gönderilmiştir.
                </p>
                
                <?php
                    $resultOrder = \App\Models\Order::find(session('order_id'));
                    $trackingCode = $resultOrder ? ($resultOrder->tracking_code ?: 'AHS-'.$resultOrder->id) : null;
                ?>

                <div class="bg-amber-50/60 rounded-2xl p-5 mb-8 border border-amber-200/80 inline-block w-full max-w-md text-left">
                    <div class="text-xs text-amber-900 font-extrabold uppercase tracking-wider mb-2 text-center">Sipariş Bilgileriniz</div>
                    <div class="text-sm text-gray-800"><strong>Sipariş Numarası:</strong> #<?php echo e(session('order_id')); ?></div>
                    
                    <?php if($trackingCode): ?>
                        <div class="mt-3 bg-white p-3 rounded-xl border border-amber-200 flex items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] text-gray-500 font-extrabold uppercase block">Üyeliksiz Sipariş Takip Kodunuz</span>
                                <span class="text-base font-black text-[#C87A53] font-mono" id="trackingCodeText"><?php echo e($trackingCode); ?></span>
                            </div>
                            <button type="button" onclick="navigator.clipboard.writeText('<?php echo e($trackingCode); ?>'); alert('Takip kodunuz kopyalandı: <?php echo e($trackingCode); ?>');" class="bg-[#C87A53] hover:bg-[#A65F38] text-white text-xs font-bold py-1.5 px-3 rounded-lg transition shadow-sm flex items-center gap-1">
                                <i class="fa-solid fa-copy"></i> Kopyala
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="text-xs text-gray-600 mt-2"><strong>Gönderim Durumu:</strong> Hazırlanıyor</div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(url('/hesabim?tab=siparisler')); ?>" class="bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold py-3.5 px-6 rounded-xl transition text-sm shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-box-open"></i> Siparişlerimi Görüntüle
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('order.tracking')); ?>" class="bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold py-3.5 px-6 rounded-xl transition text-sm shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-truck-fast"></i> Siparişimi Takip Et
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(url('/urunler')); ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 px-6 rounded-xl transition text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-store"></i> Alışverişe Devam Et
                    </a>
                </div>
            <?php else: ?>
                <!-- Error State -->
                <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center text-red-500 text-4xl mx-auto mb-6 border border-red-100">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-4 font-serif">Ödeme Başarısız Oldu</h1>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Siparişinizin ödeme işlemi gerçekleştirilirken bir hata oluştu:<br>
                    <span class="text-red-600 font-semibold block mt-2 bg-red-50 py-2.5 px-4 rounded border border-red-100 inline-block"><?php echo e(session('message', 'Beklenmeyen bir hata oluştu.')); ?></span>
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo e(route('cart.index')); ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 px-8 rounded-lg transition text-base">
                        Sepete Geri Dön
                    </a>
                    <a href="<?php echo e(route('checkout.index')); ?>" class="bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold py-3.5 px-8 rounded-lg transition text-base shadow-md">
                        Tekrar Dene
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/checkout/result.blade.php ENDPATH**/ ?>