@extends('pages.layout')

@section('page_content')
<div>
    <!-- İletişim Bilgi Kartları Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        <!-- Telefon -->
        <div class="bg-gradient-to-br from-orange-50 to-white p-5 rounded-2xl border border-orange-100/80 shadow-sm flex items-start gap-4 transition hover:shadow-md">
            <div class="w-12 h-12 bg-[#C87A53] text-white rounded-xl flex items-center justify-center text-xl shrink-0 shadow-sm">
                <i class="fa-solid fa-phone"></i>
            </div>
            <div>
                <span class="text-xs font-extrabold text-[#C87A53] uppercase tracking-wider block">Müşteri Hizmetleri</span>
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactData['phone'] ?? '') }}" class="text-lg font-bold text-gray-800 hover:text-[#C87A53] transition block mt-0.5">
                    {{ $contactData['phone'] ?? '0850 XXX XX XX' }}
                </a>
                <span class="text-xs text-gray-500 mt-1 block">Hızlı çağrı desteği</span>
            </div>
        </div>

        <!-- WhatsApp -->
        <div class="bg-gradient-to-br from-green-50 to-white p-5 rounded-2xl border border-green-100/80 shadow-sm flex items-start gap-4 transition hover:shadow-md">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-xl flex items-center justify-center text-xl shrink-0 shadow-sm">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <span class="text-xs font-extrabold text-emerald-600 uppercase tracking-wider block">WhatsApp Destek</span>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactData['whatsapp'] ?? '') }}" target="_blank" class="text-lg font-bold text-gray-800 hover:text-emerald-600 transition block mt-0.5">
                    {{ $contactData['whatsapp'] ?? '05XX XXX XX XX' }}
                </a>
                <span class="text-xs text-emerald-600 font-medium mt-1 block flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Anında mesaj gönderin
                </span>
            </div>
        </div>

        <!-- Çalışma Saatleri -->
        <div class="bg-gradient-to-br from-purple-50 to-white p-5 rounded-2xl border border-purple-100/80 shadow-sm flex items-start gap-4 transition hover:shadow-md">
            <div class="w-12 h-12 bg-purple-600 text-white rounded-xl flex items-center justify-center text-xl shrink-0 shadow-sm">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <span class="text-xs font-extrabold text-purple-600 uppercase tracking-wider block">Çalışma Saatleri</span>
                <div class="text-sm font-bold text-gray-800 mt-0.5">
                    Hafta İçi: {{ $contactData['working_hours_weekdays'] ?? '09:00 - 18:00' }}
                </div>
                @if(!empty($contactData['working_hours_saturday']))
                    <div class="text-xs font-semibold text-gray-600 mt-0.5">
                        Cumartesi: {{ $contactData['working_hours_saturday'] }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Adres -->
        <div class="bg-gradient-to-br from-blue-50 to-white p-5 rounded-2xl border border-blue-100/80 shadow-sm flex items-start gap-4 transition hover:shadow-md">
            <div class="w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center text-xl shrink-0 shadow-sm">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
                <span class="text-xs font-extrabold text-blue-600 uppercase tracking-wider block">Atölye & Adres</span>
                <div class="text-sm font-bold text-gray-800 mt-0.5 whitespace-pre-line">
                    {{ $contactData['address'] ?? 'AhşapEvim Atölyesi' }}
                </div>
                @if(!empty($contactData['email']))
                    <a href="mailto:{{ $contactData['email'] }}" class="text-xs text-blue-600 hover:underline block mt-1">
                        {{ $contactData['email'] }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Ek Not/Açıklama (Varsa) -->
    @if(!empty($contactData['note']))
        <div class="bg-orange-50 border-l-4 border-[#C87A53] p-4 rounded-r-xl text-xs text-gray-700 leading-relaxed mb-8">
            <i class="fa-solid fa-info-circle text-[#C87A53] mr-1"></i> {{ $contactData['note'] }}
        </div>
    @endif

    <!-- Konum & Harita Görsel Kısım -->
    @if(!empty($contactData['map_url']))
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-extrabold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-map-marked-alt text-[#C87A53]"></i> Atölye ve Mağaza Konumumuz
                </h3>
                <span class="text-xs text-gray-500 font-medium">Manisa / Türkiye</span>
            </div>
            <div class="w-full h-80 rounded-2xl overflow-hidden border border-gray-200 shadow-sm bg-gray-100 relative">
                <iframe src="{{ $contactData['map_url'] }}" class="absolute inset-0 w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    @endif

    <!-- Bize Mesaj Gönderin Formu -->
    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200/80">
        <h3 class="text-base font-extrabold text-gray-800 mb-2">Bize Mesaj Gönderin</h3>
        <p class="text-xs text-gray-500 mb-4">Sorularınız, özel ahşap tasarım siparişleriniz veya önerileriniz için formu doldurabilirsiniz.</p>

        <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Mesajınız başarıyla iletildi. En kısa sürede sizinle iletişime geçeceğiz!'); this.reset();">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Adınız Soyadınız *</label>
                    <input type="text" required placeholder="Ahmet Yılmaz" class="w-full text-xs border border-gray-300 rounded-lg p-2.5 bg-white focus:border-brand focus:ring-0 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">E-Posta veya Telefon *</label>
                    <input type="text" required placeholder="05XX XXX XX XX" class="w-full text-xs border border-gray-300 rounded-lg p-2.5 bg-white focus:border-brand focus:ring-0 outline-none">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Konu *</label>
                <input type="text" required placeholder="Örn: Özel Ölçü Ahşap Masa Teklifi" class="w-full text-xs border border-gray-300 rounded-lg p-2.5 bg-white focus:border-brand focus:ring-0 outline-none">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Mesajınız *</label>
                <textarea rows="4" required placeholder="Mesajınızı buraya yazabilirsiniz..." class="w-full text-xs border border-gray-300 rounded-lg p-2.5 bg-white focus:border-brand focus:ring-0 outline-none"></textarea>
            </div>
            <button type="submit" class="py-2.5 px-6 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-lg text-xs transition shadow-sm inline-flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i> Mesajı Gönder
            </button>
        </form>
    </div>
</div>
@endsection
