<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AhşapEvim - Yönetici Girişi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F7F5F0;
            background-image: url('<?php echo e(url('/light_wood_bg.jpg')); ?>');
            background-repeat: repeat;
            background-size: 320px 320px;
            color: #2E251E;
        }
        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white border border-[#EFEAE0] rounded-2xl shadow-xl p-8">
        
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="<?php echo e(url('/')); ?>" class="inline-block mb-3">
                <img src="<?php echo e(url('/ahsaplogo_yatay.png')); ?>" alt="AhşapEvim Logo" class="h-16 w-auto object-contain mx-auto">
            </a>
            <h2 class="text-2xl font-bold text-[#2E251E] mt-2">Yönetim Paneli</h2>
            <p class="text-xs text-gray-500 mt-1">Lütfen yönetici bilgilerinizi kullanarak giriş yapın.</p>
        </div>

        <?php if(session('success')): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm mb-4">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(url('/yonetim/giris')); ?>" method="POST" class="space-y-5">
            <?php echo csrf_field(); ?>
            
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">E-Posta Adresi</label>
                <div class="relative flex items-center">
                    <span class="absolute left-3 text-gray-400 text-sm"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" name="email" required value="<?php echo e(old('email')); ?>" class="w-full text-sm border-gray-300 rounded-lg p-2.5 pl-10 border focus:border-[#C87A53] focus:ring-0 outline-none" placeholder="admin@ahsapevim.com">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Şifre</label>
                <div class="relative flex items-center">
                    <span class="absolute left-3 text-gray-400 text-sm"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 pl-10 border focus:border-[#C87A53] focus:ring-0 outline-none" placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-[#C87A53] focus:ring-[#C87A53] w-4 h-4">
                    <span class="text-xs font-medium text-gray-600">Beni Hatırla</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl text-sm transition-all shadow-md shadow-brand/10 flex items-center justify-center gap-2">
                <i class="fa-solid fa-sign-in-alt"></i> Giriş Yap
            </button>
        </form>

    </div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>