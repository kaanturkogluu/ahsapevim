<?php $__env->startSection('title', 'Güvenli Ödeme - AhşapEvim'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-[#F7F5F0] pb-12 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 font-serif">Güvenli Kart Ödemesi</h1>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Side: Iyzico Payment Container -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 font-serif border-b border-gray-100 pb-3 flex items-center gap-2">
                        <i class="fa-solid fa-lock text-[#C87A53]"></i>
                        256-Bit SSL Korumalı Güvenli Ödeme
                    </h2>
                    
                    <!-- Iyzico checkout form element -->
                    <div id="iyzipay-checkout-form" class="responsive mb-6"></div>
                    
                    <!-- The script supplied by Iyzico service -->
                    <?php echo $formContent; ?>

                </div>
            </div>

            <!-- Right Side: Order Info Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3 font-serif">Sipariş Bilgileri</h3>
                    
                    <div class="mb-4">
                        <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Teslimat Alıcısı</div>
                        <div class="text-sm font-bold text-gray-850"><?php echo e($order->name); ?></div>
                        <div class="text-xs text-gray-600 mt-0.5"><?php echo e($order->phone); ?> | <?php echo e($order->email); ?></div>
                    </div>
                    
                    <div class="mb-6">
                        <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Teslimat Adresi</div>
                        <div class="text-sm text-gray-700 leading-relaxed bg-gray-50 p-3 rounded border border-gray-150"><?php echo e($order->address); ?></div>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold text-gray-800">Ödenecek Tutar</span>
                            <span class="text-2xl font-extrabold text-[#C87A53]"><?php echo e(number_format($order->total_amount, 2, ',', '.')); ?> TL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u111121823/domains/ahsapevimmanisa.com/public_html/resources/views/checkout/payment.blade.php ENDPATH**/ ?>