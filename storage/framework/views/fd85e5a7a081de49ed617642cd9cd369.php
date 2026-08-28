<?php $__env->startSection('title', 'Google Merchant Center'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-md" style="background:linear-gradient(135deg,#4285F4,#34A853)">
                    <i class="fa-brands fa-google text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Google Merchant Center</h1>
                    <p class="text-xs text-gray-400 mt-0.5">Ürünlerinizi Google Shopping'e senkronize edin</p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                
                <?php if($isConnected): ?>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-semibold">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        Bağlı<?php echo e($accountName ? ' — ' . $accountName : ''); ?>

                    </span>
                    
                    <button onclick="openSyncModal('all')"
                            id="syncAllBtn"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-[#4285F4] hover:bg-[#3367D6] text-white text-xs font-bold rounded-lg transition shadow-sm">
                        <i class="fa-solid fa-rotate"></i>
                        Tümünü Senkronize Et
                    </button>
                <?php else: ?>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-full text-xs font-semibold">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        Bağlı Değil
                    </span>
                <?php endif; ?>
            </div>
        </div>

        
        <?php if($error && !$isConnected): ?>
        <div class="mt-5 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm font-semibold text-red-700 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> API Bağlantı Hatası
            </p>
            <p class="text-xs text-red-600 mt-1 font-mono bg-white/60 rounded p-2 border border-red-100 mt-2 break-all"><?php echo e($error); ?></p>
            <details class="mt-3">
                <summary class="text-xs text-red-700 font-semibold cursor-pointer hover:underline">Çözüm adımlarını göster ▸</summary>
                <ol class="mt-2 list-decimal list-inside text-xs text-red-800 space-y-1 ml-2">
                    <li>Google Cloud Console → <strong>Content API for Shopping</strong>'i etkinleştirin.</li>
                    <li>Service Account oluşturun, JSON key indirin.</li>
                    <li>JSON'u <code class="bg-white/70 px-1 rounded">storage/app/google-service-account.json</code> olarak kaydedin.</li>
                    <li><code class="bg-white/70 px-1 rounded">.env</code> dosyasına ekleyin: <code class="bg-white/70 px-1 rounded">GOOGLE_MERCHANT_ID=...</code> ve <code class="bg-white/70 px-1 rounded">GOOGLE_SERVICE_ACCOUNT_JSON=...</code></li>
                    <li>Merchant Center → Ayarlar → Kullanıcılar → Service account e-postasını <strong>Standard</strong> yetkiyle ekleyin.</li>
                </ol>
            </details>
        </div>
        <?php endif; ?>
    </div>

    
    <div id="resultPanel" class="hidden rounded-xl border p-5 transition-all duration-300">
        <div class="flex items-start gap-3">
            <div id="resultIcon" class="text-xl shrink-0 mt-0.5"></div>
            <div class="flex-1 min-w-0">
                <p id="resultTitle" class="text-sm font-bold"></p>
                <p id="resultMessage" class="text-xs mt-1"></p>
                <div id="resultErrors" class="hidden mt-3 space-y-1"></div>
                <div id="resultStats" class="hidden mt-3 flex gap-4"></div>
            </div>
            <button onclick="document.getElementById('resultPanel').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 shrink-0">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-700">
                Ürünler
                <span class="ml-1.5 text-xs font-medium text-gray-400">(<?php echo e($products->count()); ?> ürün)</span>
            </h3>
            <?php if($isConnected): ?>
            <p class="text-xs text-gray-400">
                <i class="fa-solid fa-lock text-amber-500 mr-1"></i>
                İşlemler admin şifresi gerektirir
            </p>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-8">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Ürün</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">Fiyat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Durum</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="productTable">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/70 transition" id="row-<?php echo e($product->id); ?>">
                            <td class="px-4 py-3 text-xs text-gray-400"><?php echo e($product->id); ?></td>

                            
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <?php $hasImage = !empty($product->attributes['image']); ?>
                                    <?php if($hasImage): ?>
                                        <img src="<?php echo e(url($product->attributes['image'])); ?>"
                                             alt="<?php echo e($product->name); ?>"
                                             class="w-9 h-9 object-cover rounded-lg border border-gray-200 shrink-0">
                                    <?php else: ?>
                                        <div class="w-9 h-9 rounded-lg border-2 border-dashed border-red-200 bg-red-50 flex items-center justify-center shrink-0" title="Görsel eksik">
                                            <i class="fa-solid fa-image-slash text-red-400 text-xs"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 text-xs truncate max-w-[180px]"><?php echo e($product->name); ?></p>
                                        <?php if(!$hasImage): ?>
                                            <span class="text-[10px] text-red-500 font-medium">Görsel eksik — gönderilemez</span>
                                        <?php elseif(!$product->is_active): ?>
                                            <span class="text-[10px] text-gray-400">Pasif ürün</span>
                                        <?php else: ?>
                                            <span class="text-[10px] text-gray-400"><?php echo e($product->stock > 0 ? $product->stock.' adet stok' : 'Stok yok'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            
                            <td class="px-4 py-3 text-xs text-gray-500 hidden sm:table-cell">
                                <?php echo e($product->category->name ?? '—'); ?>

                            </td>

                            
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="text-xs font-bold text-gray-800"><?php echo e(number_format($product->price, 2, ',', '.')); ?> ₺</span>
                                <?php if($product->original_price): ?>
                                    <br><span class="text-[10px] text-gray-400 line-through"><?php echo e(number_format($product->original_price, 2, ',', '.')); ?> ₺</span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="px-4 py-3">
                                <?php if($product->is_active): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-500 border border-gray-200 rounded-full text-[10px] font-semibold">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>Pasif
                                    </span>
                                <?php endif; ?>

                                
                                <div id="status-<?php echo e($product->id); ?>" class="hidden mt-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold" id="status-text-<?php echo e($product->id); ?>"></span>
                                </div>
                            </td>

                            
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <?php if($isConnected): ?>
                                        <?php if($hasImage && $product->is_active): ?>
                                            <button onclick="openSyncModal('single', <?php echo e($product->id); ?>, '<?php echo e(addslashes($product->name)); ?>')"
                                                    id="sync-btn-<?php echo e($product->id); ?>"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg text-xs font-semibold transition"
                                                    title="Merchant Center'a Gönder">
                                                <i class="fa-brands fa-google text-xs"></i>
                                                <span>Gönder</span>
                                            </button>
                                            <button onclick="openSyncModal('delete', <?php echo e($product->id); ?>, '<?php echo e(addslashes($product->name)); ?>')"
                                                    id="del-btn-<?php echo e($product->id); ?>"
                                                    class="inline-flex items-center gap-1 px-2 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg text-xs transition"
                                                    title="Merchant Center'dan Sil">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        <?php elseif(!$hasImage): ?>
                                            <span class="text-[10px] text-red-400 italic">Görsel ekle</span>
                                        <?php else: ?>
                                            <span class="text-[10px] text-gray-400 italic">Pasif</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-[10px] text-gray-400 italic">API bağlı değil</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-16 text-center text-gray-400">
                                <i class="fa-solid fa-box-open text-4xl mb-3 block"></i>
                                Henüz ürün eklenmemiş.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




<div id="syncModal" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeSyncModal()"></div>

    
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0" id="syncModalBox">

            
            <div class="px-6 pt-6 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" id="modalIcon" style="background:linear-gradient(135deg,#4285F4,#34A853)">
                        <i class="fa-brands fa-google text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800" id="modalTitle">Merchant Center Senkronizasyon</h3>
                        <p class="text-xs text-gray-500" id="modalSubtitle">Bu işlem Google Merchant Center'a veri gönderecek.</p>
                    </div>
                </div>
            </div>

            
            <div class="px-6 py-4 bg-blue-50/50 border-b border-blue-100">
                <p class="text-xs text-blue-800" id="modalDescription"></p>
            </div>

            
            <div class="px-6 py-5">
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fa-solid fa-lock text-amber-500 mr-1"></i>
                    Admin Şifresi
                </label>
                <div class="relative">
                    <input type="password"
                           id="adminPassword"
                           placeholder="Şifrenizi girin..."
                           autocomplete="current-password"
                           onkeydown="if(event.key==='Enter') submitSync()"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent pr-10 transition">
                    <button type="button" onclick="togglePasswordVisibility()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-eye text-sm" id="eyeIcon"></i>
                    </button>
                </div>

                
                <div id="modalError" class="hidden mt-2.5 flex items-center gap-1.5 text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                    <i class="fa-solid fa-circle-xmark shrink-0"></i>
                    <span id="modalErrorText"></span>
                </div>
            </div>

            
            <div class="px-6 pb-6 flex gap-3">
                <button onclick="closeSyncModal()"
                        id="cancelBtn"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-semibold transition">
                    İptal
                </button>
                <button onclick="submitSync()"
                        id="confirmBtn"
                        class="flex-1 px-4 py-2.5 bg-[#4285F4] hover:bg-[#3367D6] text-white rounded-xl text-sm font-bold transition shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rotate" id="confirmBtnIcon"></i>
                    <span id="confirmBtnText">Başlat</span>
                </button>
            </div>
        </div>
    </div>
</div>




<script>
// ─── Modal durumu ────────────────────────────────────────────────────────────
let currentAction = null;  // 'all' | 'single' | 'delete'
let currentProductId = null;
let currentProductName = null;
let isSubmitting = false;

// ─── Modal Aç ───────────────────────────────────────────────────────────────
function openSyncModal(action, productId = null, productName = null) {
    currentAction      = action;
    currentProductId   = productId;
    currentProductName = productName;

    // Modal içeriğini güncelle
    const configs = {
        all: {
            title: 'Tüm Ürünleri Senkronize Et',
            subtitle: 'Tüm aktif ürünler Google Merchant Center\'a gönderilecek.',
            desc: '⚠️ Bu işlem tüm aktif ürünleri Merchant Center\'a yükler. İşlem ürün sayısına göre 1-5 dakika sürebilir.',
            btnText: 'Senkronize Et',
            btnClass: 'bg-[#4285F4] hover:bg-[#3367D6]',
            iconStyle: 'background:linear-gradient(135deg,#4285F4,#34A853)',
        },
        single: {
            title: 'Ürün Gönder',
            subtitle: `"${productName}" Merchant Center'a gönderilecek.`,
            desc: `✅ "${productName}" ürünü Google Shopping'de yayınlanacak.`,
            btnText: 'Gönder',
            btnClass: 'bg-[#4285F4] hover:bg-[#3367D6]',
            iconStyle: 'background:linear-gradient(135deg,#4285F4,#34A853)',
        },
        delete: {
            title: 'Merchant Center\'dan Sil',
            subtitle: `"${productName}" listeden kaldırılacak.`,
            desc: `🗑️ "${productName}" Google Shopping listesinden kaldırılacak. Ürün siteden silinmez.`,
            btnText: 'Sil',
            btnClass: 'bg-red-600 hover:bg-red-700',
            iconStyle: 'background:linear-gradient(135deg,#ef4444,#dc2626)',
        },
    };

    const cfg = configs[action];
    document.getElementById('modalTitle').textContent      = cfg.title;
    document.getElementById('modalSubtitle').textContent   = cfg.subtitle;
    document.getElementById('modalDescription').textContent = cfg.desc;
    document.getElementById('modalIcon').style.cssText     = cfg.iconStyle;
    document.getElementById('confirmBtn').className        = `flex-1 px-4 py-2.5 text-white rounded-xl text-sm font-bold transition shadow-sm flex items-center justify-center gap-2 ${cfg.btnClass}`;
    document.getElementById('confirmBtnText').textContent  = cfg.btnText;
    document.getElementById('confirmBtnIcon').className    = action === 'delete' ? 'fa-solid fa-trash-can' : 'fa-solid fa-rotate';

    // Temizle
    document.getElementById('adminPassword').value = '';
    hideModalError();
    resetConfirmBtn();

    // Göster
    const modal = document.getElementById('syncModal');
    const box   = document.getElementById('syncModalBox');
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    });

    setTimeout(() => document.getElementById('adminPassword').focus(), 200);
}

// ─── Modal Kapat ─────────────────────────────────────────────────────────────
function closeSyncModal() {
    if (isSubmitting) return;
    const box = document.getElementById('syncModalBox');
    box.classList.remove('scale-100', 'opacity-100');
    box.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        document.getElementById('syncModal').classList.add('hidden');
        currentAction = currentProductId = currentProductName = null;
    }, 200);
}

// ─── Şifreyi Göster/Gizle ────────────────────────────────────────────────────
function togglePasswordVisibility() {
    const input = document.getElementById('adminPassword');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash text-sm';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye text-sm';
    }
}

// ─── Sync Gönder ─────────────────────────────────────────────────────────────
async function submitSync() {
    if (isSubmitting) return;

    const password = document.getElementById('adminPassword').value.trim();
    if (!password) {
        showModalError('Lütfen admin şifrenizi girin.');
        return;
    }

    hideModalError();
    setSubmitting(true);

    let url, method = 'POST';
    if (currentAction === 'all') {
        url = '/yonetim/merchant-center/sync-all';
    } else if (currentAction === 'single') {
        url = `/yonetim/merchant-center/sync/${currentProductId}`;
    } else if (currentAction === 'delete') {
        url    = `/yonetim/merchant-center/delete/${currentProductId}`;
        method = 'DELETE';
    }

    try {
        const resp = await fetch(url, {
            method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept':       'application/json',
            },
            body: JSON.stringify({ password }),
        });

        const data = await resp.json();

        if (resp.status === 403 || data.error_type === 'password_wrong') {
            showModalError('Yanlış şifre. Tekrar deneyin.');
            setSubmitting(false);
            document.getElementById('adminPassword').value = '';
            document.getElementById('adminPassword').focus();
            return;
        }

        if (resp.status === 422 && data.error_type === 'password_required') {
            showModalError('Şifre zorunlu.');
            setSubmitting(false);
            return;
        }

        // Başarı ya da işlem hatası — modal kapat, sonuç göster
        closeSyncModal();
        setTimeout(() => showResultPanel(data, currentAction), 300);

        // Satır güncelle
        if ((currentAction === 'single' || currentAction === 'delete') && currentProductId) {
            updateRowStatus(currentProductId, data.success, currentAction);
        }

    } catch (err) {
        showModalError('Bağlantı hatası. İnternet bağlantınızı kontrol edin.');
        setSubmitting(false);
    }
}

// ─── Sonuç Paneli ─────────────────────────────────────────────────────────────
function showResultPanel(data, action) {
    const panel = document.getElementById('resultPanel');

    if (data.success) {
        panel.className = 'rounded-xl border p-5 transition-all duration-300 bg-green-50 border-green-200';
        document.getElementById('resultIcon').innerHTML  = '<i class="fa-solid fa-circle-check text-green-500 text-2xl"></i>';
        document.getElementById('resultTitle').className = 'text-sm font-bold text-green-800';
        document.getElementById('resultTitle').textContent = '✅ İşlem Başarılı';
        document.getElementById('resultMessage').className = 'text-xs mt-1 text-green-700';
    } else {
        panel.className = 'rounded-xl border p-5 transition-all duration-300 bg-red-50 border-red-200';
        document.getElementById('resultIcon').innerHTML  = '<i class="fa-solid fa-circle-xmark text-red-500 text-2xl"></i>';
        document.getElementById('resultTitle').className = 'text-sm font-bold text-red-800';
        document.getElementById('resultTitle').textContent = '❌ İşlem Başarısız';
        document.getElementById('resultMessage').className = 'text-xs mt-1 text-red-700';
    }

    document.getElementById('resultMessage').textContent = data.message || '';

    // İstatistikler (tüm sync için)
    const statsEl = document.getElementById('resultStats');
    if (action === 'all' && (data.success_count !== undefined || data.failed_count !== undefined)) {
        statsEl.classList.remove('hidden');
        statsEl.innerHTML = `
            <div class="flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-100 rounded-lg px-3 py-1.5">
                <i class="fa-solid fa-check"></i> ${data.success_count ?? 0} başarılı
            </div>
            ${(data.failed_count || 0) > 0 ? `
            <div class="flex items-center gap-1.5 text-xs font-semibold text-red-700 bg-red-100 rounded-lg px-3 py-1.5">
                <i class="fa-solid fa-xmark"></i> ${data.failed_count} başarısız
            </div>` : ''}
        `;
    } else {
        statsEl.classList.add('hidden');
    }

    // Hata detayları
    const errorsEl = document.getElementById('resultErrors');
    if (data.errors && data.errors.length > 0) {
        errorsEl.classList.remove('hidden');
        errorsEl.innerHTML = `
            <p class="text-xs font-bold text-red-700 mb-1.5">Hata Detayları:</p>
            ${data.errors.map(e => `
                <div class="flex items-start gap-1.5 text-xs text-red-600 bg-red-100/50 rounded px-2.5 py-1.5">
                    <i class="fa-solid fa-triangle-exclamation shrink-0 mt-0.5 text-red-400"></i>
                    <span class="break-words">${escapeHtml(e)}</span>
                </div>
            `).join('')}
        `;
    } else {
        errorsEl.classList.add('hidden');
    }

    panel.classList.remove('hidden');
    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ─── Satır Durumu Güncelle ────────────────────────────────────────────────────
function updateRowStatus(productId, success, action) {
    const statusDiv  = document.getElementById(`status-${productId}`);
    const statusText = document.getElementById(`status-text-${productId}`);
    if (!statusDiv || !statusText) return;

    statusDiv.classList.remove('hidden');

    if (success && action === 'single') {
        statusText.innerHTML = '<i class="fa-solid fa-check text-green-500 mr-1"></i><span class="text-green-600">Merchant\'a gönderildi</span>';
    } else if (success && action === 'delete') {
        statusText.innerHTML = '<i class="fa-solid fa-trash text-red-400 mr-1"></i><span class="text-red-500">Merchant\'tan silindi</span>';
    } else {
        statusText.innerHTML = '<i class="fa-solid fa-xmark text-red-500 mr-1"></i><span class="text-red-600">Başarısız</span>';
    }
}

// ─── Yardımcılar ─────────────────────────────────────────────────────────────
function showModalError(msg) {
    const el = document.getElementById('modalError');
    document.getElementById('modalErrorText').textContent = msg;
    el.classList.remove('hidden');
    el.classList.add('flex');
    document.getElementById('adminPassword').classList.add('border-red-400', 'ring-1', 'ring-red-400');
}

function hideModalError() {
    const el = document.getElementById('modalError');
    el.classList.add('hidden');
    el.classList.remove('flex');
    document.getElementById('adminPassword').classList.remove('border-red-400', 'ring-1', 'ring-red-400');
}

function setSubmitting(state) {
    isSubmitting = state;
    const btn    = document.getElementById('confirmBtn');
    const icon   = document.getElementById('confirmBtnIcon');
    const text   = document.getElementById('confirmBtnText');
    const cancel = document.getElementById('cancelBtn');
    const pwInput = document.getElementById('adminPassword');

    if (state) {
        btn.disabled    = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        icon.className  = 'fa-solid fa-spinner fa-spin';
        text.textContent = 'İşleniyor...';
        cancel.disabled = true;
        pwInput.disabled = true;
    } else {
        btn.disabled    = false;
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
        cancel.disabled = false;
        pwInput.disabled = false;
    }
}

function resetConfirmBtn() {
    const btn  = document.getElementById('confirmBtn');
    btn.disabled = false;
    btn.classList.remove('opacity-75', 'cursor-not-allowed');
    document.getElementById('cancelBtn').disabled = false;
    document.getElementById('adminPassword').disabled = false;
}

function escapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// ESC ile kapat
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeSyncModal();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ahsapevim\resources\views\admin\merchant\index.blade.php ENDPATH**/ ?>