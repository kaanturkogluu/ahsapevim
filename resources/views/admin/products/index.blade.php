@extends('layouts.admin')

@section('header', 'Ürün Yönetimi')

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Ürün Listesi</h3>
            <p class="text-xs text-gray-500 mt-1">Mağazadaki tüm aktif ve pasif ürünleri yönetin ve sitedeki gösterim sırasını belirleyin.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" onclick="openAutoSortModal()" class="py-2 px-3 bg-purple-50 hover:bg-purple-100 text-purple-800 font-bold rounded-lg text-xs transition flex items-center gap-1.5 border border-purple-200">
                <i class="fa-solid fa-wand-magic-sparkles text-purple-600"></i> Otomatik Sırala
            </button>
            <button type="button" id="saveOrderBtn" onclick="saveProductOrders()" class="py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-floppy-disk"></i> Sıralamayı Kaydet
            </button>
            <a href="{{ route('seo.sitemap') }}" target="_blank" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs transition flex items-center gap-1.5 border border-gray-200">
                <i class="fa-solid fa-sitemap text-amber-700"></i> sitemap.xml
            </a>
            <a href="{{ route('seo.urunler_xml') }}" target="_blank" class="py-2 px-3 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold rounded-lg text-xs transition flex items-center gap-1.5 border border-amber-200">
                <i class="fa-solid fa-file-code text-amber-700"></i> urunler.xml
            </a>
            <a href="{{ route('admin.products.create') }}" class="py-2 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold rounded-lg text-xs transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-plus"></i> Yeni Ürün Ekle
            </a>
        </div>
    </div>

    <!-- Alert / Toast Banner -->
    <div id="orderStatusToast" class="hidden mb-4 p-3 rounded-lg text-xs font-bold transition"></div>

    <div class="admin-table-wrapper">
        <table class="w-full text-left border-collapse responsive-stack" style="min-width: 600px">
            <thead>
                <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <th class="pb-3 w-20 text-center">Sıra</th>
                    <th class="pb-3 w-16 text-center">Görsel</th>
                    <th class="pb-3">Ürün Adı</th>
                    <th class="pb-3">Kategori</th>
                    <th class="pb-3">3D Model Şablonu</th>
                    <th class="pb-3 text-right">Fiyat</th>
                    <th class="pb-3 text-center w-20">Stok</th>
                    <th class="pb-3 text-center w-20">Durum</th>
                    <th class="pb-3 w-32 text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody id="sortableProductsTable" class="divide-y divide-gray-100 text-sm">
                @forelse($products as $product)
                    <tr draggable="true" class="product-row hover:bg-amber-50/30 transition duration-150 cursor-grab active:cursor-grabbing" data-id="{{ $product->id }}">
                        <td class="py-3.5 text-center" data-label="Sıra">
                            <div class="flex items-center justify-center gap-1">
                                <i class="fa-solid fa-grip-vertical text-gray-300 hover:text-gray-600 handle cursor-grab text-sm" title="Sürükleyip Bırakın"></i>
                                <input type="number" 
                                       class="product-sort-input w-14 text-center text-xs font-bold border border-gray-200 rounded-md p-1 focus:border-[#C87A53] focus:ring-0 outline-none bg-white" 
                                       value="{{ $product->sort_order }}" 
                                       data-id="{{ $product->id }}" 
                                       min="1"
                                       onchange="highlightSaveBtn()">
                            </div>
                        </td>
                        <td class="py-3.5 text-center" data-label="Görsel">
                            <div class="w-12 h-14 bg-gray-50 rounded-lg border border-gray-150 overflow-hidden flex items-center justify-center p-1">
                                <img src="{{ $product->image ?: '/cerceve.png' }}" class="max-w-full max-h-full object-contain" alt="product">
                            </div>
                        </td>
                        <td class="py-3.5" data-label="Ürün">
                            <div class="font-bold text-gray-800">{{ $product->name }}</div>
                            <div class="text-[11px] font-mono text-gray-400 flex items-center gap-1 mt-0.5">
                                <a href="{{ $product->url }}" target="_blank" class="hover:text-brand hover:underline flex items-center gap-0.5">
                                    /urun/{{ $product->slug ?: $product->id }}
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                </a>
                            </div>
                            @if($product->discount_percent > 0)
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 font-extrabold text-[10px] rounded-full">%{{ $product->discount_percent }} İNDİRİMLİ</span>
                                </div>
                            @endif
                        </td>
                        <td class="py-3.5" data-label="Kategori">
                            <span class="px-2.5 py-1 bg-stone-100 text-stone-700 rounded-md font-semibold text-xs">{{ $product->category->name ?? 'Kategorisiz' }}</span>
                        </td>
                        <td class="py-3.5" data-label="3D Şablon">
                            @if($product->threeDTemplate)
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-800 rounded-md font-bold text-xs border border-amber-200/50 flex items-center gap-1.5 w-max">
                                    <i class="fa-solid fa-cube text-amber-600"></i> {{ $product->threeDTemplate->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 font-semibold"><i class="fa-solid fa-ban mr-1"></i> Yok (Sadece 2D)</span>
                            @endif
                        </td>
                        <td class="py-3.5 text-right font-bold text-gray-900" data-label="Fiyat">
                            @if($product->discount_percent > 0)
                                <div class="text-xs text-gray-400 line-through">{{ number_format($product->original_price, 2, ',', '.') }} TL</div>
                            @endif
                            <div class="text-[#C87A53]">{{ number_format($product->price, 2, ',', '.') }} TL</div>
                        </td>
                        <td class="py-3.5 text-center font-semibold text-gray-600" data-label="Stok">{{ $product->stock }}</td>
                        <td class="py-3.5 text-center" data-label="Durum">
                            @if($product->is_active)
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-bold text-[10px]">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-150 text-gray-500 rounded-full font-bold text-[10px]">Pasif</span>
                            @endif
                        </td>
                        <td class="py-3.5 text-right space-x-2 whitespace-nowrap" data-label="İşlem">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs"><i class="fa-solid fa-edit"></i> Düzenle</a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs"><i class="fa-solid fa-trash"></i> Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-gray-500">Mağazaya henüz ürün eklenmemiş.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Otomatik Sıralama Modal -->
<div id="autoSortModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl border border-gray-150 transform transition-all">
        <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
            <h3 class="font-extrabold text-gray-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles text-purple-600"></i> Otomatik Ürün Sıralama
            </h3>
            <button type="button" onclick="closeAutoSortModal()" class="text-gray-400 hover:text-gray-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.products.auto_sort') }}" method="POST">
            @csrf
            <p class="text-xs text-gray-600 mb-4">
                Seçeceğiniz kritere göre tüm ürünlerin sıralama numaraları (1, 2, 3...) baştan sona otomatik olarak yeniden atanacaktır.
            </p>

            <div class="space-y-2 mb-6">
                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50/50 cursor-pointer transition">
                    <input type="radio" name="sort_by" value="newest" checked class="text-purple-600 focus:ring-purple-500">
                    <div>
                        <div class="text-xs font-bold text-gray-800">En Yeni Ürünler En Üstte</div>
                        <div class="text-[11px] text-gray-400">Eklenme tarihine göre (Yeniden Eskiye)</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50/50 cursor-pointer transition">
                    <input type="radio" name="sort_by" value="oldest" class="text-purple-600 focus:ring-purple-500">
                    <div>
                        <div class="text-xs font-bold text-gray-800">En Eski Ürünler En Üstte</div>
                        <div class="text-[11px] text-gray-400">Eklenme tarihine göre (Eskiden Yeniye)</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50/50 cursor-pointer transition">
                    <input type="radio" name="sort_by" value="name_asc" class="text-purple-600 focus:ring-purple-500">
                    <div>
                        <div class="text-xs font-bold text-gray-800">Ürün Adına Göre (A'dan Z'ye)</div>
                        <div class="text-[11px] text-gray-400">Alfabetik sıralama</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50/50 cursor-pointer transition">
                    <input type="radio" name="sort_by" value="price_asc" class="text-purple-600 focus:ring-purple-500">
                    <div>
                        <div class="text-xs font-bold text-gray-800">Fiyata Göre (En Düşükten En Yükseğe)</div>
                        <div class="text-[11px] text-gray-400">Artan fiyat sırası</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50/50 cursor-pointer transition">
                    <input type="radio" name="sort_by" value="price_desc" class="text-purple-600 focus:ring-purple-500">
                    <div>
                        <div class="text-xs font-bold text-gray-800">Fiyata Göre (En Yüksekten En Düşüğe)</div>
                        <div class="text-[11px] text-gray-400">Azalan fiyat sırası</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50/50 cursor-pointer transition">
                    <input type="radio" name="sort_by" value="id_asc" class="text-purple-600 focus:ring-purple-500">
                    <div>
                        <div class="text-xs font-bold text-gray-800">Ürün ID Numarasına Göre (1, 2, 3...)</div>
                        <div class="text-[11px] text-gray-400">Orijinal veritabanı ID sırası</div>
                    </div>
                </label>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeAutoSortModal()" class="py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
                    İptal
                </button>
                <button type="submit" class="py-2.5 px-5 bg-purple-600 hover:bg-purple-700 text-white font-extrabold rounded-xl text-xs transition shadow-md">
                    <i class="fa-solid fa-bolt mr-1"></i> Sıralamayı Uygula
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAutoSortModal() {
    document.getElementById('autoSortModal').classList.remove('hidden');
}

function closeAutoSortModal() {
    document.getElementById('autoSortModal').classList.add('hidden');
}

function highlightSaveBtn() {
    const btn = document.getElementById('saveOrderBtn');
    btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
    btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'animate-pulse');
}

function resetSaveBtn() {
    const btn = document.getElementById('saveOrderBtn');
    btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700', 'animate-pulse');
    btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
}

function showToast(msg, type = 'success') {
    const toast = document.getElementById('orderStatusToast');
    toast.innerText = msg;
    toast.className = 'mb-4 p-3 rounded-lg text-xs font-bold transition ' + 
        (type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200');
    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 4000);
}

function saveProductOrders() {
    const inputs = document.querySelectorAll('.product-sort-input');
    const orders = [];
    inputs.forEach(input => {
        orders.push({
            id: parseInt(input.dataset.id),
            sort_order: parseInt(input.value) || 1
        });
    });

    sendOrderUpdatePayload(orders);
}

function sendOrderUpdatePayload(orders) {
    const btn = document.getElementById('saveOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Kaydediliyor...';

    fetch("{{ route('admin.products.update_order') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ orders: orders })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Sıralamayı Kaydet';
        resetSaveBtn();
        if (data.success) {
            showToast(data.message || 'Ürün sıralaması güncellendi.', 'success');
        } else {
            showToast('Sıralama güncellenirken bir hata oluştu.', 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Sıralamayı Kaydet';
        resetSaveBtn();
        showToast('Bağlantı hatası oluştu.', 'error');
    });
}

// ── HTML5 Drag and Drop Implementation ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('sortableProductsTable');
    if (!tbody) return;

    let draggedRow = null;

    tbody.addEventListener('dragstart', (e) => {
        const row = e.target.closest('.product-row');
        if (!row) return;
        draggedRow = row;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', '');
        setTimeout(() => row.classList.add('opacity-40', 'bg-purple-100'), 0);
    });

    tbody.addEventListener('dragend', (e) => {
        const row = e.target.closest('.product-row');
        if (row) row.classList.remove('opacity-40', 'bg-purple-100');
        draggedRow = null;
        
        // Drag bittiğinde görünür sırayı yeniden 1..N numaralandırıp sunucuya gönder
        reindexVisibleRows();
    });

    tbody.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        const targetRow = e.target.closest('.product-row');
        if (targetRow && targetRow !== draggedRow) {
            const rect = targetRow.getBoundingClientRect();
            const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
            tbody.insertBefore(draggedRow, next ? targetRow.nextSibling : targetRow);
        }
    });

    function reindexVisibleRows() {
        const rows = tbody.querySelectorAll('.product-row');
        const orders = [];
        rows.forEach((row, index) => {
            const newIndex = index + 1;
            const input = row.querySelector('.product-sort-input');
            if (input) {
                input.value = newIndex;
            }
            orders.push({
                id: parseInt(row.dataset.id),
                sort_order: newIndex
            });
        });

        highlightSaveBtn();
        sendOrderUpdatePayload(orders);
    }
});
</script>
@endsection

