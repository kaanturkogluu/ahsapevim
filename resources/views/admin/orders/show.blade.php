@extends('layouts.admin')

@section('header', 'Sipariş Detayı #' . $order->id)

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm max-w-5xl">
    <!-- Header -->
    <div class="mb-6 pb-4 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Sipariş #{{ $order->id }} Detayı</h3>
            <p class="text-xs text-gray-500 mt-1">Sipariş tarihi: {{ $order->created_at?->format('d.m.Y H:i') ?? '-' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="openDeleteOrderModal({{ $order->id }}, 'Sipariş #{{ $order->id }} ({{ $order->name }})')" class="py-1.5 px-3 bg-rose-50 text-rose-600 hover:bg-rose-100 font-extrabold text-xs rounded-lg transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-trash-can"></i> Siparişi Sil & Görselleri Temizle
            </button>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Sipariş Listesine Dön
            </a>
        </div>
    </div>



    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Müşteri Bilgileri -->
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <i class="fa-solid fa-user text-[#C87A53]"></i> Müşteri Bilgileri
            </h4>
            <div class="text-xs space-y-1.5 text-gray-700">
                <p><strong>Ad Soyad:</strong> {{ $order->name }}</p>
                <p><strong>E-Posta:</strong> {{ $order->email }}</p>
                <p><strong>Telefon:</strong> {{ $order->phone }}</p>
                @if($order->identity_number)
                    <p><strong>T.C. Kimlik No:</strong> {{ $order->identity_number }}</p>
                @endif
            </div>
            <div class="mt-4 pt-3 border-t border-gray-200/80 flex flex-wrap gap-2">
                <button type="button" onclick="openSendMailModal('{{ $order->email }}', '{{ $order->id }}')" class="py-1.5 px-2.5 bg-amber-100 text-amber-900 hover:bg-amber-200 font-extrabold text-[11px] rounded-lg transition inline-flex items-center gap-1 border border-amber-300">
                    <i class="fa-solid fa-paper-plane text-xs"></i> E-Posta Gönder
                </button>
                <button type="button" onclick="openSendSmsModal('{{ $order->phone }}', '{{ $order->id }}')" class="py-1.5 px-2.5 bg-emerald-100 text-emerald-900 hover:bg-emerald-200 font-extrabold text-[11px] rounded-lg transition inline-flex items-center gap-1 border border-emerald-300">
                    <i class="fa-solid fa-comment-sms text-xs"></i> SMS Gönder
                </button>
            </div>
        </div>

        <!-- Teslimat Adresi -->
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <i class="fa-solid fa-truck text-blue-600"></i> Teslimat Adresi
            </h4>
            <div class="text-xs text-gray-700 space-y-1.5">
                <p class="whitespace-pre-line">{{ $order->address }}</p>
                <p><strong>Şehir / İlçe:</strong> {{ $order->city ?: 'Manisa' }} / {{ $order->district ?: 'Merkez' }}</p>
            </div>

            @if(!empty($order->note))
                <div class="mt-3 pt-3 border-t border-gray-200/80">
                    <span class="block text-[10px] font-extrabold text-[#C87A53] uppercase mb-1">
                        <i class="fa-solid fa-note-sticky"></i> Müşteri Sipariş Notu:
                    </span>
                    <p class="text-xs text-gray-800 italic bg-white p-2 rounded-lg border border-amber-200/70 font-serif">
                        "{{ $order->note }}"
                    </p>
                </div>
            @endif
        </div>

        <!-- Durum ve Kargo Takip Güncelleme Formu -->
        <div class="bg-orange-50/50 p-4 rounded-xl border border-orange-100 flex flex-col justify-between">
            <div>
                <h4 class="text-xs font-bold text-[#C87A53] uppercase tracking-wider mb-3 flex items-center justify-between">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-truck-fast"></i> Kargo & Durum Bilgisi</span>
                    @if($order->status === 'shipped')
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] rounded font-bold">Kargolandı</span>
                    @elseif($order->status === 'completed')
                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] rounded font-bold">Tamamlandı</span>
                    @endif
                </h4>

                @if(!empty($order->cargo_tracking_code))
                    <div class="mb-3 p-2.5 bg-blue-50/80 border border-blue-200 rounded-lg text-xs text-blue-900 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-blue-600 uppercase block">Kayıtlı Kargo Takip</span>
                            <span class="font-mono font-bold">{{ $order->shippingCompany?->name ?: 'Kargo' }} - {{ $order->cargo_tracking_code }}</span>
                        </div>
                        <i class="fa-solid fa-truck-ramp-box text-blue-500 text-base"></i>
                    </div>
                @endif

                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="space-y-3" onsubmit="preventSpamSubmit(this)">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1">Sipariş Durumu</label>
                        <select name="status" id="orderStatusSelect" onchange="toggleCancellationReason(this.value)" class="w-full text-xs font-bold border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none bg-white">
                            <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Ödendi / Hazırlanıyor</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Kargolandı (Kargoya Verildi)</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Tamamlandı (Teslim Edildi)</option>
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Ödeme Bekliyor</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                            <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Başarısız</option>
                        </select>
                    </div>

                    <div id="cancellationReasonDiv" class="{{ in_array($order->status, ['cancelled', 'failed']) ? '' : 'hidden' }}">
                        <label class="block text-[10px] font-extrabold text-rose-800 uppercase mb-1">İptal / Başarısızlık Açıklaması (Opsiyonel)</label>
                        <input type="text" name="payment_error_reason" placeholder="Örn: Müşteri talebi ile iptal edildi" value="{{ old('payment_error_reason', $order->payment_error_reason) }}" class="w-full text-xs border-rose-300 rounded-lg p-2 border focus:border-rose-500 outline-none bg-rose-50/50">
                        <span class="text-[10px] text-gray-400 mt-0.5 block">Bu açıklama müşteriye giden iptal e-postasında yer alır.</span>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1">Kargo Firması</label>
                        <select name="shipping_company_id" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] outline-none bg-white">
                            <option value="">Kargo Firması Seçiniz...</option>
                            @foreach($shippingCompanies as $company)
                                <option value="{{ $company->id }}" {{ $order->shipping_company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1">Kargo Takip Numarası</label>
                        <input type="text" name="cargo_tracking_code" placeholder="Örn: 123456789012" value="{{ old('cargo_tracking_code', $order->cargo_tracking_code) }}" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] outline-none font-mono bg-white">
                        <span class="text-[10px] text-gray-400 mt-0.5 block">Takip numarası girildiğinde durum otomatik olarak 'Kargolandı' yapılır.</span>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="send_notification" id="send_notification" value="1" checked class="w-3.5 h-3.5 text-[#C87A53] rounded border-gray-300 focus:ring-0">
                        <label for="send_notification" class="text-[11px] font-bold text-gray-700 cursor-pointer">Müşteriye SMS & E-Posta Bildirimi Gönder</label>
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="w-full py-2.5 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-lg text-xs transition shadow-sm flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-paper-plane"></i> Bilgileri Güncelle & Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Iyzico Finansal Hakediş & İptal / Başarısızlık Detayları -->
    @if($order->status === 'failed' || $order->status === 'cancelled' || !empty($order->payment_error_reason))
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl mb-6 shadow-2xs">
            <div class="flex items-center gap-2 text-rose-900 font-extrabold text-xs uppercase tracking-wider mb-1.5">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-sm"></i> Ödeme Durumu: İptal / Başarısız
            </div>
            <div class="text-xs text-rose-800 space-y-1">
                <p><strong>İptal / Başarısız Sebebi:</strong> <span class="font-extrabold text-rose-950">{{ $order->payment_error_reason ?: 'Yetersiz Bakiye / Kart Onayı Alınamadı' }}</span></p>
                @if($order->payment_id)
                    <p class="font-mono text-[11px] text-rose-700"><strong>İşlem Referans No:</strong> {{ $order->payment_id }}</p>
                @endif
            </div>
        </div>
    @else
        <div class="bg-gradient-to-br from-emerald-50/70 via-white to-gray-50 p-4 rounded-xl border border-emerald-200/80 mb-6 shadow-2xs">
            <h4 class="text-xs font-bold text-emerald-900 uppercase tracking-wider mb-3 flex items-center justify-between">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-credit-card text-emerald-600"></i> Iyzico Ödeme & Finansal Hakediş Detayları</span>
                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] rounded font-mono font-bold">Iyzico 256-Bit SSL</span>
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                <div class="bg-white p-3 rounded-lg border border-gray-200/70 shadow-2xs">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase block">Bankanın Çektiği Tutar</span>
                    <span class="text-sm font-black text-gray-800 mt-0.5 block">₺{{ number_format($order->paid_price ?? $order->total_amount, 2, ',', '.') }}</span>
                </div>

                <div class="bg-white p-3 rounded-lg border border-gray-200/70 shadow-2xs">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase block">Taksit Sayısı</span>
                    <span class="text-sm font-black text-blue-700 mt-0.5 block">
                        @if(($order->installment ?? 1) > 1)
                            {{ $order->installment }} Taksit
                        @else
                            Tek Çekim (1 Taksit)
                        @endif
                    </span>
                </div>

                <div class="bg-white p-3 rounded-lg border border-emerald-300 shadow-2xs bg-emerald-50/40">
                    <span class="text-[10px] font-extrabold text-emerald-800 uppercase block">Esnafın Hak Ediş Miktarı (Net)</span>
                    <span class="text-sm font-black text-emerald-700 mt-0.5 block">₺{{ number_format($order->merchant_payout_amount ?? $order->total_amount, 2, ',', '.') }}</span>
                </div>

                <div class="bg-white p-3 rounded-lg border border-gray-200/70 shadow-2xs">
                    <span class="text-[10px] font-extrabold text-gray-400 uppercase block">Kart / Referans No</span>
                    <span class="text-xs font-bold text-gray-700 mt-0.5 block">
                        {{ $order->card_family ?: 'Kredi Kartı' }} {{ $order->card_last_four ? '**** ' . $order->card_last_four : '' }}
                    </span>
                    <span class="text-[10px] font-mono text-gray-400 block truncate" title="{{ $order->payment_id }}">{{ $order->payment_id ?: 'N/A' }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Sipariş Kalemleri ve Yüklenen Fotoğraflar -->
    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200/80 mb-6">
        <h4 class="text-sm font-extrabold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-boxes-packing text-[#C87A53]"></i> Sipariş Edilen Ürünler ve Fotoğraflar
        </h4>

        <div class="divide-y divide-gray-200/80">
            @foreach($order->items as $item)
                <div class="py-4 first:pt-0 last:pb-0 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        @if($item->product && $item->product->image)
                            <img src="{{ url($item->product->image) }}" class="w-16 h-16 object-cover rounded-xl border border-gray-200 shrink-0">
                        @else
                            <div class="w-16 h-16 bg-amber-100 rounded-xl border border-amber-200 flex items-center justify-center text-amber-700 text-2xl shrink-0">
                                <i class="fa-solid fa-cube"></i>
                            </div>
                        @endif

                        <div>
                            <h5 class="text-sm font-bold text-gray-800">{{ $item->product ? $item->product->name : 'Ahşap Ürün' }}</h5>
                            <div class="text-xs text-gray-500 mt-1">
                                Adet: <strong class="text-gray-800">{{ $item->quantity }}</strong> × ₺{{ number_format($item->price, 2, ',', '.') }}
                            </div>

                            @if(!empty($item->features['is_gift']) || !empty($item->features['gift_note']))
                                <div class="mt-2 bg-amber-50/80 p-2.5 rounded-xl border border-amber-200 text-xs text-amber-950">
                                    <span class="font-extrabold text-[#C87A53] flex items-center gap-1.5 text-[11px] uppercase tracking-wider">
                                        <i class="fa-solid fa-gift"></i> Müşterinin Hediye Notu İsteği:
                                    </span>
                                    <p class="mt-1 text-gray-800 italic bg-white p-2 rounded-lg border border-amber-200/60 font-serif text-xs">
                                        "{{ $item->features['gift_note'] ?: 'Özel not belirtilmedi (Hediye Paketi Yapılacak)' }}"
                                    </p>
                                </div>
                            @endif

                            <!-- Yüklenen Özel Ön Yüz & Arka Yüz Fotoğrafları -->
                            @php
                                $fImg = $item->features['front_image'] ?? ($item->features['custom_image'] ?? null);
                                $bImg = $item->features['back_image'] ?? null;
                                $isDoubleFace = ($fImg && $bImg);
                            @endphp
                            @if($fImg || $bImg)
                                <div class="mt-3 bg-white p-3 rounded-xl border border-orange-200/80 space-y-2">
                                    <span class="block text-[11px] font-extrabold text-[#C87A53] uppercase tracking-wider">
                                        Müşteri Tarafından Yüklenen Fotoğraflar 
                                        <span class="px-2 py-0.5 rounded text-[10px] ml-1 {{ $isDoubleFace ? 'bg-emerald-100 text-emerald-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ $isDoubleFace ? 'Çift Yüzlü (2 Fotoğraf)' : 'Tek Yüzlü (1 Fotoğraf)' }}
                                        </span>
                                    </span>
                                    <div class="flex flex-wrap items-center gap-3">
                                        @if($fImg)
                                            <div class="flex items-center gap-2.5 bg-orange-50 p-2 rounded-xl border border-orange-200 shadow-2xs">
                                                <a href="{{ str_starts_with($fImg, 'http') ? $fImg : url($fImg) }}" target="_blank" title="Tam Ekran Önizle">
                                                    <img src="{{ str_starts_with($fImg, 'http') ? $fImg : url($fImg) }}" class="w-12 h-12 object-cover rounded-lg border border-orange-300 hover:opacity-90 transition">
                                                </a>
                                                <div>
                                                    <span class="block text-[11px] font-extrabold text-orange-950">1. Ön Yüz Fotoğrafı</span>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <a href="{{ route('admin.orders.download_image', ['path' => $fImg, 'filename' => 'Siparis_' . $order->id . '_1_On_Yuz']) }}" download class="px-2 py-0.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-extrabold rounded transition shadow-2xs inline-flex items-center gap-1">
                                                            <i class="fa-solid fa-download"></i> Orijinali İndir
                                                        </a>
                                                        <a href="{{ str_starts_with($fImg, 'http') ? $fImg : url($fImg) }}" target="_blank" class="text-[10px] text-gray-500 hover:text-gray-800 font-bold transition">
                                                            <i class="fa-solid fa-up-right-from-square"></i> Aç
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($bImg)
                                            <div class="flex items-center gap-2.5 bg-emerald-50 p-2 rounded-xl border border-emerald-200 shadow-2xs">
                                                <a href="{{ str_starts_with($bImg, 'http') ? $bImg : url($bImg) }}" target="_blank" title="Tam Ekran Önizle">
                                                    <img src="{{ str_starts_with($bImg, 'http') ? $bImg : url($bImg) }}" class="w-12 h-12 object-cover rounded-lg border border-emerald-300 hover:opacity-90 transition">
                                                </a>
                                                <div>
                                                    <span class="block text-[11px] font-extrabold text-emerald-950">2. Arka Yüz Fotoğrafı</span>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <a href="{{ route('admin.orders.download_image', ['path' => $bImg, 'filename' => 'Siparis_' . $order->id . '_2_Arka_Yuz']) }}" download class="px-2 py-0.5 bg-emerald-700 hover:bg-emerald-800 text-white text-[10px] font-extrabold rounded transition shadow-2xs inline-flex items-center gap-1">
                                                            <i class="fa-solid fa-download"></i> Orijinali İndir
                                                        </a>
                                                        <a href="{{ str_starts_with($bImg, 'http') ? $bImg : url($bImg) }}" target="_blank" class="text-[10px] text-gray-500 hover:text-gray-800 font-bold transition">
                                                            <i class="fa-solid fa-up-right-from-square"></i> Aç
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-right font-black text-sm text-gray-800">
                        ₺{{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Toplam Tutar -->
    <div class="bg-gray-100 p-4 rounded-xl flex items-center justify-between font-bold">
        <span class="text-sm text-gray-700">Genel Toplam Tutar:</span>
        <span class="text-xl font-black text-[#C87A53]">₺{{ number_format($order->total_amount, 2, ',', '.') }}</span>
    </div>
</div>

<!-- Admin Şifreli Sipariş Silme Modalı -->
<div id="deleteOrderModal" class="fixed inset-0 z-[99999] bg-black/70 backdrop-blur-xs hidden flex-col items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 relative">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-extrabold text-rose-700 flex items-center gap-2">
                <i class="fa-solid fa-trash-can text-lg"></i> Sipariş Kaydını Sil
            </h3>
            <button type="button" onclick="closeDeleteOrderModal()" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
        </div>

        <form id="deleteOrderForm" method="POST" action="">
            @csrf
            @method('DELETE')

            <p class="text-xs text-gray-600 mb-3 leading-relaxed">
                <strong id="deleteOrderTitle" class="text-gray-800 font-extrabold block mb-1">#0 Nolu Sipariş</strong>
                Bu sipariş kaydını silmek istediğinize emin misiniz? Siparişle ilişkili <strong>yüklenen tüm müşteri fotoğrafları diskten kalıcı olarak silinecektir.</strong>
            </p>

            <div class="bg-rose-50 p-3 rounded-xl border border-rose-200 mb-4">
                <label class="block text-[11px] font-extrabold text-rose-900 uppercase mb-1">Güvenlik Onayı İçin Admin Şifreniz *</label>
                <input type="password" name="password" required placeholder="Admin şifrenizi giriniz..." class="w-full text-xs border-gray-300 rounded-lg p-2.5 border focus:border-rose-500 focus:ring-0 outline-none">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeDeleteOrderModal()" class="py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition">
                    Vazgeç
                </button>
                <button type="submit" class="py-2 px-4 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-lg transition shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-trash-can text-xs"></i> Görselleri ve Kaydı Sil
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDeleteOrderModal(orderId, title) {
    const modal = document.getElementById('deleteOrderModal');
    const form = document.getElementById('deleteOrderForm');
    const titleEl = document.getElementById('deleteOrderTitle');
    
    if (form) {
        form.action = `/yonetim/siparisler/${orderId}`;
    }
    if (titleEl) {
        titleEl.textContent = title || `#${orderId} Nolu Sipariş`;
    }
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        const pwd = form.querySelector('input[name="password"]');
        if (pwd) {
            pwd.value = '';
            setTimeout(() => pwd.focus(), 100);
        }
    }
}

function closeDeleteOrderModal() {
    const modal = document.getElementById('deleteOrderModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
</script>

<!-- Manuel E-Posta Gönderme Modalı -->
<div id="sendMailModal" class="fixed inset-0 z-[99999] bg-black/70 backdrop-blur-xs hidden flex-col items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 relative">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-extrabold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-[#C87A53]"></i> Müşteriye Manuel E-Posta Gönder
            </h3>
            <button type="button" onclick="closeSendMailModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>

        <form action="{{ route('admin.mail.send_manual') }}" method="POST" class="space-y-4" onsubmit="preventSpamSubmit(this)">
            @csrf
            <div>
                <label class="block text-[11px] font-extrabold text-gray-500 uppercase mb-1">Alıcı E-Posta Adresi *</label>
                <input type="email" name="to_email" id="modalToEmail" required placeholder="ornek@musteri.com" class="w-full text-xs border border-gray-300 rounded-xl p-2.5 outline-none focus:border-[#C87A53]">
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-gray-500 uppercase mb-1">E-Posta Konusu *</label>
                <input type="text" name="subject" required placeholder="Siparişiniz hakkında..." class="w-full text-xs border border-gray-300 rounded-xl p-2.5 outline-none focus:border-[#C87A53]">
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-gray-500 uppercase mb-1">E-Posta İçeriği (Mesaj Metni) *</label>
                <textarea name="body" rows="5" required placeholder="Müşterinize iletmek istediğiniz mesaj metnini buraya yazınız..." class="w-full text-xs border border-gray-300 rounded-xl p-2.5 outline-none focus:border-[#C87A53]"></textarea>
            </div>

            <input type="hidden" name="order_id" id="modalOrderId">

            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeSendMailModal()" class="py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                    Vazgeç
                </button>
                <button type="submit" class="py-2 px-5 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold text-xs rounded-xl transition shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-paper-plane"></i> Gönder ve Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Manuel SMS Gönderme Modalı -->
<div id="sendSmsModal" class="fixed inset-0 z-[99999] bg-black/70 backdrop-blur-xs hidden flex-col items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 relative">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-extrabold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-comment-sms text-[#C87A53]"></i> Netgsm İle Müşteriye SMS Gönder
            </h3>
            <button type="button" onclick="closeSendSmsModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>

        <form action="{{ route('admin.sms.send_manual') }}" method="POST" class="space-y-4" id="orderSmsForm">
            @csrf
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
                        oninput="smsFormatPhone(this)"
                        onblur="smsValidatePhone(this)"
                    >
                    <span id="smsPhoneIcon" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs hidden"></span>
                </div>
                <p id="smsPhoneError" class="text-[10px] text-rose-600 font-bold mt-1 hidden">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i>
                    Geçerli bir Türk GSM numarası girin. Örn: 05321234567
                </p>
                <p class="text-[10px] text-gray-400 mt-1">Desteklenen formatlar: 0532..., +90532..., 90532...</p>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-gray-500 uppercase mb-1">SMS Mesaj Metni *</label>
                <textarea name="message" id="modalSmsBody" rows="4" maxlength="918" required placeholder="Müşterinize gitmesini istediğiniz SMS metni..." class="w-full text-xs border border-gray-300 rounded-xl p-2.5 outline-none focus:border-[#C87A53]" oninput="smsUpdateCharCount(this)"></textarea>
                <div class="text-[10px] text-gray-400 text-right mt-1 font-mono flex items-center justify-end gap-2">
                    <span id="smsPartsBadge" class="px-1.5 py-0.5 bg-amber-50 text-amber-700 rounded font-bold hidden"></span>
                    <span><span id="smsCharCounter">0</span> / 918 Karakter</span>
                </div>
            </div>

            <input type="hidden" name="order_id" id="modalSmsOrderId">

            <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeSendSmsModal()" class="py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                    Vazgeç
                </button>
                <button type="submit" id="orderSmsSubmitBtn" class="py-2 px-5 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold text-xs rounded-xl transition shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-paper-plane"></i> Netgsm İle Gönder
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Telefon Formatlama & Validasyon ─────────────────────────────────────────
function smsFormatPhone(el) {
    let digits = el.value.replace(/\D/g, '');
    if (digits.startsWith('90') && digits.length > 10) digits = digits.slice(2);
    if (digits.startsWith('5')) digits = '0' + digits;
    digits = digits.slice(0, 11);

    let fmt = '';
    if (digits.length > 0) fmt  = digits.slice(0, 4);
    if (digits.length > 4) fmt += ' ' + digits.slice(4, 7);
    if (digits.length > 7) fmt += ' ' + digits.slice(7, 9);
    if (digits.length > 9) fmt += ' ' + digits.slice(9, 11);
    el.value = fmt;

    if (digits.length === 11) smsValidatePhone(el);
    else smsClearPhoneValid();
}

function smsValidatePhone(el) {
    const digits    = el.value.replace(/\D/g, '');
    const icon      = document.getElementById('smsPhoneIcon');
    const errMsg    = document.getElementById('smsPhoneError');
    const submitBtn = document.getElementById('orderSmsSubmitBtn');
    const isValid   = /^05[0-9]{9}$/.test(digits);

    if (isValid) {
        icon.textContent = '✓';
        icon.className   = 'absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-emerald-600 font-bold';
        icon.classList.remove('hidden');
        errMsg.classList.add('hidden');
        el.classList.remove('border-rose-400'); el.classList.add('border-emerald-400');
        if (submitBtn) submitBtn.disabled = false;
    } else if (digits.length > 0) {
        icon.textContent = '✗';
        icon.className   = 'absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-rose-500 font-bold';
        icon.classList.remove('hidden');
        errMsg.classList.remove('hidden');
        el.classList.remove('border-emerald-400'); el.classList.add('border-rose-400');
        if (submitBtn) submitBtn.disabled = true;
    } else {
        smsClearPhoneValid();
    }
}

function smsClearPhoneValid() {
    const icon      = document.getElementById('smsPhoneIcon');
    const errMsg    = document.getElementById('smsPhoneError');
    const el        = document.getElementById('modalToPhone');
    const submitBtn = document.getElementById('orderSmsSubmitBtn');
    if (icon)      icon.classList.add('hidden');
    if (errMsg)    errMsg.classList.add('hidden');
    if (el)        el.classList.remove('border-rose-400', 'border-emerald-400');
    if (submitBtn) submitBtn.disabled = false;
}

// ── SMS Karakter Sayacı ──────────────────────────────────────────────────────
function smsUpdateCharCount(el) {
    const len     = el.value.length;
    const counter = document.getElementById('smsCharCounter');
    const parts   = document.getElementById('smsPartsBadge');
    if (counter) counter.textContent = len;
    const smsLimit = /[çğışöüÇĞİŞÖÜ]/.test(el.value) ? 153 : 160;
    const count    = Math.ceil(len / smsLimit) || 1;
    if (parts) {
        if (len > smsLimit) { parts.textContent = count + ' SMS'; parts.classList.remove('hidden'); }
        else { parts.classList.add('hidden'); }
    }
}

// ── Modal Kontrolleri ────────────────────────────────────────────────────────
function openSendSmsModal(phone = '', orderId = '') {
    const modal   = document.getElementById('sendSmsModal');
    const phoneEl = document.getElementById('modalToPhone');
    if (!modal) return;

    if (phone) {
        phoneEl.value = phone;
        smsFormatPhone(phoneEl);
    } else {
        phoneEl.value = '';
        smsClearPhoneValid();
    }
    document.getElementById('modalSmsOrderId').value = orderId;
    document.getElementById('modalSmsBody').value = '';
    smsUpdateCharCount(document.getElementById('modalSmsBody'));

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => phoneEl.focus(), 100);
}

function closeSendSmsModal() {
    const modal = document.getElementById('sendSmsModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        smsClearPhoneValid();
    }
}

// Modal dışına tıklayınca kapat
document.addEventListener('click', function(e) {
    const modal = document.getElementById('sendSmsModal');
    if (modal && e.target === modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
});

// ── Mail Modal Kontrolleri ───────────────────────────────────────────────────
function openSendMailModal(email = '', orderId = '') {
    const modal = document.getElementById('sendMailModal');
    if (modal) {
        document.getElementById('modalToEmail').value = email;
        document.getElementById('modalOrderId').value = orderId;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeSendMailModal() {
    const modal = document.getElementById('sendMailModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function toggleCancellationReason(status) {
    const div = document.getElementById('cancellationReasonDiv');
    if (!div) return;
    if (status === 'cancelled' || status === 'failed') {
        div.classList.remove('hidden');
    } else {
        div.classList.add('hidden');
    }
}
</script>
@endsection
