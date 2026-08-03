<?php $__env->startSection('title', ($pageTitle ?? 'Sayfa') . ' - AhşapEvim'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-gray-50 pb-12 min-h-screen pt-8">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row gap-8">
            
            <!-- Left Sidebar -->
            <div class="w-full md:w-1/4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                    <div class="p-4 bg-gray-50 border-b border-gray-100 font-bold text-gray-800">
                        Bilgilendirme
                    </div>
                    <nav class="flex flex-col text-[14px] font-medium">
                        <a href="<?php echo e(url('/iletisim')); ?>" class="px-5 py-3 border-b border-gray-50 hover:bg-orange-50 hover:text-brand transition <?php echo e(request()->is('iletisim') ? 'bg-orange-50 text-brand border-l-4 border-l-brand' : 'text-gray-600 border-l-4 border-l-transparent'); ?>">
                            İletişim
                        </a>
                        <a href="<?php echo e(url('/sikca-sorulanlar')); ?>" class="px-5 py-3 border-b border-gray-50 hover:bg-orange-50 hover:text-brand transition <?php echo e(request()->is('sikca-sorulanlar') ? 'bg-orange-50 text-brand border-l-4 border-l-brand' : 'text-gray-600 border-l-4 border-l-transparent'); ?>">
                            Sıkça Sorulanlar
                        </a>
                        <a href="<?php echo e(url('/mesafeli-satis-sozlesmesi')); ?>" class="px-5 py-3 border-b border-gray-50 hover:bg-orange-50 hover:text-brand transition <?php echo e(request()->is('mesafeli-satis-sozlesmesi') ? 'bg-orange-50 text-brand border-l-4 border-l-brand' : 'text-gray-600 border-l-4 border-l-transparent'); ?>">
                            Mesafeli Satış Sözleşmesi
                        </a>
                        <a href="<?php echo e(url('/gizlilik-politikasi')); ?>" class="px-5 py-3 border-b border-gray-50 hover:bg-orange-50 hover:text-brand transition <?php echo e(request()->is('gizlilik-politikasi') ? 'bg-orange-50 text-brand border-l-4 border-l-brand' : 'text-gray-600 border-l-4 border-l-transparent'); ?>">
                            Gizlilik Politikası
                        </a>
                        <a href="<?php echo e(url('/teslimat-ve-iade')); ?>" class="px-5 py-3 hover:bg-orange-50 hover:text-brand transition <?php echo e(request()->is('teslimat-ve-iade') ? 'bg-orange-50 text-brand border-l-4 border-l-brand' : 'text-gray-600 border-l-4 border-l-transparent'); ?>">
                            Teslimat ve İade Şartları
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Right Content -->
            <div class="w-full md:w-3/4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 min-h-[500px]">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b border-gray-100 pb-4">
                        <?php echo e($pageTitle ?? 'Sayfa İçeriği'); ?>

                    </h1>
                    
                    <div class="prose max-w-none text-gray-600 text-[14px] leading-relaxed">
                        <?php echo $__env->yieldContent('page_content'); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\cerceve\resources\views/pages/layout.blade.php ENDPATH**/ ?>