<?php $__env->startSection('header', '3D Şablon Yönetimi'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
        <div>
            <h3 class="text-lg font-bold text-gray-800">3D Çerçeve Şablonları</h3>
            <p class="text-xs text-gray-500 mt-1">Oluşturulan 3D çerçeve tasarımlarını ürünlerinizde kullanmak üzere şablon olarak kaydedin.</p>
        </div>
        <a href="<?php echo e(route('admin.templates.create')); ?>" class="py-2.5 px-5 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold rounded-lg text-sm transition flex items-center gap-2">
            <i class="fa-solid fa-cube"></i> Yeni 3D Şablon Oluştur
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <th class="pb-3 w-12 text-center">#</th>
                    <th class="pb-3">Şablon Adı</th>
                    <th class="pb-3">Ahşap Rengi / Türü</th>
                    <th class="pb-3 text-center">Dış Ebatlar (X x Y x Z)</th>
                    <th class="pb-3 text-center">İç Ebatlar (X x Y x Z)</th>
                    <th class="pb-3 text-center w-24">Kullanılan Ürünler</th>
                    <th class="pb-3 w-32 text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="py-3.5 text-center font-semibold text-gray-500"><?php echo e($tpl->id); ?></td>
                        <td class="py-3.5">
                            <div class="font-bold text-gray-800"><?php echo e($tpl->name); ?></div>
                        </td>
                        <td class="py-3.5">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-800 rounded-md font-semibold text-xs border border-amber-200/50"><?php echo e($tpl->wood_type); ?></span>
                        </td>
                        <td class="py-3.5 text-center font-mono text-xs text-gray-600">
                            <?php echo e($tpl->width); ?> x <?php echo e($tpl->height); ?> x <?php echo e($tpl->depth); ?> (Et: <?php echo e($tpl->thickness); ?>)
                        </td>
                        <td class="py-3.5 text-center font-mono text-xs text-gray-600">
                            <?php echo e($tpl->inner_width); ?> x <?php echo e($tpl->inner_height); ?> x <?php echo e($tpl->inner_depth); ?> (Kenar: <?php echo e($tpl->inner_border); ?>)
                        </td>
                        <td class="py-3.5 text-center font-bold text-[#C87A53]"><?php echo e($tpl->products_count); ?></td>
                        <td class="py-3.5 text-right space-x-2 whitespace-nowrap">
                            <a href="<?php echo e(route('admin.templates.edit', $tpl->id)); ?>" class="text-blue-600 hover:text-blue-800 font-bold text-xs"><i class="fa-solid fa-edit"></i> Düzenle</a>
                            <form action="<?php echo e(route('admin.templates.destroy', $tpl->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Bu şablonu silmek istediğinize emin misiniz? Bu şablonu kullanan ürünlerin 3D modeli pasif duruma düşecektir!')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs"><i class="fa-solid fa-trash"></i> Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-500">Kayıtlı 3D şablon bulunamadı.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views\admin\templates\index.blade.php ENDPATH**/ ?>