<?php $__env->startSection('header', 'Netgsm SMS Gönderim Logları & Manuel SMS Gönderim'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Action Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs">
        <div>
            <h3 class="text-base font-extrabold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-comment-sms text-[#C87A53]"></i> Netgsm SMS İletişim Geçmişi
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Müşterilere otomatik veya manuel gönderilen tüm SMS mesajlarını, Netgsm hata kodlarını ve ret sebeplerini takip edebilirsiniz.</p>
        </div>

        <button type="button" onclick="openSendSmsModal()" class="py-2.5 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl text-xs transition shadow-sm flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-paper-plane"></i> Manuel SMS Gönder
        </button>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <a href="<?php echo e(route('admin.sms_logs.index')); ?>" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition <?php echo e(!request('status') ? 'bg-[#29221C] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?>">
                Tüm SMS'ler
            </a>
            <a href="<?php echo e(route('admin.sms_logs.index', ['status' => 'success'])); ?>" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition <?php echo e(request('status') === 'success' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'); ?>">
                <i class="fa-solid fa-check text-[10px] mr-1"></i> Başarılılar
            </a>
            <a href="<?php echo e(route('admin.sms_logs.index', ['status' => 'failed'])); ?>" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition <?php echo e(request('status') === 'failed' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100'); ?>">
                <i class="fa-solid fa-xmark text-[10px] mr-1"></i> Başarısızlar
            </a>
        </div>

        <form action="<?php echo e(route('admin.sms_logs.index')); ?>" method="GET" class="flex items-center gap-2 w-full md:w-auto">
            <?php if(request('status')): ?>
                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
            <?php endif; ?>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Telefon no, mesaj, kod..." class="w-full md:w-64 text-xs border border-gray-300 rounded-xl px-3 py-2 outline-none focus:border-[#C87A53]">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-xl text-xs font-bold hover:bg-gray-700 transition">
                <i class="fa-solid fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-extrabold uppercase tracking-wider">
                    <tr>
                        <th class="p-4">ID / Tarih</th>
                        <th class="p-4">Alıcı Telefon</th>
                        <th class="p-4">Mesaj Metni</th>
                        <th class="p-4 text-center">Tür</th>
                        <th class="p-4 text-center">Netgsm Kodu</th>
                        <th class="p-4 text-center">Durum</th>
                        <th class="p-4 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="p-4">
                                <span class="font-mono font-bold text-gray-900 block">#<?php echo e($log->id); ?></span>
                                <span class="text-[11px] text-gray-400 block mt-0.5"><?php echo e($log->created_at?->format('d.m.Y H:i') ?? '-'); ?></span>
                                <?php if($log->order_id): ?>
                                    <a href="<?php echo e(route('admin.orders.show', $log->order_id)); ?>" class="text-[10px] text-blue-600 hover:underline font-bold font-mono block mt-0.5">
                                        Sipariş #<?php echo e($log->order_id); ?>

                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 font-mono font-bold text-gray-800">
                                <?php echo e($log->to_phone); ?>

                            </td>
                            <td class="p-4 max-w-xs truncate" title="<?php echo e($log->message); ?>">
                                <?php echo e($log->message); ?>

                            </td>
                            <td class="p-4 text-center">
                                <?php if($log->type === 'manual'): ?>
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-extrabold">Manuel</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-[10px] font-extrabold">Otomatik</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center font-mono text-xs">
                                <span class="px-2 py-0.5 bg-gray-100 rounded font-bold"><?php echo e($log->response_code ?: 'N/A'); ?></span>
                            </td>
                            <td class="p-4 text-center">
                                <?php if($log->status === 'success'): ?>
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-black inline-flex items-center gap-1">
                                        <i class="fa-solid fa-check"></i> İletildi
                                    </span>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-[10px] font-black inline-flex items-center gap-1">
                                        <i class="fa-solid fa-xmark"></i> Başarısız
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-right space-x-1 whitespace-nowrap">
                                <button type="button" onclick="viewSmsDetail(<?php echo e(json_encode($log)); ?>)" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition">
                                    <i class="fa-solid fa-eye"></i> Detay
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="p-12 text-center text-gray-400 font-bold">
                                Kaydedilmiş bir SMS gönderim kaydı bulunamadı.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($logs->hasPages()): ?>
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                <?php echo e($logs->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Manuel SMS Gönderme Modalı -->
<div id="sendSmsModal" class="fixed inset-0 z-[99999] bg-black/70 backdrop-blur-xs hidden flex-col items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 relative">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-extrabold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-[#C87A53]"></i> Netgsm İle Manuel SMS Gönder
            </h3>
            <button type="button" onclick="closeSendSmsModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>

        <form action="<?php echo e(route('admin.sms.send_manual')); ?>" method="POST" class="space-y-4" id="manualSmsForm">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-[11px] font-extrabold text-gray-500 uppercase mb-1">Alıcı GSM / Telefon Numarası *</label>
                <div class="relative">
                    <input
                        type="text"
                        name="to_phone"
                        id="modalToPhone"
                        required
                        placeholder="0532 123 45 67"
                        maxlength="19"
                        autocomplete="tel"
                        inputmode="tel"
                        class="w-full text-xs font-mono border border-gray-300 rounded-xl p-2.5 pr-9 outline-none focus:border-[#C87A53] transition"
                        oninput="formatPhoneInput(this)"
                        onblur="validatePhoneInput(this)"
                    >
                    <span id="phoneValidIcon" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs hidden"></span>
                </div>
                <p id="phoneError" class="text-[10px] text-rose-600 font-bold mt-1 hidden">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i>
                    Geçerli bir Türk GSM numarası girin. Örn: 05XXXXXXXXX
                </p>
                <p class="text-[10px] text-gray-400 mt-1">Desteklenen formatlar: 05XX, +905XX, 905XX</p>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-gray-500 uppercase mb-1">SMS Mesaj Metni *</label>
                <textarea name="message" id="modalSmsBody" rows="4" maxlength="918" required placeholder="Müşterinize gitmesini istediğiniz SMS metni..." class="w-full text-xs border border-gray-300 rounded-xl p-2.5 outline-none focus:border-[#C87A53]" oninput="updateSmsCharCount(this)"></textarea>
                <div class="text-[10px] text-gray-400 text-right mt-1 font-mono flex items-center justify-end gap-2">
                    <span id="smsParts" class="px-1.5 py-0.5 bg-amber-50 text-amber-700 rounded font-bold hidden"></span>
                    <span><span id="smsCharCount">0</span> / 918 Karakter</span>
                </div>
            </div>

            <input type="hidden" name="order_id" id="modalSmsOrderId">

            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeSendSmsModal()" class="py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                    Vazgeç
                </button>
                <button type="submit" id="smsSubmitBtn" class="py-2 px-5 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold text-xs rounded-xl transition shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-paper-plane"></i> Netgsm İle Gönder
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Log Detay Görünüm Modalı -->
<div id="smsDetailModal" class="fixed inset-0 z-[99999] bg-black/70 backdrop-blur-xs hidden flex-col items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-200 relative max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-extrabold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-comment-dots text-[#C87A53]"></i> SMS Kayıt Detayı #<span id="detailSmsLogId"></span>
            </h3>
            <button type="button" onclick="closeSmsDetailModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>

        <div class="space-y-4 overflow-y-auto pr-1 text-xs text-gray-700">
            <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3 rounded-xl border border-gray-150">
                <div>
                    <span class="text-[10px] text-gray-400 font-extrabold uppercase block">Alıcı Telefon</span>
                    <strong id="detailSmsToPhone" class="text-gray-900 font-mono font-bold"></strong>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-extrabold uppercase block">Tarih / Netgsm Kodu</span>
                    <span id="detailSmsMeta" class="font-bold"></span>
                </div>
            </div>

            <div id="detailSmsErrorBox" class="hidden bg-rose-50 border border-rose-200 p-3 rounded-xl text-rose-900">
                <span class="text-[10px] font-extrabold uppercase block text-rose-700 mb-1">Netgsm / Başarısızlık Sebebi</span>
                <div id="detailSmsErrorMessage" class="font-mono text-[11px] leading-relaxed"></div>
            </div>

            <div>
                <span class="text-[10px] text-gray-400 font-extrabold uppercase block mb-1">Gönderilen SMS Metni</span>
                <div id="detailSmsMessage" class="p-3 bg-gray-50 border border-gray-200 rounded-xl whitespace-pre-line leading-relaxed font-mono"></div>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeSmsDetailModal()" class="py-2 px-5 bg-gray-800 text-white font-bold text-xs rounded-xl transition">
                Kapat
            </button>
        </div>
    </div>
</div>

<script>
// ── Telefon Numarası Formatlama ──────────────────────────────────────────────

/**
 * Türk GSM numarasını "0532 123 45 67" formatında gösterir.
 * Girilen metni hidden input'a ham (rakam-only) olarak yazar.
 */
function formatPhoneInput(el) {
    // Sadece rakam al
    let digits = el.value.replace(/\D/g, '');

    // Başında 90 ile geliyorsa at (kullanıcı +90 yazmış olabilir)
    if (digits.startsWith('90') && digits.length > 10) {
        digits = digits.slice(2);
    }

    // Başında 0 yoksa ekle (5 ile başlıyorsa)
    if (digits.startsWith('5')) {
        digits = '0' + digits;
    }

    // Maksimum 11 hane (0 + 10 hane)
    digits = digits.slice(0, 11);

    // Format: 0532 123 45 67
    let formatted = '';
    if (digits.length > 0)  formatted  = digits.slice(0, 4);
    if (digits.length > 4)  formatted += ' ' + digits.slice(4, 7);
    if (digits.length > 7)  formatted += ' ' + digits.slice(7, 9);
    if (digits.length > 9)  formatted += ' ' + digits.slice(9, 11);

    el.value = formatted;

    // Geçerlilik kontrolü (canlı)
    if (digits.length === 11) {
        validatePhoneInput(el);
    } else {
        clearPhoneValidation();
    }
}

function validatePhoneInput(el) {
    const digits = el.value.replace(/\D/g, '');
    const validIcon = document.getElementById('phoneValidIcon');
    const errorMsg  = document.getElementById('phoneError');
    const submitBtn = document.getElementById('smsSubmitBtn');

    // Türk GSM: 0 + 5XX + 8 hane = 11 hane
    const isValid = /^05[0-9]{9}$/.test(digits);

    if (isValid) {
        validIcon.textContent = '✓';
        validIcon.className = 'absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-emerald-600 font-bold';
        validIcon.classList.remove('hidden');
        errorMsg.classList.add('hidden');
        el.classList.remove('border-rose-400');
        el.classList.add('border-emerald-400');
        if (submitBtn) submitBtn.disabled = false;
    } else if (digits.length > 0) {
        validIcon.textContent = '✗';
        validIcon.className = 'absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-rose-500 font-bold';
        validIcon.classList.remove('hidden');
        errorMsg.classList.remove('hidden');
        el.classList.remove('border-emerald-400');
        el.classList.add('border-rose-400');
        if (submitBtn) submitBtn.disabled = true;
    } else {
        clearPhoneValidation();
    }
}

function clearPhoneValidation() {
    const validIcon = document.getElementById('phoneValidIcon');
    const errorMsg  = document.getElementById('phoneError');
    const phoneEl   = document.getElementById('modalToPhone');
    const submitBtn = document.getElementById('smsSubmitBtn');
    if (validIcon) validIcon.classList.add('hidden');
    if (errorMsg)  errorMsg.classList.add('hidden');
    if (phoneEl)   phoneEl.classList.remove('border-rose-400', 'border-emerald-400');
    if (submitBtn) submitBtn.disabled = false;
}

// ── SMS Karakter Sayacı & Bölüm Göstergesi ──────────────────────────────────

function updateSmsCharCount(el) {
    const len     = el.value.length;
    const counter = document.getElementById('smsCharCount');
    const parts   = document.getElementById('smsParts');

    if (counter) counter.textContent = len;

    // SMS bölüm hesabı (Türkçe karakter varsa 153, yoksa 160 karakter/bölüm)
    const hasTurkish = /[çğışöüÇĞİŞÖÜ]/.test(el.value);
    const smsLimit   = hasTurkish ? 153 : 160;
    const smsCount   = Math.ceil(len / smsLimit) || 1;

    if (parts) {
        if (len > smsLimit) {
            parts.textContent = smsCount + ' SMS';
            parts.classList.remove('hidden');
        } else {
            parts.classList.add('hidden');
        }
    }
}

// ── Modal Kontrolleri ────────────────────────────────────────────────────────

function openSendSmsModal(phone = '', orderId = '') {
    const modal   = document.getElementById('sendSmsModal');
    const phoneEl = document.getElementById('modalToPhone');
    if (!modal) return;

    // Numarayı formatla
    if (phone) {
        phoneEl.value = phone;
        formatPhoneInput(phoneEl);
    } else {
        phoneEl.value = '';
        clearPhoneValidation();
    }

    document.getElementById('modalSmsOrderId').value = orderId;
    document.getElementById('modalSmsBody').value = '';
    updateSmsCharCount(document.getElementById('modalSmsBody'));

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => phoneEl.focus(), 100);
}

function closeSendSmsModal() {
    const modal = document.getElementById('sendSmsModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        clearPhoneValidation();
    }
}

function viewSmsDetail(log) {
    document.getElementById('detailSmsLogId').textContent   = log.id;
    document.getElementById('detailSmsToPhone').textContent = log.to_phone;
    document.getElementById('detailSmsMeta').textContent    = (log.created_at || '') + ' | Kod: ' + (log.response_code || 'N/A');
    document.getElementById('detailSmsMessage').textContent = log.message || 'Mesaj bulunamadı.';

    const errBox = document.getElementById('detailSmsErrorBox');
    if (log.status === 'failed' || log.error_message) {
        document.getElementById('detailSmsErrorMessage').textContent = log.error_message || 'Bilinmeyen SMS gönderim hatası.';
        errBox.classList.remove('hidden');
    } else {
        errBox.classList.add('hidden');
    }

    const modal = document.getElementById('smsDetailModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeSmsDetailModal() {
    const modal = document.getElementById('smsDetailModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Modal dışına tıklayınca kapat
document.addEventListener('click', function(e) {
    ['sendSmsModal', 'smsDetailModal'].forEach(function(id) {
        const modal = document.getElementById(id);
        if (modal && e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views\admin\logs\sms.blade.php ENDPATH**/ ?>