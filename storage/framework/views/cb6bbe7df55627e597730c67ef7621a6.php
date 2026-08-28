<?php $__env->startSection('header', 'E-Posta Şablonu Düzenle - ' . $template->name); ?>

<!-- Quill.js Editor Styles & Script -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="<?php echo e(route('admin.email_templates.index')); ?>" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Şablon Listesine Dön
        </a>
        <a href="<?php echo e(route('admin.email_templates.preview', $template->id)); ?>" target="_blank" class="py-1.5 px-3 bg-stone-100 text-stone-700 hover:bg-stone-200 font-bold text-xs rounded-lg transition inline-flex items-center gap-1 border border-stone-300">
            <i class="fa-solid fa-eye"></i> Canlı Ön İzle
        </a>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Edit Form (2 cols) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="text-lg font-bold text-gray-800 mb-1"><?php echo e($template->name); ?></h3>
            <p class="text-xs text-gray-500 mb-6 font-mono">Olay Kodu: <?php echo e($template->slug); ?></p>

            <form id="emailTemplateForm" action="<?php echo e(route('admin.email_templates.update', $template->id)); ?>" method="POST" class="space-y-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div>
                    <label for="subject" class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-2">E-Posta Konu Başlığı *</label>
                    <input type="text" id="subject" name="subject" value="<?php echo e(old('subject', $template->subject)); ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm font-semibold focus:border-[#C87A53] focus:ring-0 outline-none">
                    <span class="text-[11px] text-gray-400 mt-1 block">Konu başlığında müşterinizin e-posta kutusunda göreceği cümleyi yazabilirsiniz.</span>
                </div>

                <!-- One-Click Shortcodes Bar -->
                <div>
                    <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-[#C87A53] fa-magic mr-1"></i> Tıklayarak Metne Otomatik Değişken Ekle:
                    </label>
                    <div class="flex flex-wrap gap-2 bg-amber-50/60 p-3 rounded-xl border border-amber-200/80 mb-3">
                        <?php if(!empty($template->shortcodes)): ?>
                            <?php $__currentLoopData = $template->shortcodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" onclick="insertShortcode('<?php echo e($code); ?>')" class="px-3 py-1.5 bg-white hover:bg-amber-100 text-[#C87A53] border border-amber-300 rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-plus text-[10px]"></i> <?php echo e($desc); ?> <span class="font-mono text-[10px] opacity-75">(<?php echo e($code); ?>)</span>
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Visual WYSIWYG Editor (Quill.js) -->
                <div>
                    <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wider mb-2">E-Posta Şablon İçeriği (Görsel Metin Düzenleyici) *</label>
                    
                    <!-- Hidden Real Input -->
                    <input type="hidden" id="content" name="content" value="<?php echo e(old('content', $template->content)); ?>">

                    <!-- Quill Container -->
                    <div id="quillEditor" class="bg-white rounded-b-lg border border-gray-300 text-sm font-sans min-h-[260px]">
                        <?php echo old('content', $template->content); ?>

                    </div>
                    <span class="text-[11px] text-gray-400 mt-1.5 block"><i class="fa-solid fa-circle-info"></i> HTML bilmenize gerek yoktur! Yazı tipi, kalınlık, başlık ve renkleri Word gibi görsel olarak düzenleyebilirsiniz.</span>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" <?php echo e($template->is_active ? 'checked' : ''); ?> class="w-4 h-4 text-[#C87A53] rounded border-gray-300 focus:ring-0">
                    <label for="is_active" class="text-xs font-bold text-gray-700">Bu Şablon Otomatik Gönderimler İçin Aktif Olsun</label>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold py-3 px-8 rounded-xl transition text-sm shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Değişiklikleri Kaydet
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar: Dynamic Variable Help & Test Mail -->
        <div class="space-y-6">
            <!-- Shortcodes Reference Box -->
            <div class="bg-amber-50/60 p-5 rounded-xl border border-amber-200/80">
                <h4 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-wand-magic-sparkles text-[#C87A53]"></i> Dinamik Değişkenler Nasıl Çalışır?
                </h4>
                <p class="text-xs text-amber-800/80 mb-4 leading-relaxed">
                    Sol taraftaki butonlara tıkladığınızda metninize eklenen süslü parantezli kodlar, e-posta gönderilirken sistem tarafından sipariş sahibinin adı, sipariş numarası gibi gerçek verilerle değiştirilir.
                </p>

                <div class="space-y-2">
                    <?php if(!empty($template->shortcodes)): ?>
                        <?php $__currentLoopData = $template->shortcodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-white p-2.5 rounded-lg border border-amber-200/80 flex items-center justify-between text-xs cursor-pointer hover:bg-amber-100 transition" onclick="insertShortcode('<?php echo e($code); ?>')">
                                <code class="font-bold text-[#C87A53] font-mono"><?php echo e($code); ?></code>
                                <span class="text-[11px] text-gray-600 font-semibold"><?php echo e($desc); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Test Mail Form -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h4 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-paper-plane text-blue-600"></i> Test E-Postası Gönder
                </h4>
                <p class="text-xs text-gray-500 mb-4">Şablonun e-posta kutunuza nasıl düştüğünü canlı test etmek için adresinize deneme maili atabilirsiniz.</p>

                <form action="<?php echo e(route('admin.email_templates.test', $template->id)); ?>" method="POST" class="space-y-3">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="test_email" class="block text-[11px] font-bold text-gray-700 mb-1">E-Posta Adresiniz</label>
                        <input type="email" id="test_email" name="email" value="<?php echo e(auth()->user()->email); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs outline-none focus:border-[#C87A53]">
                    </div>
                    <button type="submit" class="w-full py-2.5 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa-solid fa-envelope"></i> Test Maili Gönder
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script to initialize Quill WYSIWYG Editor -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.quill = new Quill('#quillEditor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link', 'clean']
                ]
            },
            placeholder: 'E-posta içeriğini buraya yazabilirsiniz...'
        });

        // Sync Quill HTML content to hidden input before form submit
        const form = document.getElementById('emailTemplateForm');
        form.onsubmit = function() {
            const contentInput = document.getElementById('content');
            contentInput.value = window.quill.root.innerHTML;
        };
    });

    // Helper function to insert shortcode into Quill Editor at current cursor position
    function insertShortcode(code) {
        if (!window.quill) return;
        
        window.quill.focus();
        const range = window.quill.getSelection(true);
        if (range) {
            window.quill.insertText(range.index, code, 'bold', true);
            window.quill.setSelection(range.index + code.length);
        } else {
            window.quill.insertText(window.quill.getLength(), code, 'bold', true);
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views\admin\email_templates\edit.blade.php ENDPATH**/ ?>