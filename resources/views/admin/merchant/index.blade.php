@extends('layouts.admin')

@section('header', 'Google Merchant Center')

@section('content')
<div class="space-y-6">

    {{-- Başlık ve Durum Kartı --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:#4285F4">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22.288 11.285l-10.01-10.01a1.83 1.83 0 00-2.588 0L7.576 3.39l3.275 3.275a2.17 2.17 0 012.74 2.767l3.157 3.157a2.17 2.17 0 11-1.302 1.302L12.47 10.91v7.04a2.172 2.172 0 11-1.787-.108V10.82a2.172 2.172 0 01-1.178-2.855L6.265 4.712 1.713 9.264a1.83 1.83 0 000 2.59l10.01 10.008a1.83 1.83 0 002.588 0l7.977-7.977a1.83 1.83 0 000-2.6"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Google Merchant Center</h2>
                    <p class="text-xs text-gray-500">Ürünlerinizi Google Shopping'e senkronize edin</p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                {{-- Bağlantı Durumu --}}
                @if($isConnected)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs font-semibold">
                        <span class="w-2 h-2 bg-green-500 rounded-full inline-block animate-pulse"></span>
                        Bağlı{{ $accountName ? ' — '.$accountName : '' }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-full text-xs font-semibold">
                        <span class="w-2 h-2 bg-red-500 rounded-full inline-block"></span>
                        Bağlı Değil
                    </span>
                @endif

                {{-- Tümünü Senkronize Et --}}
                @if($isConnected)
                    <form action="{{ route('admin.merchant.sync_all') }}" method="POST" id="syncAllForm">
                        @csrf
                        <button type="button" onclick="confirmSyncAll()"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-[#4285F4] hover:bg-[#3367D6] text-white text-xs font-bold rounded-lg transition shadow-sm">
                            <i class="fa-solid fa-rotate"></i>
                            Tümünü Senkronize Et
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Hata mesajı --}}
        @if($error && !$isConnected)
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm font-semibold text-red-700 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Bağlantı Hatası
                </p>
                <p class="text-xs text-red-600 mt-1">{{ $error }}</p>
                <div class="mt-3 p-3 bg-white border border-red-100 rounded text-xs text-gray-600 space-y-1">
                    <p class="font-semibold text-gray-700">Çözüm adımları:</p>
                    <ol class="list-decimal list-inside space-y-0.5 ml-2">
                        <li>Google Cloud Console'da <strong>Content API for Shopping</strong>'i etkinleştirin.</li>
                        <li><strong>Service Account</strong> oluşturun ve JSON key indirin.</li>
                        <li>JSON dosyasını sunucuya yükleyin (örn: <code>storage/app/google-service-account.json</code>).</li>
                        <li><code>.env</code> dosyasına ekleyin: <code>GOOGLE_SERVICE_ACCOUNT_JSON=/tam/yol/service-account.json</code></li>
                        <li>Merchant Center'da Service Account e-postasını <strong>Standard access</strong> ile yetkili yapın.</li>
                        <li><code>GOOGLE_MERCHANT_ID</code> değerini .env'e ekleyin.</li>
                    </ol>
                </div>
            </div>
        @endif
    </div>

    {{-- Flash Mesajları --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800 flex items-start gap-2">
            <i class="fa-solid fa-circle-check text-green-500 mt-0.5 shrink-0"></i>
            <span>{!! session('success') !!}</span>
        </div>
    @endif
    @if(session('warning'))
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800 flex items-start gap-2">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 shrink-0"></i>
            <span>{!! session('warning') !!}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800 flex items-start gap-2">
            <i class="fa-solid fa-circle-xmark text-red-500 mt-0.5 shrink-0"></i>
            <span>{!! session('error') !!}</span>
        </div>
    @endif

    {{-- Ürün Listesi --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-700">
                Ürünler
                <span class="ml-1.5 text-xs font-medium text-gray-400">({{ $products->count() }} ürün)</span>
            </h3>
            <p class="text-xs text-gray-400">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Görsel olmayan ürünler Merchant Center'a gönderilemez.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Ürün</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Fiyat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Stok</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Durum</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition" id="merchant-row-{{ $product->id }}">
                            {{-- Ürün --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($product->attributes['image'] ?? null)
                                        <img src="{{ url($product->attributes['image']) }}"
                                             alt="{{ $product->name }}"
                                             class="w-10 h-10 object-cover rounded-lg border border-gray-200 shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-lg border border-dashed border-red-300 bg-red-50 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-image-slash text-red-400 text-xs"></i>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 truncate max-w-[200px]">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-400">#{{ $product->id }}</p>
                                    </div>
                                </div>
                            </td>
                            {{-- Kategori --}}
                            <td class="px-4 py-3 text-gray-600 text-xs">
                                {{ $product->category->name ?? '—' }}
                            </td>
                            {{-- Fiyat --}}
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gray-800">{{ number_format($product->price, 2, ',', '.') }} ₺</span>
                                @if($product->original_price)
                                    <br><span class="text-xs text-gray-400 line-through">{{ number_format($product->original_price, 2, ',', '.') }} ₺</span>
                                @endif
                            </td>
                            {{-- Stok --}}
                            <td class="px-4 py-3">
                                @if($product->stock > 0)
                                    <span class="text-xs text-green-700 font-medium">{{ $product->stock }} adet</span>
                                @else
                                    <span class="text-xs text-red-600 font-medium">Stok yok</span>
                                @endif
                            </td>
                            {{-- Durum --}}
                            <td class="px-4 py-3">
                                @if($product->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-xs">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-500 border border-gray-200 rounded-full text-xs">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Pasif
                                    </span>
                                @endif
                                @if(!($product->attributes['image'] ?? null))
                                    <br><span class="text-xs text-red-500 mt-0.5 inline-block">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Görsel eksik
                                    </span>
                                @endif
                            </td>
                            {{-- İşlemler --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @if($isConnected)
                                        {{-- Merchant'a Gönder --}}
                                        @if($product->attributes['image'] ?? null)
                                            <button onclick="syncProduct({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg text-xs font-semibold transition"
                                                    title="Google Merchant Center'a Gönder"
                                                    id="sync-btn-{{ $product->id }}">
                                                <i class="fa-brands fa-google text-xs"></i> Gönder
                                            </button>
                                            <button onclick="deleteFromMerchant({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-lg text-xs font-semibold transition"
                                                    title="Merchant Center'dan Sil"
                                                    id="delete-btn-{{ $product->id }}">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Görsel gerekli</span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400 italic">API bağlı değil</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-box-open text-3xl mb-2 block"></i>
                                Henüz ürün eklenmemiş.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Kurulum Rehberi --}}
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
        <h4 class="text-sm font-bold text-blue-800 mb-3 flex items-center gap-2">
            <i class="fa-solid fa-book-open-reader text-blue-600"></i>
            Service Account Kurulum Rehberi
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-blue-900">
            <div class="space-y-2">
                <p class="font-semibold">Google Cloud Console (1 kez yapılır):</p>
                <ol class="list-decimal list-inside space-y-1 ml-2">
                    <li><a href="https://console.cloud.google.com" target="_blank" class="underline">console.cloud.google.com</a>'a gidin</li>
                    <li>Proje seçin veya yeni proje oluşturun</li>
                    <li><strong>API ve Hizmetler</strong> → <strong>Kitaplık</strong>'a gidin</li>
                    <li><em>Content API for Shopping</em>'i arayın ve <strong>Etkinleştir</strong>'e tıklayın</li>
                    <li><strong>Kimlik Bilgileri</strong> → <strong>Kimlik Bilgisi Oluştur</strong> → <strong>Hizmet Hesabı</strong></li>
                    <li>Hizmet hesabını oluşturun, <strong>JSON anahtar</strong> indirin</li>
                </ol>
            </div>
            <div class="space-y-2">
                <p class="font-semibold">Merchant Center (1 kez yapılır):</p>
                <ol class="list-decimal list-inside space-y-1 ml-2">
                    <li><a href="https://merchants.google.com" target="_blank" class="underline">merchants.google.com</a>'a gidin</li>
                    <li><strong>Ayarlar</strong> → <strong>Kullanıcılar</strong>'a gidin</li>
                    <li>Service Account e-postasını (örn: my-svc@project.iam.gserviceaccount.com) ekleyin</li>
                    <li><strong>Standard access</strong> verin</li>
                </ol>
                <p class="font-semibold mt-3">.env'e ekleyin:</p>
                <div class="bg-white/70 rounded p-2 font-mono text-xs space-y-0.5">
                    <p>GOOGLE_MERCHANT_ID=<em>buraya_merchant_id</em></p>
                    <p>GOOGLE_SERVICE_ACCOUNT_JSON=/full/path/service-account.json</p>
                    <p>MERCHANT_BRAND="Ahsap Evim Manisa"</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmSyncAll() {
    if (!confirm('Tüm aktif ürünler Google Merchant Center\'a gönderilecek. Bu işlem birkaç dakika sürebilir. Devam etmek istiyor musunuz?')) return;
    document.getElementById('syncAllForm').submit();
}

function syncProduct(id, name) {
    const btn = document.getElementById('sync-btn-' + id);
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Gönderiliyor...';

    fetch(`/yonetim/merchant-center/sync/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="fa-solid fa-check text-xs"></i> Gönderildi';
            btn.className = btn.className.replace('bg-blue-50 hover:bg-blue-100 text-blue-700 border-blue-200', 'bg-green-50 text-green-700 border-green-200');
            showToast(data.message, 'success');
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-brands fa-google text-xs"></i> Gönder';
            showToast(data.message, 'error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-brands fa-google text-xs"></i> Gönder';
        showToast('Bir hata oluştu.', 'error');
    });
}

function deleteFromMerchant(id, name) {
    if (!confirm(`"${name}" ürünü Merchant Center'dan silinecek. Onaylıyor musunuz?`)) return;

    const btn = document.getElementById('delete-btn-' + id);
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i>';

    fetch(`/yonetim/merchant-center/delete/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-trash-can text-xs"></i>';
        showToast(data.message, data.success ? 'success' : 'error');
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-trash-can text-xs"></i>';
        showToast('Bir hata oluştu.', 'error');
    });
}

function showToast(message, type = 'success') {
    const colors = {
        success: 'bg-green-600',
        error: 'bg-red-600',
        warning: 'bg-amber-500',
    };
    const toast = document.createElement('div');
    toast.className = `fixed bottom-6 right-6 z-50 px-4 py-3 rounded-xl text-white text-sm shadow-xl ${colors[type]} transition-all`;
    toast.innerHTML = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}
</script>
@endsection
