<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahşap Evim Manisa - Yönetim</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(url('/favicon.ico')); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(url('/favicon.ico')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(url('/ahsaplogo_yataybg.png')); ?>">
    <!-- Tailwind CSS CDN for admin -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    <div class="w-64 bg-[#29221C] text-white flex flex-col">
        <div class="p-4 bg-[#1D1713] text-center font-bold text-xl border-b border-[#3D332B]">
            Ahşap Evim Admin
        </div>
        <nav class="flex-1 overflow-y-auto mt-4">
            <a href="<?php echo e(url('/yonetim/siparisler')); ?>" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-shopping-cart mr-2"></i> Siparişler</a>
            <a href="<?php echo e(url('/yonetim/urunler')); ?>" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-box mr-2"></i> Ürünler</a>
            <a href="<?php echo e(url('/yonetim/kategoriler')); ?>" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-list mr-2"></i> Kategoriler</a>
            <a href="<?php echo e(url('/yonetim/3d-sablonlar')); ?>" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-cube mr-2"></i> 3D Şablonlar</a>
            <a href="<?php echo e(route('admin.email_templates.index')); ?>" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-envelope-open-text mr-2"></i> E-Posta Şablonları</a>
            <a href="<?php echo e(url('/yonetim/sayfalar')); ?>" class="block p-4 hover:bg-[#3D332B]"><i class="fa-solid fa-file-lines mr-2"></i> Bilgilendirme</a>
            <a href="<?php echo e(url('/')); ?>" class="block p-4 hover:bg-[#3D332B] mt-4 border-t border-[#3D332B]"><i class="fa-solid fa-eye mr-2"></i> Siteyi Görüntüle</a>
            
            <form action="<?php echo e(route('admin.logout')); ?>" method="POST" class="block w-full border-t border-[#3D332B]">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full text-left p-4 hover:bg-red-950/40 text-red-350 hover:text-red-200 transition-colors flex items-center"><i class="fa-solid fa-sign-out-alt mr-2"></i> Güvenli Çıkış</button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <header class="bg-white p-4 shadow flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800"><?php echo $__env->yieldContent('header', 'Yönetim Paneli'); ?></h2>
            <div>Admin</div>
        </header>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            <?php if(session('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/layouts/admin.blade.php ENDPATH**/ ?>