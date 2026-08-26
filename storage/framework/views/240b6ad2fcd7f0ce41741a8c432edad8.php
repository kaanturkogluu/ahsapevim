<?php $__env->startSection('title', 'Ödeme Bilgileri - AhşapEvim'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-[#F7F5F0] pb-12 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 font-serif">Ödeme ve Teslimat Bilgileri</h1>

        <?php if(session('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Side: Delivery Details Form -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 font-serif border-b border-gray-100 pb-3">Teslimat & Fatura Adresi</h2>
                    
                    <form id="checkoutForm" action="<?php echo e(route('checkout.process')); ?>" method="POST" onsubmit="return validateCheckoutForm(event)">
                        <?php echo csrf_field(); ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Ad Soyad *</label>
                                <input type="text" id="name" name="name" value="<?php echo e(old('name', auth()->user() ? auth()->user()->name : '')); ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#C87A53] focus:border-[#C87A53] transition">
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">E-Posta Adresi *</label>
                                <input type="email" id="email" name="email" value="<?php echo e(old('email', auth()->user() ? auth()->user()->email : '')); ?>" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#C87A53] focus:border-[#C87A53] transition">
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Telefon Numarası *</label>
                                <input type="text" id="phone" name="phone" placeholder="+90 5XX XXX XX XX" value="<?php echo e(old('phone', auth()->user() ? auth()->user()->phone : '')); ?>" required oninput="formatPhoneInput(this)" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#C87A53] focus:border-[#C87A53] transition font-mono">
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label for="identity_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                    T.C. Kimlik No *
                                    <span class="text-[11px] font-medium text-amber-700 block mt-0.5">(Bu bilgiler fatura kesimi için kullanılacaktır)</span>
                                </label>
                                <input type="text" id="identity_number" name="identity_number" maxlength="11" placeholder="11 haneli T.C. Kimlik No" value="<?php echo e(old('identity_number')); ?>" required oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#C87A53] focus:border-[#C87A53] transition font-mono">
                                <?php $__errorArgs = ['identity_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <!-- Select2 İl & İlçe Seçimi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="citySelect" class="block text-sm font-semibold text-gray-700 mb-2">İl (Şehir) *</label>
                                <select id="citySelect" name="city" required class="w-full px-4 py-3 border border-gray-200 rounded-lg">
                                    <option value="">İl Seçiniz...</option>
                                </select>
                            </div>

                            <div>
                                <label for="districtSelect" class="block text-sm font-semibold text-gray-700 mb-2">İlçe *</label>
                                <select id="districtSelect" name="district" required class="w-full px-4 py-3 border border-gray-200 rounded-lg">
                                    <option value="">Önce İl Seçiniz...</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="streetAddress" class="block text-sm font-semibold text-gray-700 mb-2">Açık Adres (Mahalle, Cadde, Sokak, Bina/Daire No) *</label>
                            <textarea id="streetAddress" rows="3" placeholder="Örn: Atatürk Mahallesi, Cumhuriyet Caddesi, No: 15 Daire: 4" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#C87A53] focus:border-[#C87A53] transition" oninput="updateFullAddress()"><?php echo e(old('street_address', auth()->user() ? auth()->user()->address : '')); ?></textarea>
                            <input type="hidden" id="address" name="address" value="<?php echo e(old('address', auth()->user() ? auth()->user()->address : '')); ?>">
                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Satıcıya Not / Sipariş Notu -->
                        <div class="mb-6 bg-amber-50/40 p-4 rounded-xl border border-amber-200/60">
                            <label for="note" class="block text-sm font-bold text-amber-950 mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-store text-[#C87A53]"></i> Satıcıya Not / Sipariş Notunuz (Opsiyonel)
                            </label>
                            <textarea id="note" name="note" rows="2" placeholder="Hediye notu haricinde satıcıya (AhşapEvim ekibine) iletmek istediğiniz özel notunuzu yazabilirsiniz..." class="w-full px-3.5 py-2.5 bg-white border border-amber-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#C87A53] focus:border-[#C87A53] transition text-xs text-gray-800"><?php echo e(old('note')); ?></textarea>
                        </div>

                        <!-- Ödeme Yöntemi Seçimi -->
                        <div class="mb-8 border-t border-gray-100 pt-6">
                            <label class="block text-sm font-bold text-gray-800 mb-3">Ödeme Yöntemi Seçiniz *</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <!-- Option 1: Credit / Debit Card -->
                                <label class="payment-method-card relative border-2 border-[#C87A53] bg-orange-50/40 p-4 rounded-xl cursor-pointer flex items-center gap-3 transition hover:shadow-sm">
                                    <input type="radio" name="payment_method" value="card" checked onchange="togglePaymentMethodDisplay()" class="text-[#C87A53] focus:ring-[#C87A53] w-4 h-4">
                                    <div>
                                        <span class="block text-sm font-extrabold text-gray-800 flex items-center gap-1.5">
                                            <i class="fa-solid fa-credit-card text-[#C87A53]"></i> Kredi / Banka Kartı
                                        </span>
                                        <span class="block text-[11px] text-gray-500 mt-0.5">Iyzico 256-Bit SSL Güvenli Ödeme</span>
                                    </div>
                                </label>

                                <!-- Option 2: Havale / EFT -->
                                <label class="payment-method-card relative border border-gray-200 hover:border-gray-300 p-4 rounded-xl cursor-pointer flex items-center gap-3 transition">
                                    <input type="radio" name="payment_method" value="eft" onchange="togglePaymentMethodDisplay()" class="text-[#C87A53] focus:ring-[#C87A53] w-4 h-4">
                                    <div>
                                        <span class="block text-sm font-extrabold text-gray-800 flex items-center gap-1.5">
                                            <i class="fa-solid fa-building-columns text-emerald-600"></i> Havale / EFT ile Ödeme
                                        </span>
                                        <span class="block text-[11px] text-gray-500 mt-0.5">Banka Hesabına Doğrudan Transfer</span>
                                    </div>
                                </label>
                            </div>

                            <!-- EFT Account Details Box -->
                            <div id="eftDetailsBox" class="p-4 bg-amber-50/90 border border-amber-200/90 rounded-2xl hidden space-y-3">
                                <div class="flex items-center justify-between border-b border-amber-200/60 pb-2">
                                    <div class="flex items-center gap-2 text-[#C87A53] font-extrabold text-sm">
                                        <i class="fa-solid fa-building-columns"></i>
                                        <span>Havale / EFT Hesap Bilgileri</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 text-[11px] font-black rounded-md flex items-center gap-1 shadow-sm">
                                        <i class="fa-solid fa-building text-[10px]"></i> Halkbank
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div class="bg-white p-3 rounded-xl border border-amber-100 shadow-sm">
                                        <span class="text-gray-500 font-bold block text-[10px] uppercase">Alıcı Ad Soyad</span>
                                        <span class="font-extrabold text-gray-900 text-sm">Mete Almaz</span>
                                    </div>
                                    <div class="bg-white p-3 rounded-xl border border-amber-100 flex items-center justify-between gap-2 shadow-sm">
                                        <div>
                                            <span class="text-gray-500 font-bold block text-[10px] uppercase">IBAN Numarası (Halkbank)</span>
                                            <span class="font-mono font-extrabold text-[#C87A53] text-xs sm:text-sm tracking-wider">TR67 0001 2009 5620 0009 0180 61</span>
                                        </div>
                                        <button type="button" onclick="navigator.clipboard.writeText('TR670001200956200009018061'); showToast('IBAN kopyalandı!', 'info');" class="px-2.5 py-1.5 bg-amber-100 text-amber-900 hover:bg-amber-200 rounded-lg font-bold text-[11px] transition shrink-0 flex items-center gap-1">
                                            <i class="fa-solid fa-copy"></i> Kopyala
                                        </button>
                                    </div>
                                </div>
                                <div class="p-3 bg-white border border-amber-200 rounded-xl text-xs text-amber-900 leading-relaxed font-medium">
                                    <i class="fa-solid fa-circle-info text-[#C87A53] mr-1"></i>
                                    <strong>Önemli Yönerge:</strong> EFT/Havale ödemelerinde açıklama kısmına <strong>Müşteri Adı - Soyadı ve Sipariş Numarasını</strong> yazarak ücreti göndermeniz gerekmektedir. Sipariş oluşturulduktan sonra takip numaranız ekranınızda görüntülenecektir.
                                </div>
                            </div>
                        </div>

                        <button id="submitCheckoutBtn" type="submit" class="w-full md:w-auto bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold py-3.5 px-8 rounded-xl transition shadow-md flex items-center justify-center gap-2 text-base">
                            <i class="fa-solid fa-lock"></i>
                            <span id="submitBtnText">Güvenli Ödemeye Devam Et</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Side: Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3 font-serif">Sipariş Özeti</h3>
                    
                    <div class="max-h-60 overflow-y-auto mb-6 divide-y divide-gray-100 pr-1">
                        <?php $total = 0; ?>
                        <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php 
                                $total += $item['price'] * $item['quantity']; 
                                $customImgUrl = !empty($item['custom_image']) ? (str_starts_with($item['custom_image'], 'http') ? $item['custom_image'] : url($item['custom_image'])) : null;
                                $itemImgUrl = !empty($item['image']) ? (str_starts_with($item['image'], 'http') ? $item['image'] : url($item['image'])) : url('/cerceve.png');
                            ?>
                            <div class="py-3 flex items-center gap-4">
                                <div class="w-14 h-14 bg-stone-100 rounded-lg border border-gray-200 flex-shrink-0 p-1 relative flex items-center justify-center overflow-hidden">
                                    <img src="<?php echo e($itemImgUrl); ?>" alt="<?php echo e($item['name']); ?>" class="max-w-full max-h-full object-contain">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-800 truncate"><?php echo e($item['name']); ?></div>
                                    <?php if($customImgUrl): ?>
                                        <div class="text-[11px] font-semibold text-amber-700 flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-camera"></i> Özel Fotoğraflı
                                        </div>
                                    <?php endif; ?>
                                    <?php if(!empty($item['is_gift']) || !empty($item['gift_note'])): ?>
                                        <div class="text-[10px] font-bold text-amber-900 bg-amber-100 border border-amber-200 px-1.5 py-0.5 rounded inline-block mt-0.5">
                                            🎁 Hediye Notu: <?php echo e($item['gift_note'] ?: 'Hediye Paketi'); ?>

                                        </div>
                                    <?php endif; ?>
                                    <div class="text-xs text-gray-500 mt-0.5"><?php echo e($item['quantity']); ?> adet x <?php echo e(number_format($item['price'], 2, ',', '.')); ?> TL</div>
                                </div>
                                <div class="text-sm font-bold text-gray-800">
                                    <?php echo e(number_format($item['price'] * $item['quantity'], 2, ',', '.')); ?> TL
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <div class="flex justify-between items-center mb-3 text-sm text-gray-600">
                        <span>Ürünler Toplamı</span>
                        <span class="font-bold"><?php echo e(number_format($total, 2, ',', '.')); ?> TL</span>
                    </div>
                    
                    <div class="flex justify-between items-center mb-4 text-sm text-gray-600">
                        <span>Kargo Ücreti</span>
                        <span class="text-green-600 font-bold">Ücretsiz</span>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-base font-bold text-gray-800">Genel Toplam</span>
                            <span class="text-2xl font-extrabold text-[#C87A53]"><?php echo e(number_format($total, 2, ',', '.')); ?> TL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select2 Assets & Custom Styling -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.select2-container--default .select2-selection--single {
    height: 48px !important;
    border-radius: 0.5rem !important;
    border-color: #e5e7eb !important;
    padding: 8px 12px !important;
    display: flex !important;
    align-items: center !important;
    background-color: #ffffff !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #374151 !important;
    font-size: 0.875rem !important;
    line-height: 1.25rem !important;
    padding-left: 0 !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 46px !important;
    right: 8px !important;
}
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #C87A53 !important;
    box-shadow: 0 0 0 2px rgba(200, 122, 83, 0.2) !important;
}
.select2-dropdown {
    border-color: #C87A53 !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    z-index: 9999 !important;
}
.select2-search__field {
    border-radius: 0.375rem !important;
    border-color: #d1d5db !important;
    padding: 6px 10px !important;
    outline: none !important;
}
.select2-results__option--highlighted[aria-selected] {
    background-color: #C87A53 !important;
    color: #ffffff !important;
}
</style>

<script>
const turkeyAddressData = {
    "Adana": ["Seyhan", "Çukurova", "Yüreğir", "Sarıçam", "Ceyhan", "Kozan", "Imamoğlu", "Karataş", "Karaisalı", "Pozantı", "Feke", "Yumurtalık", "Tufanbeyli", "Aladağ", "Saimbeyli"],
    "Adıyaman": ["Merkez", "Kahta", "Besni", "Gölbaşı", "Gerger", "Sincik", "Çelikhan", "Tut", "Samsat"],
    "Afyonkarahisar": ["Merkez", "Sandıklı", "Dinar", "Bolvadin", "Sinanpaşa", "Emirdağ", "Şuhut", "Çay", "İhsaniye", "İscehisar", "Dazkırı", "Başmakçı", "Evciler", "Bayat", "Çobanlar", "Hocalar", "Kızılören"],
    "Ağrı": ["Merkez", "Patnos", "Doğubayazıt", "Diyadin", "Eleşkirt", "Tutak", "Taşlıçay", "Hamur"],
    "Amasya": ["Merkez", "Merzifon", "Suluova", "Taşova", "Göynücek", "Hamamözü", "Gümüşhacıköy"],
    "Ankara": ["Çankaya", "Keçiören", "Yenimahalle", "Mamak", "Etimesgut", "Sincan", "Altındağ", "Gölbaşı", "Pursaklar", "Polatlı", "Çubuk", "Kahramankazan", "Beypazarı", "Elmadağ", "Haymana", "Nallıhan", "Kızılcahamam", "Şereflikoçhisar", "Bala", "Güdül", "Akyurt", "Ayaş", "Çamlıdere", "Evren", "Kalecik"],
    "Antalya": ["Muratpaşa", "Kepez", "Konyaaltı", "Alanya", "Manavgat", "Serik", "Aksu", "Döşemealtı", "Kumluca", "Kaş", "Kemer", "Gazipaşa", "Finike", "Korkuteli", "Elmalı", "Demre", "Akseki", "İbradı", "Gündoğmuş"],
    "Artvin": ["Merkez", "Hopa", "Borçka", "Arhavi", "Yusufeli", "Şavşat", "Ardanuç", "Murgul", "Kemalpaşa"],
    "Aydın": ["Efeler", "Nazilli", "Söke", "Kuşadası", "Didim", "İncirliova", "Çine", "Germencik", "Bozdoğan", "Köşk", "Kuyucak", "Sultanhisar", "Karacasu", "Yenipazar", "Karpuzlu", "Buharkent"],
    "Balıkesir": ["Karesi", "Altıeylül", "Bandırma", "Edremit", "Gönen", "Ayvalık", "Burhaniye", "Bigadiç", "Dursunbey", "Susurluk", "Erdek", "İvrindi", "Havran", "Sındırgı", "Manyas", "Savaştepe", "Gömeç", "Balya", "Marmara"],
    "Bilecik": ["Merkez", "Bozüyük", "Osmaneli", "Söğüt", "Gölpazarı", "Pazaryeri", "Yenipazar", "İnhisar"],
    "Bingöl": ["Merkez", "Genç", "Solhan", "Karlıova", "Adaklı", "Kiğı", "Yedisu", "Yayladere"],
    "Bitlis": ["Tatvan", "Merkez", "Güroymak", "Ahlat", "Hizan", "Mutki", "Adilcevaz"],
    "Bolu": ["Merkez", "Gerede", "Mudurnu", "Göynük", "Mengen", "Yeniçağa", "Dörtdivan", "Seben", "Kıbrıscık"],
    "Burdur": ["Merkez", "Bucak", "Gölhisar", "Yeşilova", "Çavdır", "Tefenni", "Ağlasun", "Karamanlı", "Kemer", "Altınyayla", "Çeltikçi"],
    "Bursa": ["Osmangazi", "Yıldırım", "Nilüfer", "İnegöl", "Gemlik", "Mustafakemalpaşa", "Mudanya", "Gürsu", "Karacabey", "Orhangazi", "Kestel", "Yenişehir", "Iznik", "Orhaneli", "Keles", "Büyükorhan", "Harmancık"],
    "Çanakkale": ["Merkez", "Biga", "Çan", "Gelibolu", "Yenice", "Ayvacık", "Ezine", "Bayramiç", "Lapseki", "Eceabat", "Gökçeada", "Bozcaada"],
    "Çankırı": ["Merkez", "Çerkeş", "Ilgaz", "Orta", "Şabanözü", "Kurşunlu", "Yapraklı", "Kızılırmak", "Atkaracalar", "Eldivan", "Korgun", "Bayramören"],
    "Çorum": ["Merkez", "Sungurlu", "Osmancık", "İskilip", "Alaca", "Bayat", "Kargı", "Mecitözü", "Dodurga", "Oğuzlar", "Uğurludağ", "Ortaköy", "Laçin", "Boğazkale"],
    "Denizli": ["Pamukkale", "Merkezefendi", "Çivril", "Acıpayam", "Tavas", "Honaz", "Sarayköy", "Buldan", "Kale", "Çal", "Serinhisar", "Çardak", "Bozkurt", "Güney", "Çameli", "Babadağ", "Beyağaç", "Baklan"],
    "Diyarbakır": ["Bağlar", "Kayapınar", "Yenişehir", "Sur", "Ergani", "Bismil", "Silvan", "Çınar", "Çermik", "Dicle", "Kulp", "Hani", "Lice", "Eğil", "Hazro", "Kocaköy", "Çüngüş"],
    "Edirne": ["Merkez", "Keşan", "Uzunköprü", "İpsala", "Havsa", "Meriç", "Enez", "Süloğlu", "Lalapaşa"],
    "Elazığ": ["Merkez", "Kovancılar", "Karakoçan", "Palu", "Arıcak", "Baskil", "Madeni", "Sivrice", "Keban", "Alacakaya", "Ağın"],
    "Erzincan": ["Merkez", "Tercan", "Üzümlü", "Refahiye", "Çayırlı", "Iliç", "Kemah", "Kemaliye", "Otlukbeli"],
    "Erzurum": ["Yakutiye", "Palandöken", "Aziziye", "Horasan", "Oltu", "Pasinler", "Karayazı", "Hınıs", "Tekman", "Karaçoban", "Aşkale", "Şenkaya", "Çat", "Tortum", "Köprüköy", "İspir", "Narman", "Uzundere", "Olur", "Pazaryolu"],
    "Eskişehir": ["Odunpazarı", "Tepebaşı", "Sivrihisar", "Çifteler", "Seyitgazi", "Alpu", "Mihalıççık", "Mahmudiye", "Beylikova", "İnönü", "Günyüzü", "Sarıcakaya", "Mihalgazi"],
    "Gaziantep": ["Şahinbey", "Şehitkamil", "Nizip", "İslahiye", "Nurdağı", "Araban", "Oğuzeli", "Yavuzeli", "Karkamış"],
    "Giresun": ["Merkez", "Bulancak", "Espiye", "Görele", "Tirebolu", "Şebinkarahisar", "Keşap", "Dereli", "Yağlıdere", "Piraziz", "Eynesil", "Alucra", "Çamoluk", "Güce", "Çanakçı", "Doğankent"],
    "Gümüşhane": ["Merkez", "Kelkit", "Şiran", "Köse", "Torul", "Kürtün"],
    "Hakkari": ["Yüksekova", "Merkez", "Şemdinli", "Çukurca", "Derecik"],
    "Hatay": ["Antakya", "Iskenderun", "Defne", "Dörtyol", "Samandağ", "Kırıkhan", "Reyhanlı", "Arsuz", "Altınözü", "Hassa", "Erzin", "Payas", "Belen", "Yayladağı", "Kumlu"],
    "Isparta": ["Merkez", "Yalvaç", "Eğirdir", "Şarkikaraağaç", "Gelendost", "Keçiborlu", "Senirkent", "Sütçüler", "Gönen", "Uluborlu", "Atabey", "Aksu", "Yenişarbademli"],
    "Mersin": ["Tarsus", "Toroslar", "Akdeniz", "Yenişehir", "Mezitli", "Erdemli", "Silifke", "Anamur", "Mut", "Bozyazı", "Gülnar", "Aydıncık", "Çamlıyayla"],
    "İstanbul": ["Esenyurt", "Küçükçekmece", "Bağcılar", "Ümraniye", "Pendik", "Bahçelievler", "Üsküdar", "Sultangazi", "Gaziosmanpaşa", "Maltepe", "Kartal", "Kadıköy", "Esenler", "Kâğıthane", "Fatih", "Avcılar", "Başakşehir", "Ataşehir", "Sancaktepe", "Eyüpsultan", "Beylikdüzü", "Sarıyer", "Sultanbeyli", "Zeytinburnu", "Güngören", "Arnavutköy", "Şişli", "Bayrampaşa", "Tuzla", "Büyükçekmece", "Çekmeköy", "Beykoz", "Zeytinburnu", "Bakırköy", "Beyoğlu", "Silivri", "Çatalca", "Şile", "Adalar"],
    "İzmir": ["Buca", "Karabağlar", "Bornova", "Konak", "Karşıyaka", "Bayraklı", "Çiğli", "Torbalı", "Menemen", "Gaziemir", "Ödemiş", "Kemalpaşa", "Bergama", "Aliağa", "Menderes", "Tire", "Balçova", "Narlıdere", "Urla", "Kiraz", "Dikili", "Çeşme", "Seferihisar", "Bayındır", "Foça", "Güzelbahçe", "Kınık", "Beydağ", "Karaburun"],
    "Kars": ["Merkez", "Kağızman", "Sarıkamış", "Digor", "Selim", "Arpaçay", "Akyaka", "Susuz"],
    "Kastamonu": ["Merkez", "Tosya", "Taşköprü", "Cide", "İnebolu", "Araç", "Devrekani", "Daday", "Bozkurt", "Azdavay", "Küre", "Şenpazar", "Abana", "İhsangazi", "Pınarbaşı", "Katalzeytin", "Seydiler", "Hanönü", "Ağlı"],
    "Kayseri": ["Melikgazi", "Kocasinan", "Talas", "Develi", "Yahyalı", "Bünyan", "Incesu", "Pınarbaşı", "Tomarza", "Yeşilhisar", "Sarıoğlan", "Hacılar", "Sarız", "Felahiye", "Akkışla", "Özvatan"],
    "Kırklareli": ["Merkez", "Lüleburgaz", "Babaeski", "Vize", "Pınarhisar", "Demirköy", "Pehlivanköy", "Kofçaz"],
    "Kırşehir": ["Merkez", "Kaman", "Mucur", "Çiçekdağı", "Akpınar", "Akçakent", "Boztepe"],
    "Kocaeli": ["Gebze", "İzmit", "Darıca", "Körfez", "Gölcük", "Derince", "Çayırova", "Kartepe", "Başiskele", "Kandıra", "Karamürsel", "Dilovası"],
    "Konya": ["Selçuklu", "Karatay", "Meram", "Ereğli", "Akşehir", "Beyşehir", "Çumra", "Seydişehir", "Cihanbeyli", "Kulu", "Karapınar", "Kadınhanı", "Sarayönü", "Bozkır", "Yalıhüyük", "Hüyük", "Altınekin", "Derbent", "Ilgın", "Hadim", "Taşkent"],
    "Kütahya": ["Merkez", "Tavşanlı", "Simav", "Gediz", "Emet", "Altıntaş", "Domaniç", "Hisarcık", "Aslanapa", "Çavdarhisar", "Şaphane", "Pazarlar", "Dumlupınar"],
    "Malatya": ["Battalgazi", "Yeşilyurt", "Doğanşehir", "Darende", "Akçadağ", "Hekimhan", "Pütürge", "Yazıhan", "Arapgir", "Arguvan", "Kuluncak", "Kale", "Doğanyol"],
    "Manisa": ["Yunusemre", "Şehzadeler", "Akhisar", "Turgutlu", "Salihli", "Soma", "Alaşehir", "Saruhanlı", "Kırkağaç", "Demirci", "Kula", "Sarıgöl", "Gördes", "Selendi", "Ahmetli", "Köprübaşı"],
    "Kahramanmaraş": ["Onikişubat", "Dulkadiroğlu", "Elbistan", "Afşin", "Türkoğlu", "Pazarcık", "Göksun", "Andırın", "Çağlayancerit", "Ekinözü", "Nurhak"],
    "Mardin": ["Kızıltepe", "Artuklu", "Midyat", "Nusaybin", "Derik", "Mazıdağı", "Dargeçit", "Savur", "Yeşilli", "Ömerli"],
    "Muğla": ["Bodrum", "Fethiye", "Milas", "Menteşe", "Marmaris", "Seydikemer", "Dalaman", "Yatağan", "Ortaca", "Ula", "Datça", "Köyceğiz", "Kavaklıdere"],
    "Muş": ["Merkez", "Bulanık", "Malazgirt", "Varto", "Hasköy", "Korkut"],
    "Nevşehir": ["Merkez", "Ürgüp", "Avanos", "Gülşehir", "Derinkuyu", "Acıgöl", "Kozaklı", "Hacıbektaş"],
    "Niğde": ["Merkez", "Bor", "Çiftlik", "Ulukışla", "Altunhisar", "Çamardı"],
    "Ordu": ["Altınordu", "Ünye", "Fatsa", "Gölköy", "Perşembe", "Kumru", "Korgan", "Aybastı", "Ulubey", "İkizce", "Gürgentepe", "Çatalpınar", "Çaybaşı", "Mesudiye", "Kabadüz", "Kabataş", "Çamaş"],
    "Rize": ["Merkez", "Çayeli", "Ardeşen", "Pazar", "Fındıklı", "Güneysu", "Kalkandere", "İkizdere", "Derepazarı", "Çamlıhemşin", "Hemşin"],
    "Sakarya": ["Adapazarı", "Serdivan", "Akyazı", "Erenler", "Hendek", "Karasu", "Geyve", "Arifiye", "Sapanca", "Pamukova", "Kocaali", "Kaynarca", "Söğütlü", "Ferizli", "Karapürçek", "Taraklı"],
    "Samsun": ["İlkadım", "Atakum", "Bafra", "Çarşamba", "Canik", "Vezirköprü", "Terme", "Tekkeköy", "Havza", "Alaçam", "19 Mayis", "Kavak", "Salıpazarı", "Ayvacık", "Yakakent", "Asarcık", "Ladik"],
    "Siirt": ["Merkez", "Kurtalan", "Pervari", "Baykan", "Şirvan", "Eruh", "Tillo"],
    "Sinop": ["Merkez", "Boyabat", "Gerze", "Ayancık", "Durağan", "Türkeli", "Erfelek", "Dikmen", "Saraydüzü"],
    "Sivas": ["Merkez", "Şarkışla", "Yıldızeli", "Suşehri", "Gemerek", "Zara", "Kangal", "Gürün", "Divriği", "Koyulhisar", "Altınyayla", "Hafik", "Ulaş", "İmranlı", "Gölova", "Doğanşar"],
    "Tekirdağ": ["Çorlu", "Süleymanpaşa", "Çerkezköy", "Kapaklı", "Ergene", "Malkara", "Saray", "Hayrabolu", "Şarköy", "Muratlı", "Marmaraereğlisi"],
    "Tokat": ["Merkez", "Erbaa", "Turhal", "Niksar", "Zile", "Reşadiye", "Almus", "Pazar", "Yeşilyurt", "Artova", "Sulusaray", "Başçiftlik"],
    "Trabzon": ["Ortahisar", "Akçaabat", "Araklı", "Of", "Yomra", "Arsin", "Vakfıkebir", "Sürmene", "Çarşıbaşı", "Beşikdüzü", "Maçka", "Çaykara", "Tonya", "Düzköy", "Şalpazarı", "Hayrat", "Köprübaşı", "Dernekpazarı"],
    "Tunceli": ["Merkez", "Pertek", "Mazgirt", "Çemişgezek", "Hozat", "Ovacık", "Pülümür", "Nazımiye"],
    "Şanlıurfa": ["Eyyübiye", "Haliliye", "Siverek", "Viranşehir", "Karaköprü", "Suruç", "Birecik", "Ceylanpınar", "Harran", "Bozova", "Akçakale", "Hilvan", "Halfeti"],
    "Uşak": ["Merkez", "Banaz", "Eşme", "Sivaslı", "Ulubey", "Karahallı"],
    "Van": ["Ipekyolu", "Tuşba", "Edremit", "Erciş", "Özalp", "Çaldıran", "Muradiye", "Gürpınar", "Başkale", "Gevaş", "Çatak", "Saray", "Bahçesaray"],
    "Yozgat": ["Merkez", "Sorgun", "Akdağmadeni", "Yerköy", "Boğazlıyan", "Sarıkaya", "Çekerek", "Şefaatli", "Saraykent", "Çayıralan", "Kadışehri", "Aydıncık", "Yenifakılı", "Chandır"],
    "Zonguldak": ["Merkez", "Ereğli", "Çaycuma", "Devrek", "Kozlu", "Kilimli", "Alaplı", "Gökçebey"],
    "Aksaray": ["Merkez", "Ortaköy", "Eskil", "Gülağaç", "Güzelyurt", "Sultanhanı", "Ağaçören", "Sarıyahşi"],
    "Bayburt": ["Merkez", "Demirözü", "Aydıntepe"],
    "Karaman": ["Merkez", "Ermenek", "Sarıveliler", "Kazımkarabekir", "Başyayla", "Ayrancı"],
    "Kırıkkale": ["Merkez", "Yahşihan", "Keskin", "Delice", "Bahşılı", "Sulakyurt", "Balışeyh", "Karakeçili", "Çelebi"],
    "Batman": ["Merkez", "Merkez", "Kozluk", "Sason", "Beşiri", "Gercüş", "Hasankeyf"],
    "Şırnak": ["Cizre", "Silopi", "Merkez", "İdil", "Uludere", "Beytüşşebap", "Güçlükonak"],
    "Bartın": ["Merkez", "Ulus", "Amasra", "Kurucaşile"],
    "Ardahan": ["Merkez", "Göle", "Çıldır", "Hanak", "Posof", "Damal"],
    "Iğdır": ["Merkez", "Tuzluca", "Aralık", "Karakoyunlu"],
    "Yalova": ["Merkez", "Çiftlikköy", "Çınarcık", "Altınova", "Armutlu", "Termal"],
    "Karabük": ["Merkez", "Safranbolu", "Yenice", "Eskipazar", "Eflani", "Ovacık"],
    "Kilis": ["Merkez", "Elbeyli", "Musabeyli", "Polateli"],
    "Osmaniye": ["Merkez", "Kadirli", "Düziçi", "Bahçe", "Toprakkale", "Sumbas", "Hasanbeyli"],
    "Düzce": ["Merkez", "Akçakoca", "Kaynaşlı", "Gölyaka", "Çilimli", "Yığılca", "Gümüşova", "Cumayeri"]
};

function updateFullAddress() {
    const city = $('#citySelect').val() || '';
    const district = $('#districtSelect').val() || '';
    const street = $('#streetAddress').val() || '';
    
    let full = '';
    if (city && district) {
        full = 'İl: ' + city + ' / İlçe: ' + district + ' - ' + street;
    } else if (city) {
        full = 'İl: ' + city + ' - ' + street;
    } else {
        full = street;
    }
    
    $('#address').val(full);
}

$(document).ready(function() {
    const citySelect = $('#citySelect');
    const districtSelect = $('#districtSelect');

    // Populate Cities
    Object.keys(turkeyAddressData).forEach(function(cityName) {
        citySelect.append(new Option(cityName, cityName, false, false));
    });

    // Initialize Select2
    citySelect.select2({
        placeholder: "İl Seçiniz...",
        allowClear: true,
        width: '100%'
    });

    districtSelect.select2({
        placeholder: "Önce İl Seçiniz...",
        allowClear: true,
        width: '100%'
    });

    // On City Change -> Update Districts
    citySelect.on('change', function() {
        const selectedCity = $(this).val();
        districtSelect.empty().append(new Option('İlçe Seçiniz...', '', false, false));

        if (selectedCity && turkeyAddressData[selectedCity]) {
            turkeyAddressData[selectedCity].forEach(function(distName) {
                districtSelect.append(new Option(distName, distName, false, false));
            });
            districtSelect.prop('disabled', false);
        } else {
            districtSelect.append(new Option('Önce İl Seçiniz...', '', false, false));
            districtSelect.prop('disabled', true);
        }

        districtSelect.trigger('change.select2');
        updateFullAddress();
    });

    districtSelect.on('change', function() {
        updateFullAddress();
    });

    // Format phone initially if value exists
    const phoneEl = document.getElementById('phone');
    if (phoneEl && phoneEl.value) {
        formatPhoneInput(phoneEl);
    }
});

// Phone Input Mask (+90 5XX XXX XX XX)
function formatPhoneInput(input) {
    let digits = input.value.replace(/\D/g, '');
    if (digits.startsWith('90')) digits = digits.substring(2);
    if (digits.startsWith('0')) digits = digits.substring(1);
    digits = digits.substring(0, 10);

    let formatted = '+90 ';
    if (digits.length > 0) {
        formatted += digits.substring(0, 3);
    }
    if (digits.length >= 4) {
        formatted += ' ' + digits.substring(3, 6);
    }
    if (digits.length >= 7) {
        formatted += ' ' + digits.substring(6, 8);
    }
    if (digits.length >= 9) {
        formatted += ' ' + digits.substring(8, 10);
    }

    input.value = formatted;
}

// TC Kimlik No Verification Algorithm
function isValidTcNo(tc) {
    tc = tc.toString().trim();
    if (!/^[1-9]\d{10}$/.test(tc)) return false;

    let digits = tc.split('').map(Number);
    let oddSum = digits[0] + digits[2] + digits[4] + digits[6] + digits[8];
    let evenSum = digits[1] + digits[3] + digits[5] + digits[7];

    let d10 = ((oddSum * 7) - evenSum) % 10;
    if (d10 < 0) d10 += 10;
    if (d10 !== digits[9]) return false;

    let totalSum = 0;
    for (let i = 0; i < 10; i++) totalSum += digits[i];
    if ((totalSum % 10) !== digits[10]) return false;

    return true;
}

// Toggle EFT Box & Radio Styling
function togglePaymentMethodDisplay() {
    const selected = document.querySelector('input[name="payment_method"]:checked')?.value;
    const eftBox = document.getElementById('eftDetailsBox');
    const cards = document.querySelectorAll('.payment-method-card');

    cards.forEach(card => {
        const radio = card.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            card.classList.add('border-2', 'border-[#C87A53]', 'bg-orange-50/40');
            card.classList.remove('border-gray-200');
        } else {
            card.classList.remove('border-2', 'border-[#C87A53]', 'bg-orange-50/40');
            card.classList.add('border-gray-200');
        }
    });

    if (eftBox) {
        if (selected === 'eft') {
            eftBox.classList.remove('hidden');
        } else {
            eftBox.classList.add('hidden');
        }
    }
}

// Checkout Submit Validation & Anti-Spam
let isSubmittingCheckout = false;
function validateCheckoutForm(e) {
    if (isSubmittingCheckout) {
        e.preventDefault();
        return false;
    }

    const tcInput = document.getElementById('identity_number');
    const tcVal = tcInput ? tcInput.value.trim() : '';

    if (!isValidTcNo(tcVal)) {
        e.preventDefault();
        showToast('Lütfen geçerli 11 haneli bir T.C. Kimlik Numarası giriniz.', 'error');
        if (tcInput) tcInput.focus();
        return false;
    }

    const phoneInput = document.getElementById('phone');
    const phoneVal = phoneInput ? phoneInput.value.replace(/\D/g, '') : '';
    if (phoneVal.length < 10) {
        e.preventDefault();
        showToast('Lütfen geçerli 10 haneli cep telefon numaranızı giriniz.', 'error');
        if (phoneInput) phoneInput.focus();
        return false;
    }

    // Disable button & show spinner
    isSubmittingCheckout = true;
    const btn = document.getElementById('submitCheckoutBtn');
    const btnText = document.getElementById('submitBtnText');

    if (btn && btnText) {
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btnText.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-1"></i> Sipariş İşleniyor...`;
    }

    return true;
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u111121823/domains/ahsapevimmanisa.com/public_html/resources/views/checkout/index.blade.php ENDPATH**/ ?>