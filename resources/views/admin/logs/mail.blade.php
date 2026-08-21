@extends('layouts.admin')

@section('header', 'E-Posta Gönderim Logları & Manuel Gönderim')

@section('content')
<div class="space-y-6">
    <!-- Action Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs">
        <div>
            <h3 class="text-base font-extrabold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-envelope-circle-check text-[#C87A53]"></i> E-Posta İletişim Geçmişi
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Sistem tarafından veya manuel gönderilen tüm e-postaları, durumlarını ve ret sebeplerini takip edebilirsiniz.</p>
        </div>

        <button type="button" onclick="openSendMailModal()" class="py-2.5 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-xl text-xs transition shadow-sm flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-paper-plane"></i> Manuel E-Posta Gönder
        </button>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <a href="{{ route('admin.mail_logs.index') }}" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition {{ !request('status') ? 'bg-[#29221C] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Tüm Gönderimler
            </a>
            <a href="{{ route('admin.mail_logs.index', ['status' => 'success']) }}" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition {{ request('status') === 'success' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                <i class="fa-solid fa-check text-[10px] mr-1"></i> Başarılılar
            </a>
            <a href="{{ route('admin.mail_logs.index', ['status' => 'failed']) }}" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition {{ request('status') === 'failed' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                <i class="fa-solid fa-xmark text-[10px] mr-1"></i> Başarısızlar
            </a>
        </div>

        <form action="{{ route('admin.mail_logs.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="E-posta, konu, sipariş no..." class="w-full md:w-64 text-xs border border-gray-300 rounded-xl px-3 py-2 outline-none focus:border-[#C87A53]">
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
                        <th class="p-4">Alıcı E-Posta</th>
                        <th class="p-4">Konu</th>
                        <th class="p-4 text-center">Tür</th>
                        <th class="p-4 text-center">Durum</th>
                        <th class="p-4 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="p-4">
                                <span class="font-mono font-bold text-gray-900 block">#{{ $log->id }}</span>
                                <span class="text-[11px] text-gray-400 block mt-0.5">{{ $log->created_at?->format('d.m.Y H:i') ?? '-' }}</span>
                                @if($log->order_id)
                                    <a href="{{ route('admin.orders.show', $log->order_id) }}" class="text-[10px] text-blue-600 hover:underline font-bold font-mono block mt-0.5">
                                        Sipariş #{{ $log->order_id }}
                                    </a>
                                @endif
                            </td>
                            <td class="p-4 font-bold text-gray-800">
                                {{ $log->to_email }}
                            </td>
                            <td class="p-4 max-w-xs truncate" title="{{ $log->subject }}">
                                {{ $log->subject }}
                            </td>
                            <td class="p-4 text-center">
                                @if($log->type === 'manual')
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-extrabold">Manuel</span>
                                @else
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-[10px] font-extrabold">Otomatik</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($log->status === 'success')
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-black inline-flex items-center gap-1">
                                        <i class="fa-solid fa-check"></i> Başarılı
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-[10px] font-black inline-flex items-center gap-1">
                                        <i class="fa-solid fa-xmark"></i> Başarısız
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-1 whitespace-nowrap">
                                <button type="button" onclick="viewMailDetail({{ json_encode($log) }})" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition">
                                    <i class="fa-solid fa-eye"></i> Detay
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-gray-400 font-bold">
                                Kaydedilmiş bir e-posta gönderim kaydı bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Manuel E-Posta Gönderme Modalı -->
<div id="sendMailModal" class="fixed inset-0 z-[99999] bg-black/70 backdrop-blur-xs hidden flex-col items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 relative">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-extrabold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-[#C87A53]"></i> Manuel E-Posta Gönder
            </h3>
            <button type="button" onclick="closeSendMailModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>

        <form action="{{ route('admin.mail.send_manual') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[11px] font-extrabold text-gray-500 uppercase mb-1">Alıcı E-Posta Adresi *</label>
                <input type="email" name="to_email" id="modalToEmail" required placeholder="ornek@musteri.com" class="w-full text-xs border border-gray-300 rounded-xl p-2.5 outline-none focus:border-[#C87A53]">
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-gray-500 uppercase mb-1">E-Posta Konusu *</label>
                <input type="text" name="subject" required placeholder="Mesajınızın konusu..." class="w-full text-xs border border-gray-300 rounded-xl p-2.5 outline-none focus:border-[#C87A53]">
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

<!-- Log Detay Görünüm Modalı -->
<div id="mailDetailModal" class="fixed inset-0 z-[99999] bg-black/70 backdrop-blur-xs hidden flex-col items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-200 relative max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="text-sm font-extrabold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-envelope-open text-[#C87A53]"></i> E-Posta Kayıt Detayı #<span id="detailLogId"></span>
            </h3>
            <button type="button" onclick="closeMailDetailModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>

        <div class="space-y-4 overflow-y-auto pr-1 text-xs text-gray-700">
            <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3 rounded-xl border border-gray-150">
                <div>
                    <span class="text-[10px] text-gray-400 font-extrabold uppercase block">Alıcı</span>
                    <strong id="detailToEmail" class="text-gray-900 font-bold"></strong>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-extrabold uppercase block">Tarih / Tür</span>
                    <span id="detailDateType" class="font-bold"></span>
                </div>
            </div>

            <div>
                <span class="text-[10px] text-gray-400 font-extrabold uppercase block mb-1">Konu</span>
                <div id="detailSubject" class="p-2.5 bg-white border border-gray-200 rounded-xl font-bold text-gray-800"></div>
            </div>

            <div id="detailErrorBox" class="hidden bg-rose-50 border border-rose-200 p-3 rounded-xl text-rose-900">
                <span class="text-[10px] font-extrabold uppercase block text-rose-700 mb-1">Hata / Başarısızlık Sebebi</span>
                <div id="detailErrorMessage" class="font-mono text-[11px] leading-relaxed"></div>
            </div>

            <div>
                <span class="text-[10px] text-gray-400 font-extrabold uppercase block mb-1">E-Posta İçerik Metni</span>
                <div id="detailBody" class="p-3 bg-gray-50 border border-gray-200 rounded-xl whitespace-pre-line leading-relaxed max-h-60 overflow-y-auto"></div>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeMailDetailModal()" class="py-2 px-5 bg-gray-800 text-white font-bold text-xs rounded-xl transition">
                Kapat
            </button>
        </div>
    </div>
</div>

<script>
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

function viewMailDetail(log) {
    document.getElementById('detailLogId').textContent = log.id;
    document.getElementById('detailToEmail').textContent = log.to_email;
    document.getElementById('detailDateType').textContent = (log.created_at || '') + ' (' + (log.type === 'manual' ? 'Manuel' : 'Otomatik') + ')';
    document.getElementById('detailSubject').textContent = log.subject;
    document.getElementById('detailBody').textContent = log.body || 'İçerik belirtilmemiş';

    const errBox = document.getElementById('detailErrorBox');
    if (log.status === 'failed' || log.error_message) {
        document.getElementById('detailErrorMessage').textContent = log.error_message || 'Bilinmeyen e-posta gönderim hatası.';
        errBox.classList.remove('hidden');
    } else {
        errBox.classList.add('hidden');
    }

    const modal = document.getElementById('mailDetailModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeMailDetailModal() {
    const modal = document.getElementById('mailDetailModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
</script>
@endsection
