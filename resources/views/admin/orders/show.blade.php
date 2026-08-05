@extends('layouts.admin')

@section('header', 'Sipariş Detayı #' . $order->id)

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm max-w-5xl">
    <!-- Header -->
    <div class="mb-6 pb-4 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Sipariş #{{ $order->id }} Detayı</h3>
            <p class="text-xs text-gray-500 mt-1">Sipariş tarihi: {{ $order->created_at->format('d.m.Y H:i') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Sipariş Listesine Dön
        </a>
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
        </div>

        <!-- Durum ve Kargo Takip Güncelleme Formu -->
        <div class="bg-orange-50/50 p-4 rounded-xl border border-orange-100">
            <h4 class="text-xs font-bold text-[#C87A53] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <i class="fa-solid fa-truck-fast"></i> Kargo & Durum Güncelle
            </h4>
            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1">Sipariş Durumu</label>
                    <select name="status" class="w-full text-xs font-bold border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none">
                        <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Ödendi / Hazırlanıyor</option>
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Ödeme Bekliyor</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Kargolandı</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                        <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Başarısız</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1">Kargo Şirketi</label>
                    <select name="shipping_company_id" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] outline-none">
                        <option value="">Kargo Şirketi Seçiniz...</option>
                        @foreach($shippingCompanies as $company)
                            <option value="{{ $company->id }}" {{ $order->shipping_company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold text-gray-500 uppercase mb-1">Kargo Takip Numarası</label>
                    <input type="text" name="cargo_tracking_code" placeholder="Örn: 123456789" value="{{ old('cargo_tracking_code', $order->cargo_tracking_code) }}" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] outline-none font-mono">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-lg text-xs transition shadow-sm flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-paper-plane"></i> Güncelle & SMS/Mail Gönder
                </button>
            </form>
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

                            <!-- Yüklenen Özel Ön Yüz & Arka Yüz Fotoğrafları -->
                            @php
                                $fImg = $item->features['front_image'] ?? ($item->features['custom_image'] ?? null);
                                $bImg = $item->features['back_image'] ?? null;
                            @endphp
                            @if($fImg || $bImg)
                                <div class="mt-3 bg-white p-3 rounded-xl border border-orange-200/80 space-y-2">
                                    <span class="block text-[11px] font-extrabold text-[#C87A53] uppercase tracking-wider">Müşteri Tarafından Yüklenen Fotoğraflar:</span>
                                    <div class="flex flex-wrap items-center gap-3">
                                        @if($fImg)
                                            <div class="flex items-center gap-2 bg-orange-50 p-1.5 rounded-lg border border-orange-200">
                                                <img src="{{ str_starts_with($fImg, 'http') ? $fImg : url($fImg) }}" class="w-10 h-10 object-cover rounded border border-orange-300">
                                                <div>
                                                    <span class="block text-[10px] font-bold text-orange-900">1. Ön Yüz Fotoğrafı</span>
                                                    <a href="{{ str_starts_with($fImg, 'http') ? $fImg : url($fImg) }}" target="_blank" class="text-[10px] text-blue-600 hover:underline font-bold">
                                                        <i class="fa-solid fa-download"></i> Orijinali İndir
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

                                        @if($bImg)
                                            <div class="flex items-center gap-2 bg-emerald-50 p-1.5 rounded-lg border border-emerald-200">
                                                <img src="{{ str_starts_with($bImg, 'http') ? $bImg : url($bImg) }}" class="w-10 h-10 object-cover rounded border border-emerald-300">
                                                <div>
                                                    <span class="block text-[10px] font-bold text-emerald-900">2. Arka Yüz Fotoğrafı</span>
                                                    <a href="{{ str_starts_with($bImg, 'http') ? $bImg : url($bImg) }}" target="_blank" class="text-[10px] text-blue-600 hover:underline font-bold">
                                                        <i class="fa-solid fa-download"></i> Orijinali İndir
                                                    </a>
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
@endsection
