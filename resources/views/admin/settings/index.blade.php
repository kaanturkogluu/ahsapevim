@extends('layouts.admin')

@section('title', 'Sistem & Bildirim Ayarları')
@section('header', 'Sistem Ayarları')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2.5">
                <i class="fa-solid fa-sliders text-[#C87A53]"></i>
                <span>Sistem ve Bildirim Ayarları</span>
            </h2>
            <p class="text-xs text-gray-500 mt-1">
                Yönetici telefon ve e-posta bildirimlerini, SMS/Netgsm entegrasyonunu, kuyruk (queue) ve mağaza iletişim bilgilerini yönetin.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Kuyruk: {{ config('queue.default', 'database') }}
            </span>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50/70 px-4 pt-3 flex flex-wrap gap-2">
            <button type="button" onclick="switchTab('tab-notifications')" id="btn-tab-notifications"
                    class="tab-btn active px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors flex items-center gap-2 border-b-2 border-[#C87A53] text-[#C87A53] bg-white shadow-xs">
                <i class="fa-solid fa-bell text-sm"></i>
                <span>Sipariş & Bildirimler</span>
            </button>

            <button type="button" onclick="switchTab('tab-sms')" id="btn-tab-sms"
                    class="tab-btn px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors flex items-center gap-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100/50">
                <i class="fa-solid fa-comment-sms text-sm"></i>
                <span>SMS (Netgsm) Entegrasyonu</span>
            </button>

            <button type="button" onclick="switchTab('tab-email')" id="btn-tab-email"
                    class="tab-btn px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors flex items-center gap-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100/50">
                <i class="fa-solid fa-envelope text-sm"></i>
                <span>E-Posta & SMTP</span>
            </button>

            <button type="button" onclick="switchTab('tab-queue')" id="btn-tab-queue"
                    class="tab-btn px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors flex items-center gap-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100/50">
                <i class="fa-solid fa-server text-sm"></i>
                <span>Kuyruk (Queue) Mimarisi</span>
            </button>

            <button type="button" onclick="switchTab('tab-facebook')" id="btn-tab-facebook"
                    class="tab-btn px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors flex items-center gap-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100/50">
                <i class="fa-brands fa-facebook text-sm text-[#1877F2]"></i>
                <span>Meta &amp; Facebook (Pixel &amp; XML)</span>
            </button>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6">
            @csrf

            {{-- ── TAB 1: SİPARİŞ & BİLDİRİM AYARLARI ── --}}
            <div id="tab-notifications" class="tab-content space-y-6">
                
                {{-- Admin İletişim Bilgileri --}}
                <div class="bg-[#FAF9F6] p-5 rounded-2xl border border-[#EFEAE0]">
                    <h3 class="text-sm font-bold text-gray-900 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-user-shield text-[#C87A53]"></i>
                        <span>Yönetici Bildirim Hedefleri</span>
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Mağazaya her yeni sipariş (Havale/EFT veya Kredi Kartı) geldiğinde bildirimin gideceği yönetici e-posta ve telefon numarası.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Yönetici Bildirim E-Posta Adresi
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-envelope text-xs"></i>
                                </div>
                                <input type="email" name="admin_email"
                                       value="{{ old('admin_email', $settings['notifications']['admin_email'] ?? '') }}"
                                       placeholder="ornek@ahsapevimmanisa.com"
                                       class="w-full pl-9 pr-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:border-[#C87A53] focus:ring-1 focus:ring-[#C87A53] outline-none transition bg-white">
                            </div>
                            <span class="text-[11px] text-gray-400 mt-1 block">Detaylı sipariş özeti ve ürün listesi bu e-postaya iletilir.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Yönetici Bildirim Telefon Numarası (SMS)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-phone text-xs"></i>
                                </div>
                                <input type="text" name="admin_phone"
                                       value="{{ old('admin_phone', $settings['notifications']['admin_phone'] ?? '') }}"
                                       placeholder="0532 123 45 67 veya 8503074917"
                                       class="w-full pl-9 pr-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:border-[#C87A53] focus:ring-1 focus:ring-[#C87A53] outline-none transition bg-white">
                            </div>
                            <span class="text-[11px] text-gray-400 mt-1 block">Netgsm SMS API üzerinden anlık SMS uyarısı bu numaraya gönderilir.</span>
                        </div>
                    </div>
                </div>

                {{-- Bildirim Açma / Kapatma Anahtarları --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-200">
                    <h3 class="text-sm font-bold text-gray-900 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-toggle-on text-emerald-600"></i>
                        <span>Otomatik Bildirim Kanalları</span>
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Yeni sipariş oluştuğunda kuyruk üzerinden hangi bildirimlerin aktif olacağını belirleyin.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        {{-- Admin E-Posta Toggle --}}
                        <label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 hover:border-amber-300 bg-gray-50/50 hover:bg-amber-50/30 cursor-pointer transition">
                            <input type="checkbox" name="notify_admin_email" value="1"
                                   {{ ($settings['notifications']['notify_admin_email'] ?? '1') == '1' ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-[#C87A53] rounded border-gray-300 focus:ring-[#C87A53]">
                            <div>
                                <div class="text-xs font-bold text-gray-900">Yönetici E-Posta Bildirimi</div>
                                <div class="text-[11px] text-gray-500 leading-relaxed mt-0.5">
                                    Yeni siparişte yönetici e-posta adresine zengin HTML sipariş tablosu ve müşteri bilgileri gönderilsin.
                                </div>
                            </div>
                        </label>

                        {{-- Admin SMS Toggle --}}
                        <label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 hover:border-amber-300 bg-gray-50/50 hover:bg-amber-50/30 cursor-pointer transition">
                            <input type="checkbox" name="notify_admin_sms" value="1"
                                   {{ ($settings['notifications']['notify_admin_sms'] ?? '1') == '1' ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-[#C87A53] rounded border-gray-300 focus:ring-[#C87A53]">
                            <div>
                                <div class="text-xs font-bold text-gray-900">Yönetici SMS Bildirimi</div>
                                <div class="text-[11px] text-gray-500 leading-relaxed mt-0.5">
                                    Yeni sipariş oluştuğunda yönetici telefonuna Netgsm üzerinden anlık SMS uyarısı gönderilsin.
                                </div>
                            </div>
                        </label>

                        {{-- Müşteri E-Posta Toggle --}}
                        <label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 hover:border-amber-300 bg-gray-50/50 hover:bg-amber-50/30 cursor-pointer transition">
                            <input type="checkbox" name="notify_customer_email" value="1"
                                   {{ ($settings['notifications']['notify_customer_email'] ?? '1') == '1' ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-[#C87A53] rounded border-gray-300 focus:ring-[#C87A53]">
                            <div>
                                <div class="text-xs font-bold text-gray-900">Müşteri E-Posta Onayı</div>
                                <div class="text-[11px] text-gray-500 leading-relaxed mt-0.5">
                                    Sipariş veren müşteriye otomatik "Siparişiniz Alındı" e-postası iletilsin.
                                </div>
                            </div>
                        </label>

                        {{-- Müşteri SMS Toggle --}}
                        <label class="flex items-start gap-3 p-4 rounded-xl border border-gray-200 hover:border-amber-300 bg-gray-50/50 hover:bg-amber-50/30 cursor-pointer transition">
                            <input type="checkbox" name="notify_customer_sms" value="1"
                                   {{ ($settings['notifications']['notify_customer_sms'] ?? '1') == '1' ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-[#C87A53] rounded border-gray-300 focus:ring-[#C87A53]">
                            <div>
                                <div class="text-xs font-bold text-gray-900">Müşteri SMS Bildirimi</div>
                                <div class="text-[11px] text-gray-500 leading-relaxed mt-0.5">
                                    Sipariş veren müşteriye Netgsm ile sipariş alındı SMS onayı iletilsin.
                                </div>
                            </div>
                        </label>

                    </div>
                </div>

                {{-- Bildirim Şablonları & Özel Alanlar --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-file-pen text-purple-600"></i>
                        <span>Yönetici Bildirim Şablonları</span>
                    </h3>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Yöneticiye Gönderilecek SMS Metni
                        </label>
                        <textarea name="admin_sms_template" rows="3"
                                  class="w-full p-3 text-xs font-mono border border-gray-300 rounded-xl focus:border-[#C87A53] focus:ring-1 focus:ring-[#C87A53] outline-none transition bg-white">{{ old('admin_sms_template', $settings['notifications']['admin_sms_template'] ?? '') }}</textarea>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="text-[10px] text-gray-500 font-semibold">Kullanılabilir Değişkenler:</span>
                            <code class="text-[10px] bg-gray-100 text-[#C87A53] px-1.5 py-0.5 rounded font-mono font-bold">{order_id}</code>
                            <code class="text-[10px] bg-gray-100 text-[#C87A53] px-1.5 py-0.5 rounded font-mono font-bold">{total_amount}</code>
                            <code class="text-[10px] bg-gray-100 text-[#C87A53] px-1.5 py-0.5 rounded font-mono font-bold">{user_name}</code>
                            <code class="text-[10px] bg-gray-100 text-[#C87A53] px-1.5 py-0.5 rounded font-mono font-bold">{user_phone}</code>
                            <code class="text-[10px] bg-gray-100 text-[#C87A53] px-1.5 py-0.5 rounded font-mono font-bold">{tracking_code}</code>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Yönetici E-Posta Konusu
                        </label>
                        <input type="text" name="admin_email_subject"
                               value="{{ old('admin_email_subject', $settings['notifications']['admin_email_subject'] ?? '') }}"
                               class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:border-[#C87A53] focus:ring-1 focus:ring-[#C87A53] outline-none transition bg-white">
                    </div>
                </div>

            </div>

            {{-- ── TAB 2: SMS (NETGSM) ENTEGRASYONU ── --}}
            <div id="tab-sms" class="tab-content hidden space-y-6">
                
                <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-200 text-xs text-amber-900 flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-amber-600 text-base mt-0.5"></i>
                    <div>
                        <strong>Netgsm REST v2 JSON Entegrasyonu:</strong> Aşağıdaki alanları doldurarak Netgsm hesabınızı bağlayabilirsiniz. Alanlar boş bırakılırsa <code>.env</code> dosyasındaki mevcut ayarlar kullanılır.
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Netgsm Kullanıcı Kodu (Usercode)
                        </label>
                        <input type="text" name="netgsm_usercode"
                               value="{{ old('netgsm_usercode', $settings['sms']['netgsm_usercode'] ?? '') }}"
                               placeholder="8503074917"
                               class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:border-[#C87A53] focus:ring-1 focus:ring-[#C87A53] outline-none transition bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Netgsm Şifre (Password)
                        </label>
                        <input type="password" name="netgsm_password"
                               value="{{ old('netgsm_password', $settings['sms']['netgsm_password'] ?? '') }}"
                               placeholder="••••••••"
                               class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:border-[#C87A53] focus:ring-1 focus:ring-[#C87A53] outline-none transition bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Gönderici Başlığı (Header / Originator)
                        </label>
                        <input type="text" name="netgsm_header"
                               value="{{ old('netgsm_header', $settings['sms']['netgsm_header'] ?? '') }}"
                               placeholder="Mete Almaz"
                               class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:border-[#C87A53] focus:ring-1 focus:ring-[#C87A53] outline-none transition bg-white">
                    </div>
                </div>

                {{-- Hızlı Test SMS Bölümü --}}
                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200">
                    <h4 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane text-[#C87A53]"></i>
                        <span>Anlık Test SMS Gönder</span>
                    </h4>
                    <p class="text-xs text-gray-500 mb-3">
                        Girdiğiniz kimlik bilgileriyle doğrudan test mesajı göndererek SMS gateway bağlantısını doğrulayın.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center gap-2 max-w-md">
                        <input type="text" id="quickTestPhone" placeholder="05XXXXXXXXX"
                               value="{{ $settings['notifications']['admin_phone'] ?? '' }}"
                               class="w-full px-3.5 py-2 text-xs border border-gray-300 rounded-xl bg-white focus:border-[#C87A53] outline-none">
                        <button type="button" onclick="sendQuickTestSms()"
                                class="w-full sm:w-auto px-4 py-2 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold text-xs rounded-xl shadow-xs transition shrink-0">
                            Test SMS Gönder
                        </button>
                    </div>
                </div>

            </div>

            {{-- ── TAB 3: E-POSTA & SMTP AYARLARI ── --}}
            <div id="tab-email" class="tab-content hidden space-y-6">
                
                <div class="bg-blue-50/70 p-4 rounded-2xl border border-blue-200 text-xs text-blue-900 flex items-start gap-3">
                    <i class="fa-solid fa-info-circle text-blue-600 text-base mt-0.5"></i>
                    <div>
                        <strong>Mevcut SMTP Sunucu Yapılandırması:</strong> E-posta gönderimi sistemdeki <code>.env</code> SMTP ayarları üzerinden yürütülmektedir.
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="text-gray-400 font-bold uppercase text-[10px]">MAIL MAILER</div>
                        <div class="font-bold text-gray-800 mt-1 font-mono">{{ config('mail.default', 'smtp') }}</div>
                    </div>

                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="text-gray-400 font-bold uppercase text-[10px]">MAIL HOST</div>
                        <div class="font-bold text-gray-800 mt-1 font-mono">{{ config('mail.mailers.smtp.host', 'smtp.hostinger.com') }}</div>
                    </div>

                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="text-gray-400 font-bold uppercase text-[10px]">PORT & ŞİFRELEME</div>
                        <div class="font-bold text-gray-800 mt-1 font-mono">{{ config('mail.mailers.smtp.port', '465') }} ({{ config('mail.mailers.smtp.encryption', 'ssl') }})</div>
                    </div>

                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="text-gray-400 font-bold uppercase text-[10px]">GÖNDERİCİ (FROM)</div>
                        <div class="font-bold text-gray-800 mt-1 font-mono truncate">{{ config('mail.from.address', 'info@ahsapevimmanisa.com') }}</div>
                    </div>
                </div>

                {{-- Hızlı Test E-Postası Bölümü --}}
                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200">
                    <h4 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane text-blue-600"></i>
                        <span>Anlık Test E-Postası Gönder</span>
                    </h4>
                    <p class="text-xs text-gray-500 mb-3">
                        SMTP sunucu bağlantısını test etmek için yönetici adresinize hemen bir deneme e-postası yollayın.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center gap-2 max-w-md">
                        <input type="email" id="quickTestEmail" placeholder="ornek@ahsapevim.com"
                               value="{{ $settings['notifications']['admin_email'] ?? '' }}"
                               class="w-full px-3.5 py-2 text-xs border border-gray-300 rounded-xl bg-white focus:border-[#C87A53] outline-none">
                        <button type="button" onclick="sendQuickTestEmail()"
                                class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition shrink-0">
                            Test E-Postası Gönder
                        </button>
                    </div>
                </div>

            </div>

            {{-- ── TAB 4: KUYRUK (QUEUE) MİMARİSİ ── --}}
            <div id="tab-queue" class="tab-content hidden space-y-6">
                
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-5 rounded-2xl border border-emerald-200">
                    <h3 class="text-sm font-bold text-emerald-900 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-network-wired text-emerald-600"></i>
                        <span>Asenkron Kuyruk (Queue) Sistemi Aktif</span>
                    </h3>
                    <p class="text-xs text-emerald-800 leading-relaxed">
                        Sipariş bildirimi e-postaları ve SMS'ler <strong>SendNewOrderNotificationJob</strong> kuyruk sınıfı üzerinden arkaplanda asenkron olarak işlenir. Bu sayede müşterilerinizin ödeme tamamlama ekranı (Iyzico ve Havale) hiçbir gecikme yaşamadan anında sonuçlanır.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="text-gray-400 font-bold uppercase text-[10px]">KUYRUK SÜRÜCÜSÜ (DRIVER)</div>
                        <div class="font-extrabold text-gray-800 text-sm mt-1 font-mono">{{ config('queue.default', 'database') }}</div>
                        <div class="text-[11px] text-gray-500 mt-1">Veritabanı tablosu: <code>jobs</code></div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="text-gray-400 font-bold uppercase text-[10px]">İŞ DENEME HAKKI (TRIES)</div>
                        <div class="font-extrabold text-gray-800 text-sm mt-1 font-mono">3 Deneme</div>
                        <div class="text-[11px] text-gray-500 mt-1">Başarısız olursa otomatik yeniden denenir</div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="text-gray-400 font-bold uppercase text-[10px]">İŞLENEN KUYRUK İŞİ</div>
                        <div class="font-extrabold text-gray-800 text-sm mt-1 font-mono">SendNewOrderNotificationJob</div>
                        <div class="text-[11px] text-gray-500 mt-1">Admin E-Posta + SMS + Müşteri Logları</div>
                    </div>
                </div>

                <div class="p-4 bg-gray-100 rounded-xl border border-gray-200 text-xs">
                    <div class="font-bold text-gray-700 mb-1">💡 Sunucuda Kuyruk İşçisini Başlatma (Production):</div>
                    <code class="block p-2 bg-gray-900 text-amber-400 rounded-lg font-mono text-[11px]">
                        php artisan queue:work --tries=3 --timeout=60
                    </code>
                </div>

            </div>

            {{-- ── TAB 5: META & FACEBOOK (PIXEL & XML FEED) ── --}}
            <div id="tab-facebook" class="tab-content hidden space-y-6">
                
                <div class="bg-blue-50/70 p-4 rounded-2xl border border-blue-200 text-xs text-blue-950 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#1877F2] text-white flex items-center justify-center shrink-0 text-base shadow-sm">
                        <i class="fa-brands fa-facebook-f"></i>
                    </div>
                    <div class="leading-relaxed">
                        <strong class="text-sm font-bold text-blue-900 block mb-0.5">Meta (Facebook & Instagram) Entegrasyon Merkezi</strong>
                        Tarayıcı Meta Pikseli, sunucu taraflı Dönüşümler API'si (Conversions API - CAPI) ve Meta Ticaret Yöneticisi (Commerce Manager) için XML Ürün Kataloğu akışınız bu alandan yönetilmektedir.
                    </div>
                </div>

                {{-- XML Katalog Linkleri Kartı --}}
                <div class="bg-gradient-to-br from-white to-amber-50/30 p-5 rounded-2xl border border-amber-200 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-amber-200/60">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                <i class="fa-solid fa-square-rss text-[#C87A53]"></i>
                                <span>Facebook & Instagram XML Ürün Kataloğu Bağlantısı</span>
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Meta Ticaret Yöneticisi (Commerce Manager / Business Suite) kataloğunuza otomatik güncellenen veri akışı olarak bağlayın.
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 text-[11px] font-extrabold rounded-full shrink-0">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Otomatik & Canlı XML
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1">
                                🔗 1. Facebook Katalog Akış Linki (Önerilen)
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly id="fbCatalogFeedUrl"
                                       value="{{ url('/facebook-catalog.xml') }}"
                                       class="w-full px-3.5 py-2.5 text-xs font-mono bg-white border border-amber-300 rounded-xl text-[#C87A53] font-bold select-all outline-none">
                                <button type="button" onclick="navigator.clipboard.writeText('{{ url('/facebook-catalog.xml') }}'); showToast('Facebook Katalog linki kopyalandı!', 'success');"
                                        class="px-4 py-2.5 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold text-xs rounded-xl shadow-xs transition shrink-0 flex items-center gap-1.5">
                                    <i class="fa-solid fa-copy"></i> Kopyala
                                </button>
                                <a href="{{ url('/facebook-catalog.xml') }}" target="_blank"
                                   class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition shrink-0 flex items-center gap-1">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Aç
                                </a>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider mb-1">
                                🔗 2. Google / Genel XML Ürün Kataloğu Linki
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly id="generalXmlFeedUrl"
                                       value="{{ url('/urunler.xml') }}"
                                       class="w-full px-3.5 py-2.5 text-xs font-mono bg-white border border-gray-300 rounded-xl text-gray-700 font-bold select-all outline-none">
                                <button type="button" onclick="navigator.clipboard.writeText('{{ url('/urunler.xml') }}'); showToast('XML Ürünler linki kopyalandı!', 'success');"
                                        class="px-4 py-2.5 bg-gray-700 hover:bg-gray-800 text-white font-bold text-xs rounded-xl shadow-xs transition shrink-0 flex items-center gap-1.5">
                                    <i class="fa-solid fa-copy"></i> Kopyala
                                </button>
                                <a href="{{ url('/urunler.xml') }}" target="_blank"
                                   class="px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition shrink-0 flex items-center gap-1">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Aç
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Facebook Piksel & Conversions API Form Alanları --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-200 space-y-4">
                    <h3 class="text-sm font-bold text-gray-900 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-[#1877F2]"></i>
                        <span>Meta Piksel & Conversions API (CAPI) Kimlik Bilgileri</span>
                    </h3>
                    <p class="text-xs text-gray-500 mb-3">
                        Kullanıcıların site içi hareketleri (Sayfa Görüntüleme, Ürün İnceleme, Sepete Ekleme, Sipariş Başlatma ve Satın Alma) Meta Piksel ve Sunucu Taraflı CAPI ile eşzamanlı takip edilir.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Facebook / Meta Piksel ID (Pixel ID)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-bullseye text-xs"></i>
                                </div>
                                <input type="text" name="facebook_pixel_id"
                                       value="{{ old('facebook_pixel_id', $settings['facebook']['facebook_pixel_id'] ?? '') }}"
                                       placeholder="1151884751162206"
                                       class="w-full pl-9 pr-4 py-2.5 text-xs font-mono font-bold border border-gray-300 rounded-xl focus:border-[#C87A53] focus:ring-1 focus:ring-[#C87A53] outline-none transition bg-white">
                            </div>
                            <span class="text-[11px] text-gray-400 mt-1 block">Meta Event Manager'daki Piksel Kimlik Numarası.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Dönüşümler API'si (CAPI) Erişim Jetonu (Access Token)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-key text-xs"></i>
                                </div>
                                <input type="text" name="facebook_access_token"
                                       value="{{ old('facebook_access_token', $settings['facebook']['facebook_access_token'] ?? '') }}"
                                       placeholder="EAAaur..."
                                       class="w-full pl-9 pr-4 py-2.5 text-xs font-mono border border-gray-300 rounded-xl focus:border-[#C87A53] focus:ring-1 focus:ring-[#C87A53] outline-none transition bg-white">
                            </div>
                            <span class="text-[11px] text-gray-400 mt-1 block">Olay Yöneticisi > Ayarlar > Dönüşümler API'si > Erişim Jetonu Oluştur.</span>
                        </div>
                    </div>

                    {{-- Hızlı Test CAPI Butonu --}}
                    <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="text-xs text-gray-500">
                            Meta API erişim jetonunuzun geçerliliğini ve sunucu bağlantısını anlık olarak test edin.
                        </div>
                        <button type="button" onclick="document.getElementById('hiddenFacebookCapiTestForm').submit();"
                                class="px-4 py-2 bg-[#1877F2] hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
                            <i class="fa-solid fa-bolt"></i> Meta CAPI Bağlantısını Test Et
                        </button>
                    </div>
                </div>

                {{-- Meta Commerce Manager Rehberi --}}
                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 text-xs space-y-3">
                    <h4 class="font-extrabold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-[#C87A53]"></i>
                        <span>Meta Kataloğuna Nasıl Eklenir? (Adım Adım)</span>
                    </h4>
                    <ol class="list-decimal list-inside space-y-1.5 text-gray-600 leading-relaxed font-medium">
                        <li><strong>business.facebook.com/commerce</strong> adresine gidip işletmenizin Kataloğunu seçin.</li>
                        <li>Sol menüden <strong>Veri Kaynakları (Data Sources)</strong> &gt; <strong>Ürün Ekle (Add Products)</strong> seçin.</li>
                        <li><strong>Veri Akışı (Data Feed)</strong> seçeneğini işaretleyin.</li>
                        <li><strong>Planlanmış Akış (Scheduled Feed)</strong> seçip yukarıdaki <code class="bg-white px-1.5 py-0.5 rounded border border-gray-300 text-[#C87A53] font-bold">{{ url('/facebook-catalog.xml') }}</code> adresini yapıştırın.</li>
                        <li>Yenileme sıklığını <strong>Günlük (Daily)</strong> olarak kaydedin. Ürünleriniz ve stok durumunuz otomatik güncellenecektir.</li>
                    </ol>
                </div>

            </div>

            {{-- Kaydet Butonu (Sabit Alt Bar) --}}
            <div class="mt-8 pt-5 border-t border-gray-200 flex items-center justify-end gap-3">
                <button type="submit"
                        class="py-3 px-8 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl text-xs transition-all shadow-md shadow-[#C87A53]/20 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Tüm Ayarları Kaydet</span>
                </button>
            </div>

        </form>
    </div>

</div>

{{-- Gizli Test Formları --}}
<form id="hiddenSmsTestForm" action="{{ route('admin.settings.test_sms') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="test_phone" id="hiddenTestPhone">
</form>

<form id="hiddenEmailTestForm" action="{{ route('admin.settings.test_email') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="test_email" id="hiddenTestEmail">
</form>

<form id="hiddenFacebookCapiTestForm" action="{{ route('admin.settings.test_facebook_capi') }}" method="POST" class="hidden">
    @csrf
</form>

@push('scripts')
<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'border-b-2', 'border-[#C87A53]', 'text-[#C87A53]', 'bg-white', 'shadow-xs');
        btn.classList.add('text-gray-500');
    });

    const targetTab = document.getElementById(tabId);
    const targetBtn = document.getElementById('btn-' + tabId);

    if (targetTab) targetTab.classList.remove('hidden');
    if (targetBtn) {
        targetBtn.classList.add('active', 'border-b-2', 'border-[#C87A53]', 'text-[#C87A53]', 'bg-white', 'shadow-xs');
        targetBtn.classList.remove('text-gray-500');
    }
}

function sendQuickTestSms() {
    const phone = document.getElementById('quickTestPhone').value.trim();
    if (!phone) {
        alert('Lütfen test edilecek telefon numarasını giriniz.');
        return;
    }
    document.getElementById('hiddenTestPhone').value = phone;
    document.getElementById('hiddenSmsTestForm').submit();
}

function sendQuickTestEmail() {
    const email = document.getElementById('quickTestEmail').value.trim();
    if (!email) {
        alert('Lütfen test edilecek e-posta adresini giriniz.');
        return;
    }
    document.getElementById('hiddenTestEmail').value = email;
    document.getElementById('hiddenEmailTestForm').submit();
}
</script>
@endpush
@endsection
