<?php $__env->startSection('header', 'Kargo Şirketleri Yönetimi'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-5xl">
    <!-- Add New Company Form -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-plus-circle text-[#C87A53]"></i> Yeni Kargo Şirketi Ekle
        </h3>
        <form action="<?php echo e(route('admin.shipping_companies.store')); ?>" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Kargo Şirketi Adı *</label>
                <input type="text" name="name" required placeholder="Örn: Yurtiçi Kargo" class="w-full text-xs border border-gray-300 rounded-lg p-2.5 outline-none focus:border-[#C87A53]">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Web Sitesi / Takip Linki</label>
                <input type="url" name="website_url" placeholder="https://www.yurticikargo.com" class="w-full text-xs border border-gray-300 rounded-lg p-2.5 outline-none focus:border-[#C87A53]">
            </div>
            <div>
                <button type="submit" class="w-full py-2.5 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-lg text-xs transition shadow-sm flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-save"></i> Kargo Şirketi Ekle
                </button>
            </div>
        </form>
    </div>

    <!-- Companies List -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200 font-bold text-sm text-gray-700">
            Kargo Şirketleri Listesi
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-700">
                <thead class="bg-gray-100 uppercase text-[10px] font-extrabold text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="p-3">#ID</th>
                        <th class="p-3">Kargo Adı</th>
                        <th class="p-3">Web Sitesi / Takip Adresi</th>
                        <th class="p-3">Durum</th>
                        <th class="p-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-3 font-mono font-bold">#<?php echo e($company->id); ?></td>
                            <td class="p-3 font-bold text-gray-900"><?php echo e($company->name); ?></td>
                            <td class="p-3">
                                <?php if($company->website_url): ?>
                                    <a href="<?php echo e($company->website_url); ?>" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> <?php echo e($company->website_url); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 font-normal">Belirtilmedi</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo e($company->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?php echo e($company->is_active ? 'Aktif' : 'Pasif'); ?>

                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <form action="<?php echo e(route('admin.shipping_companies.destroy', $company->id)); ?>" method="POST" onsubmit="return confirm('Bu kargo şirketini silmek istediğinize emin misiniz?')" class="inline-block">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="px-2.5 py-1 bg-red-100 hover:bg-red-200 text-red-700 font-bold rounded text-xs transition">
                                        <i class="fa-solid fa-trash-can"></i> Sil
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-400">Henüz kargo şirketi eklenmedi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            <?php echo e($companies->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views\admin\shipping_companies\index.blade.php ENDPATH**/ ?>