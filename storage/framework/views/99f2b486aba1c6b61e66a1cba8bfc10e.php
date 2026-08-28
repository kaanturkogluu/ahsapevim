<?php $__env->startSection('title', 'Cari Hesaplar'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><i class="fa-solid fa-wallet text-[#C87A53] mr-2"></i>Cari Hesaplar</h1>
        <p class="text-xs text-gray-500 mt-1">Müşterilerinizin güncel borç/alacak durumlarını takip edin.</p>
    </div>
</div>

<!-- Özet Kartları -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl mr-4">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div class="text-sm text-gray-500 font-medium">Toplam Cari Hesap</div>
            <div class="text-2xl font-bold text-gray-800"><?php echo e($users->total()); ?></div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl mr-4">
            <i class="fa-solid fa-arrow-up-right-dots"></i>
        </div>
        <div>
            <div class="text-sm text-gray-500 font-medium">Toplam Alacak (Müşteri Borcu)</div>
            <div class="text-2xl font-bold text-red-600"><?php echo e(number_format($totalDebt, 2, ',', '.')); ?> ₺</div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
        <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl mr-4">
            <i class="fa-solid fa-arrow-down-long"></i>
        </div>
        <div>
            <div class="text-sm text-gray-500 font-medium">Toplam Borç (Firma Borcu)</div>
            <div class="text-2xl font-bold text-green-600"><?php echo e(number_format(abs($totalCredit), 2, ',', '.')); ?> ₺</div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Filtreler -->
    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <form action="<?php echo e(route('admin.cari.index')); ?>" method="GET" class="flex gap-2">
            <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Müşteri Adı, E-posta veya Tel" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#C87A53] focus:ring-1 focus:ring-[#C87A53] w-64">
            <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition"><i class="fa-solid fa-search"></i></button>
            <?php if($search): ?>
                <a href="<?php echo e(route('admin.cari.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition">Temizle</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-4">Müşteri</th>
                    <th scope="col" class="px-6 py-4">İletişim</th>
                    <th scope="col" class="px-6 py-4 text-right">Bakiye (Durum)</th>
                    <th scope="col" class="px-6 py-4 text-center">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="bg-white border-b border-gray-50 hover:bg-orange-50/30 transition">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#C87A53] text-white flex items-center justify-center font-bold">
                                    <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                </div>
                                <?php echo e($user->name); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-800"><?php echo e($user->email); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($user->phone ?? '-'); ?></div>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-base">
                            <?php if($user->balance > 0): ?>
                                <span class="text-red-600" title="Müşteri firmamıza borçlu"><?php echo e(number_format($user->balance, 2, ',', '.')); ?> ₺</span>
                                <div class="text-[10px] text-red-500 font-normal">Borçlu</div>
                            <?php elseif($user->balance < 0): ?>
                                <span class="text-green-600" title="Müşteri alacaklı"><?php echo e(number_format(abs($user->balance), 2, ',', '.')); ?> ₺</span>
                                <div class="text-[10px] text-green-500 font-normal">Alacaklı</div>
                            <?php else: ?>
                                <span class="text-gray-500">0,00 ₺</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="<?php echo e(route('admin.cari.show', $user->id)); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#C87A53] hover:bg-[#A65F38] text-white rounded-lg text-xs font-semibold transition shadow-sm">
                                <i class="fa-solid fa-file-invoice"></i> Ekstre Gör
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Cari hesabı bulunan müşteri bulunamadı.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if($users->hasPages()): ?>
        <div class="p-4 border-t border-gray-100">
            <?php echo e($users->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views\admin\cari\index.blade.php ENDPATH**/ ?>