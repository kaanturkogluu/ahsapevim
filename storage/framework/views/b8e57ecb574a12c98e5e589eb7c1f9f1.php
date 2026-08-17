<?php $__env->startSection('header', 'E-Posta Şablon Yönetimi'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <div class="mb-6 pb-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Bilgilendirme E-Posta Şablonları</h3>
            <p class="text-xs text-gray-500 mt-1">Müşterilere otomatik olarak gönderilen tüm e-postaların konularını ve içeriklerini özelleştirin.</p>
        </div>
    </div>



    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <th class="pb-3 w-12 text-center">#</th>
                    <th class="pb-3">Şablon Adı / Olay</th>
                    <th class="pb-3">E-Posta Konusu</th>
                    <th class="pb-3 text-center">Durum</th>
                    <th class="pb-3 text-center">Son Güncelleme</th>
                    <th class="pb-3 text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="py-4 text-center font-bold text-gray-500 text-xs">
                            <?php echo e($index + 1); ?>

                        </td>
                        <td class="py-4">
                            <div class="font-bold text-gray-800"><?php echo e($template->name); ?></div>
                            <div class="text-xs font-mono text-gray-400 mt-0.5"><?php echo e($template->slug); ?></div>
                        </td>
                        <td class="py-4 text-xs font-semibold text-gray-700">
                            <?php echo e($template->subject); ?>

                        </td>
                        <td class="py-4 text-center">
                            <?php if($template->is_active): ?>
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full font-bold text-[10px]">Aktif</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full font-bold text-[10px]">Pasif</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 text-center text-xs text-gray-500">
                            <?php echo e($template->updated_at->format('d.m.Y H:i')); ?>

                        </td>
                        <td class="py-4 text-right space-x-2">
                            <a href="<?php echo e(route('admin.email_templates.preview', $template->id)); ?>" target="_blank" class="py-1.5 px-3 bg-stone-100 text-stone-700 hover:bg-stone-200 font-bold text-xs rounded-lg transition inline-flex items-center gap-1">
                                <i class="fa-solid fa-eye"></i> Ön İzle
                            </a>
                            <a href="<?php echo e(route('admin.email_templates.edit', $template->id)); ?>" class="py-1.5 px-3 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold text-xs rounded-lg transition inline-flex items-center gap-1 shadow-sm">
                                <i class="fa-solid fa-pen-to-square"></i> Düzenle
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-500">Henüz e-posta şablonu bulunmamaktadır.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/admin/email_templates/index.blade.php ENDPATH**/ ?>