@extends('layouts.app')

@section('title', 'Hesabım & Profilim - AhşapEvim')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">
    
    <!-- Profil Üst Bilgilendirme Kartı (Ahşap & Krem Doku) -->
    <div class="bg-gradient-to-r from-[#FAF3EE] via-[#F7F5F0] to-[#FAF3EE] border border-amber-200/80 rounded-2xl p-6 mb-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-[#C87A53] text-white flex items-center justify-center text-2xl font-black shadow-md border-2 border-white shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-gray-800 flex items-center gap-2">
                    Hoş Geldiniz, {{ $user->name }}
                    <span class="text-xs bg-amber-100 text-amber-800 font-bold px-2.5 py-0.5 rounded-full border border-amber-200">Müşteri Hesabı</span>
                </h1>
                <p class="text-xs text-gray-500 mt-1 flex items-center gap-3">
                    <span><i class="fa-solid fa-envelope text-amber-700/60 mr-1"></i>{{ $user->email }}</span>
                    <span><i class="fa-solid fa-calendar-day text-amber-700/60 mr-1"></i>Üyelik: {{ $user->created_at->format('d.m.Y') }}</span>
                </p>
            </div>
        </div>

        <!-- İstatistik Özet Rozetleri -->
        <div class="flex items-center gap-3 w-full md:w-auto justify-around md:justify-end">
            <div class="bg-white/90 border border-amber-100 px-4 py-2.5 rounded-xl text-center shadow-2xl shadow-gray-100">
                <span class="block text-lg font-black text-[#C87A53]">{{ $orders->count() }}</span>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Sipariş</span>
            </div>
            <div class="bg-white/90 border border-amber-100 px-4 py-2.5 rounded-xl text-center shadow-2xl shadow-gray-100">
                <span class="block text-lg font-black text-red-500">{{ $favorites->count() }}</span>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Favori</span>
            </div>
            <div class="bg-white/90 border border-amber-100 px-4 py-2.5 rounded-xl text-center shadow-2xl shadow-gray-100">
                @if(!empty($user->address))
                    <span class="block text-xs font-bold text-green-600 mt-1"><i class="fa-solid fa-check-circle"></i> Kayıtlı</span>
                @else
                    <span class="block text-xs font-bold text-amber-600 mt-1"><i class="fa-solid fa-exclamation-circle"></i> Tanımlanmadı</span>
                @endif
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Adres</span>
            </div>
        </div>
    </div>

    <!-- Başarı ve Hata Bildirimleri -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl text-xs text-green-700 font-bold mb-6 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl text-xs text-red-700 font-semibold mb-6 shadow-sm">
            <div class="font-bold mb-1">Lütfen aşağıdaki hataları düzeltin:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profil Paneli Ana Gövde (Sol Sekme Menüsü + Sağ İçerik) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Sol Sekme Navigasyonu (4 Sütun) -->
        <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-200/80 p-3 shadow-sm space-y-1">
            <button onclick="switchTab('siparisler')" id="tab-btn-siparisler" class="tab-button w-full flex items-center justify-between p-3 rounded-xl text-xs font-extrabold text-gray-600 hover:bg-amber-50 hover:text-[#C87A53] transition">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-box-open text-base text-[#C87A53]"></i>
                    <span>Siparişlerim</span>
                </div>
                <span class="px-2 py-0.5 bg-orange-100 text-[#C87A53] rounded-full text-[10px] font-bold">{{ $orders->count() }}</span>
            </button>

            <button onclick="switchTab('favoriler')" id="tab-btn-favoriler" class="tab-button w-full flex items-center justify-between p-3 rounded-xl text-xs font-extrabold text-gray-600 hover:bg-amber-50 hover:text-[#C87A53] transition">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-heart text-base text-red-500"></i>
                    <span>Favorilerim</span>
                </div>
                <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-[10px] font-bold">{{ $favorites->count() }}</span>
            </button>

            <button onclick="switchTab('bilgiler')" id="tab-btn-bilgiler" class="tab-button w-full flex items-center justify-between p-3 rounded-xl text-xs font-extrabold text-gray-600 hover:bg-amber-50 hover:text-[#C87A53] transition">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-user-pen text-base text-blue-500"></i>
                    <span>Kullanıcı Bilgilerim</span>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
            </button>

            <button onclick="switchTab('adres')" id="tab-btn-adres" class="tab-button w-full flex items-center justify-between p-3 rounded-xl text-xs font-extrabold text-gray-600 hover:bg-amber-50 hover:text-[#C87A53] transition">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-location-dot text-base text-emerald-600"></i>
                    <span>Adres Bilgilerim</span>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
            </button>

            <button onclick="switchTab('sifre')" id="tab-btn-sifre" class="tab-button w-full flex items-center justify-between p-3 rounded-xl text-xs font-extrabold text-gray-600 hover:bg-amber-50 hover:text-[#C87A53] transition">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-shield-halved text-base text-purple-600"></i>
                    <span>Şifre & Güvenlik</span>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
            </button>

            <div class="pt-2 border-t border-gray-100 mt-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl text-xs font-extrabold text-red-600 hover:bg-red-50 transition">
                        <i class="fa-solid fa-right-from-bracket text-base"></i>
                        <span>Çıkış Yap</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Sağ İçerik Alanı (9 Sütun) -->
        <div class="lg:col-span-9">
            
            <!-- SEKME 1: SİPARİŞLERİM -->
            <div id="tab-content-siparisler" class="tab-content hidden space-y-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-extrabold text-gray-800 mb-1 flex items-center gap-2">
                            <i class="fa-solid fa-box-open text-[#C87A53]"></i> Geçmiş Siparişleriniz
                        </h2>
                        <p class="text-xs text-gray-500">Verdiğiniz tüm siparişlerin durumunu ve satın alınan ürünleri buradan inceleyebilirsiniz.</p>
                    </div>
                    <a href="{{ url('/urunler') }}" class="py-2.5 px-5 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl text-xs transition inline-flex items-center gap-2 shrink-0 shadow-sm self-start sm:self-auto">
                        <i class="fa-solid fa-bag-shopping"></i> Alışverişe Başla
                    </a>
                </div>

                @forelse($orders as $order)
                    <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-sm hover:shadow-md transition">
                        <!-- Sipariş Başlığı -->
                        <div class="bg-gray-50 p-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-4">
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Sipariş No</span>
                                    <span class="text-sm font-black text-gray-800">#{{ $order->id }}</span>
                                </div>
                                <div class="hidden sm:block border-l border-gray-200 h-8"></div>
                                <div class="hidden sm:block">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Sipariş Tarihi</span>
                                    <span class="text-xs font-semibold text-gray-700">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Toplam Tutar</span>
                                    <span class="text-sm font-black text-[#C87A53]">₺{{ number_format($order->total_amount, 2, ',', '.') }}</span>
                                </div>

                                <div>
                                    @if($order->status === 'paid' || $order->status === 'preparing')
                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-extrabold text-xs inline-flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Ödendi / Hazırlanıyor
                                        </span>
                                    @elseif($order->status === 'shipped')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-extrabold text-xs inline-flex items-center gap-1">
                                            <i class="fa-solid fa-truck text-[10px]"></i> Kargoya Verildi
                                        </span>
                                    @elseif($order->status === 'completed')
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full font-extrabold text-xs inline-flex items-center gap-1">
                                            <i class="fa-solid fa-box-open text-[10px]"></i> Teslim Edildi
                                        </span>
                                    @elseif($order->status === 'pending')
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full font-extrabold text-xs inline-flex items-center gap-1">
                                            <i class="fa-solid fa-clock text-[10px]"></i> Ödeme Bekliyor
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full font-extrabold text-xs inline-flex items-center gap-1">
                                            <i class="fa-solid fa-circle-xmark text-[10px]"></i> İptal / {{ $order->payment_error_reason ?: 'Yetersiz Bakiye' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Sipariş Kalemleri -->
                        <div class="p-4 divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-14 h-14 object-cover rounded-xl border border-gray-100 shrink-0">
                                        @else
                                            <div class="w-14 h-14 bg-amber-50 rounded-xl border border-amber-100 flex items-center justify-center text-amber-700 text-xl font-bold shrink-0">
                                                <i class="fa-solid fa-cube"></i>
                                            </div>
                                        @endif

                                        <div>
                                            <h4 class="text-xs font-bold text-gray-800">
                                                {{ $item->product ? $item->product->name : 'Silinmiş Ürün' }}
                                            </h4>
                                            <div class="text-[11px] text-gray-500 mt-0.5">
                                                Adet: <span class="font-bold text-gray-700">{{ $item->quantity }}</span> × ₺{{ number_format($item->price, 2, ',', '.') }}
                                            </div>
                                            @if(!empty($item->features['is_gift']) || !empty($item->features['gift_note']))
                                                <div class="mt-1 font-bold text-amber-900 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded text-[10px] inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-gift text-brand"></i> Hediye Notu: {{ $item->features['gift_note'] ?: 'Hediye Paketi Yapılacak' }}
                                                </div>
                                            @endif
                                            @php
                                                $fImg = $item->features['front_image'] ?? ($item->features['custom_image'] ?? null);
                                                $bImg = $item->features['back_image'] ?? null;
                                            @endphp
                                            @if($fImg || $bImg)
                                                <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                                    @if($fImg)
                                                        <a href="{{ str_starts_with($fImg, 'http') ? $fImg : url($fImg) }}" target="_blank" class="inline-flex items-center gap-1 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded text-[10px] text-amber-900 font-bold hover:bg-amber-100 transition">
                                                            <i class="fa-solid fa-[#C87A53] fa-camera"></i> Ön Yüz Fotoğrafı
                                                        </a>
                                                    @endif
                                                    @if($bImg)
                                                        <a href="{{ str_starts_with($bImg, 'http') ? $bImg : url($bImg) }}" target="_blank" class="inline-flex items-center gap-1 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded text-[10px] text-emerald-900 font-bold hover:bg-emerald-100 transition">
                                                            <i class="fa-solid fa-camera text-emerald-600"></i> Arka Yüz Fotoğrafı
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-right font-black text-xs text-gray-800">
                                        ₺{{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Teslimat Adresi Özeti -->
                        <div class="bg-gray-50/70 p-3 px-4 border-t border-gray-100 text-[11px] text-gray-600 flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <i class="fa-solid fa-truck text-amber-700 mr-1"></i>
                                <span class="font-bold text-gray-700">Teslimat Adresi:</span> {{ Str::limit($order->address, 60) }}
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-gray-400 font-mono text-[10px]">Alıcı: {{ $order->name }} ({{ $order->phone }})</span>
                                @if($order->status === 'pending')
                                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bu siparişi iptal etmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 font-bold px-2.5 py-1 rounded-lg border border-red-200 text-[10px] transition">
                                            <i class="fa-solid fa-xmark"></i> Siparişi İptal Et
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-12 rounded-2xl border border-gray-200/80 text-center shadow-sm">
                        <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center text-[#C87A53] text-2xl mx-auto mb-4">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <h3 class="text-sm font-extrabold text-gray-800 mb-1">Henüz Siparişiniz Bulunmuyor</h3>
                        <p class="text-xs text-gray-500 mb-6 max-w-sm mx-auto">AhşapEvim'in masif ahşap el işçiliği ürünlerini keşfetmek için hemen alışverişe başlayabilirsiniz.</p>
                        <a href="{{ url('/urunler') }}" class="inline-flex items-center gap-2 py-2.5 px-6 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold text-xs rounded-xl transition shadow-sm">
                            <i class="fa-solid fa-store"></i> Ürünleri Keşfet
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- SEKME 2: FAVORİLERİM -->
            <div id="tab-content-favoriler" class="tab-content hidden space-y-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-gray-800 mb-1 flex items-center gap-2">
                            <i class="fa-solid fa-heart text-red-500"></i> Favori Ürünleriniz
                        </h2>
                        <p class="text-xs text-gray-500">Beğenip listenize eklediğiniz özel ahşap tasarımlar.</p>
                    </div>
                    <span class="text-xs font-bold text-gray-500">{{ $favorites->count() }} Ürün</span>
                </div>

                @if($favorites->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($favorites as $favProduct)
                            <div class="bg-white rounded-2xl border border-gray-200/80 p-3 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                                <div>
                                    <div class="relative rounded-xl overflow-hidden mb-3 aspect-square bg-gray-100">
                                        <img src="{{ url($favProduct->image) }}" alt="{{ $favProduct->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <h4 class="text-xs font-extrabold text-gray-800 line-clamp-1 mb-1">
                                        {{ $favProduct->name }}
                                    </h4>
                                    <div class="text-sm font-black text-[#C87A53] mb-3">
                                        ₺{{ number_format($favProduct->price, 2, ',', '.') }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                                    <a href="{{ url('/urun/' . ($favProduct->slug ?: $favProduct->id)) }}" class="flex-1 text-center py-2 px-3 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold text-xs rounded-xl transition">
                                        İncele
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white p-12 rounded-2xl border border-gray-200/80 text-center shadow-sm">
                        <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center text-red-500 text-2xl mx-auto mb-4">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                        <h3 class="text-sm font-extrabold text-gray-800 mb-1">Favori Ürününüz Yok</h3>
                        <p class="text-xs text-gray-500 mb-6">Beğendiğiniz ürünlerin üzerindeki kalp ikonuna tıklayarak listenize ekleyebilirsiniz.</p>
                        <a href="{{ url('/urunler') }}" class="inline-flex items-center gap-2 py-2.5 px-6 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold text-xs rounded-xl transition shadow-sm">
                            Ürünleri İncele
                        </a>
                    </div>
                @endif
            </div>

            <!-- SEKME 3: KULLANICI BİLGİLERİM -->
            <div id="tab-content-bilgiler" class="tab-content hidden">
                <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm">
                    <h2 class="text-base font-extrabold text-gray-800 mb-1 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fa-solid fa-user-pen text-blue-500"></i> Kişisel Bilgilerinizi Güncelleyin
                    </h2>

                    <form action="{{ route('profile.updateInfo') }}" method="POST" class="mt-4 space-y-4 max-w-xl">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Adınız Soyadınız *</label>
                            <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="w-full text-xs border border-gray-300 rounded-xl p-3 focus:border-[#C87A53] focus:ring-0 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">E-Posta Adresiniz *</label>
                            <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full text-xs border border-gray-300 rounded-xl p-3 focus:border-[#C87A53] focus:ring-0 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Telefon Numarası</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="05XX XXX XX XX" class="w-full text-xs border border-gray-300 rounded-xl p-3 focus:border-[#C87A53] focus:ring-0 outline-none">
                            <span class="text-[10px] text-gray-400 mt-1 block">Sipariş güncellemeleri ve kargo SMS bilgilendirmeleri için kullanılır.</span>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="py-3 px-8 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl text-xs transition shadow-sm inline-flex items-center gap-2">
                                <i class="fa-solid fa-save"></i> Bilgilerimi Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SEKME 4: ADRES BİLGİLERİM -->
            <div id="tab-content-adres" class="tab-content hidden">
                <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm">
                    <h2 class="text-base font-extrabold text-gray-800 mb-1 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fa-solid fa-location-dot text-emerald-600"></i> Teslimat ve Fatura Adresiniz
                    </h2>
                    <p class="text-xs text-gray-500 mt-2 mb-4">Buraya kaydedeceğiniz adres bilgileri ödeme sayfasında otomatik doldurulacaktır.</p>

                    <form action="{{ route('profile.updateAddress') }}" method="POST" class="space-y-4 max-w-xl">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">İl (Şehir) *</label>
                                <input type="text" name="city" required value="{{ old('city', $user->city) }}" placeholder="Örn: Manisa" class="w-full text-xs border border-gray-300 rounded-xl p-3 focus:border-[#C87A53] focus:ring-0 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">İlçe</label>
                                <input type="text" name="district" value="{{ old('district', $user->district) }}" placeholder="Örn: Şehzadeler" class="w-full text-xs border border-gray-300 rounded-xl p-3 focus:border-[#C87A53] focus:ring-0 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Açık Adres (Mahalle, Cadde, Sokak, No, Daire) *</label>
                            <textarea name="address" rows="3" required placeholder="Açık teslimat adresinizi yazınız..." class="w-full text-xs border border-gray-300 rounded-xl p-3 focus:border-[#C87A53] focus:ring-0 outline-none">{{ old('address', $user->address) }}</textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="py-3 px-8 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl text-xs transition shadow-sm inline-flex items-center gap-2">
                                <i class="fa-solid fa-save"></i> Adres Bilgilerini Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SEKME 5: ŞİFRE & GÜVENLİK -->
            <div id="tab-content-sifre" class="tab-content hidden">
                <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm">
                    <h2 class="text-base font-extrabold text-gray-800 mb-1 flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="fa-solid fa-shield-halved text-purple-600"></i> Şifre Değiştirme
                    </h2>

                    <form action="{{ route('profile.updatePassword') }}" method="POST" class="mt-4 space-y-4 max-w-xl">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Mevcut Şifreniz *</label>
                            <input type="password" name="current_password" required class="w-full text-xs border border-gray-300 rounded-xl p-3 focus:border-[#C87A53] focus:ring-0 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Yeni Şifreniz (En az 6 karakter) *</label>
                            <input type="password" name="password" required class="w-full text-xs border border-gray-300 rounded-xl p-3 focus:border-[#C87A53] focus:ring-0 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Yeni Şifre Tekrarı *</label>
                            <input type="password" name="password_confirmation" required class="w-full text-xs border border-gray-300 rounded-xl p-3 focus:border-[#C87A53] focus:ring-0 outline-none">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="py-3 px-8 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl text-xs transition shadow-sm inline-flex items-center gap-2">
                                <i class="fa-solid fa-[#C87A53] fa-key"></i> Şifreyi Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function switchTab(tabId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    
    // Reset all tab button styles
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('bg-amber-50', 'text-[#C87A53]', 'shadow-sm', 'border', 'border-amber-200/60');
        btn.classList.add('text-gray-600');
    });

    // Show target content
    const targetContent = document.getElementById('tab-content-' + tabId);
    if (targetContent) {
        targetContent.classList.remove('hidden');
    }

    // Active button style
    const targetBtn = document.getElementById('tab-btn-' + tabId);
    if (targetBtn) {
        targetBtn.classList.remove('text-gray-600');
        targetBtn.classList.add('bg-amber-50', 'text-[#C87A53]', 'shadow-sm', 'border', 'border-amber-200/60');
    }

    // Update URL query string without reloading page
    const url = new URL(window.location);
    url.searchParams.set('tab', tabId);
    window.history.replaceState({}, '', url);
}

// Auto open tab from URL parameter (e.g. ?tab=adres) or default to 'siparisler'
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'siparisler';
    switchTab(activeTab);
});
</script>
@endsection
