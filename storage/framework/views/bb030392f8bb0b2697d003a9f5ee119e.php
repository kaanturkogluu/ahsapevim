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
                        <?php
                            $sidebarPages = \App\Models\Page::where('is_active', true)->get();
                        ?>
                        <?php $__currentLoopData = $sidebarPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sbPage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(url('/' . $sbPage->slug)); ?>" class="px-5 py-3 border-b border-gray-50 hover:bg-amber-50 hover:text-[#C87A53] transition <?php echo e(request()->is($sbPage->slug) ? 'bg-amber-50 text-[#C87A53] border-l-4 border-l-[#C87A53] font-bold' : 'text-gray-600 border-l-4 border-l-transparent'); ?>">
                                <?php echo e($sbPage->title); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u111121823/domains/ahsapevimmanisa.com/public_html/resources/views/pages/layout.blade.php ENDPATH**/ ?>