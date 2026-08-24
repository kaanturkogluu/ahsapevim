@extends('layouts.admin')

@section('title', 'Anasayfa Görselleri Yönetimi - AhşapEvim Admin')

@section('header', 'Anasayfa Görselleri Yönetimi')

@section('content')
<div class="space-y-6">

    {{-- Üst Bilgi Kutusu & Yeni Görsel Yükleme --}}
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-images text-[#C87A53]"></i> Anasayfa Görsel & Banner Galerisi
                </h3>
                <p class="text-xs text-gray-500 mt-1">Anasayfadaki ürün sergileme ve vitrin alanında görünecek resimleri buradan ekleyebilir, sırasını değiştirebilir veya silip yenisini yükleyebilirsiniz.</p>
            </div>
            <button type="button" onclick="document.getElementById('addBannerSection').classList.toggle('hidden')" class="py-2.5 px-5 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold rounded-lg text-xs transition flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-plus text-xs"></i> Yeni Görsel Ekle
            </button>
        </div>

        {{-- Yeni Görsel Yükleme Formu --}}
        <div id="addBannerSection" class="hidden bg-amber-50/60 p-5 rounded-xl border border-amber-200/80 mb-6">
            <h4 class="text-sm font-bold text-amber-900 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up"></i> Yeni Görsel Yükle
            </h4>
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Görsel Seç *</label>
                    <input type="file" name="image" required accept="image/*" class="w-full text-xs bg-white border border-gray-300 rounded-lg p-2 focus:border-[#C87A53] outline-none">
                    <span class="text-[10px] text-gray-400 mt-1 block">JPG, PNG, WEBP formatı (Maks. 10MB)</span>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Görsel Adı / Etiket</label>
                    <input type="text" name="title" placeholder="Örn: Masif Çerçeve Koleksiyonu" class="w-full text-xs bg-white border border-gray-300 rounded-lg p-2.5 focus:border-[#C87A53] outline-none">
                </div>
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Sıralama No</label>
                        <input type="number" name="order" min="1" placeholder="Otomatik verilir" class="w-full text-xs bg-white border border-gray-300 rounded-lg p-2.5 focus:border-[#C87A53] outline-none">
                    </div>
                    <button type="submit" class="py-2.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition shadow-xs flex items-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-check"></i> Kaydet & Yükle
                    </button>
                </div>
            </form>
        </div>

        {{-- Görsel Kartları Izgarası (Grid View) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6">
            @forelse($banners as $banner)
                <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden flex flex-col justify-between shadow-xs group hover:border-[#C87A53] transition">
                    <!-- Image Preview -->
                    <div class="relative h-56 bg-stone-200 overflow-hidden flex items-center justify-center p-2">
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover rounded-lg group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute top-3 left-3 bg-black/70 text-white px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-xs flex items-center gap-1">
                            <i class="fa-solid fa-sort"></i> SIRA #{{ $banner->order }}
                        </div>
                        @if($banner->is_active)
                            <div class="absolute top-3 right-3 bg-emerald-600 text-white px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase shadow-xs">
                                Aktif
                            </div>
                        @else
                            <div class="absolute top-3 right-3 bg-gray-600 text-white px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase shadow-xs">
                                Pasif
                            </div>
                        @endif
                    </div>

                    <!-- Card Body / Edit Form -->
                    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-3 bg-white flex-grow flex flex-col justify-between">
                        @csrf
                        @method('PUT')

                        <div class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase">Görsel Adı / Başlık</label>
                                <input type="text" name="title" value="{{ $banner->title }}" class="w-full text-xs font-semibold border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] outline-none">
                            </div>

                            <div class="grid grid-cols-2 gap-2 items-center">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase">Sıra No</label>
                                    <input type="number" name="order" value="{{ $banner->order }}" min="1" class="w-full text-xs font-bold border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] outline-none">
                                </div>
                                <div class="pt-4">
                                    <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer select-none">
                                        <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }} class="w-4 h-4 text-[#C87A53] rounded border-gray-300">
                                        <span>Aktif Göster</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Görseli Değiştir (İsteğe Bağlı)</label>
                                <input type="file" name="image" accept="image/*" class="w-full text-[11px] text-gray-500 border border-gray-200 rounded-lg p-1.5">
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-3 border-t border-gray-100">
                            <button type="submit" class="py-1.5 px-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition inline-flex items-center gap-1">
                                <i class="fa-solid fa-save text-xs"></i> Güncelle
                            </button>

                            <button type="button" onclick="confirmDeleteBanner({{ $banner->id }}, '{{ e($banner->title) }}')" class="py-1.5 px-3 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-lg transition inline-flex items-center gap-1">
                                <i class="fa-solid fa-trash text-xs"></i> Sil
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="col-span-3 py-12 text-center text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <i class="fa-solid fa-image text-3xl text-gray-300 mb-2"></i>
                    <p class="font-bold">Henüz anasayfa görseli yüklenmemiş.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Silme Onay Formu (Gizli) -->
<form id="deleteBannerForm" action="" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDeleteBanner(id, title) {
    if (confirm(`"${title}" görselini silmek istediğinize emin misiniz?`)) {
        const form = document.getElementById('deleteBannerForm');
        form.action = `/yonetim/anasayfa-gorselleri/${id}`;
        form.submit();
    }
}
</script>
@endsection
