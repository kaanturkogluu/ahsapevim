@extends('layouts.admin')

@section('header', 'Ürünü Düzenle')

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm max-w-4xl">
    <div class="mb-6 pb-4 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Ürün Bilgileri</h3>
            <p class="text-xs text-gray-500 mt-1">Düzenlenen ürünün temel, indirim, galeri ve 3D özelliklerini güncelleyin.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Geri Dön
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Left Side: Basic Info & Pricing -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ürün Adı *</label>
                    <input type="text" name="name" required value="{{ old('name', $product->name) }}" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: 360 Dönen Masif Çerçeve">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori *</label>
                        <select name="category_id" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none bg-white">
                            <option value="">Seçin...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Stok Adedi *</label>
                        <input type="number" name="stock" required value="{{ old('stock', $product->stock) }}" min="0" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none">
                    </div>
                </div>

                @php
                    $isDiscounted = old('has_discount', $product->original_price > $product->price);
                    $normalPriceVal = $isDiscounted ? $product->original_price : $product->price;
                    $discountedPriceVal = $isDiscounted ? $product->price : '';
                @endphp

                <!-- Price & Discount Section -->
                <div class="p-4 bg-red-50/40 border border-red-200/50 rounded-xl space-y-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Normal Fiyat (TL) *</label>
                        <input type="number" id="normalPrice" name="price" required step="0.01" value="{{ old('price', $normalPriceVal) }}" min="0" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: 500.00" oninput="calculateDiscount()">
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" name="has_discount" id="hasDiscount" value="1" {{ $isDiscounted ? 'checked' : '' }} onchange="toggleDiscountBlock()" class="rounded text-red-600 focus:ring-red-500 w-4 h-4 cursor-pointer">
                        <label for="hasDiscount" class="text-sm font-bold text-red-700 cursor-pointer select-none">Bu Üründe İndirim Var</label>
                    </div>

                    <div id="discountBlock" class="{{ $isDiscounted ? '' : 'hidden' }} space-y-2 pt-1 border-t border-red-100">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-semibold text-gray-700">İndirimli Satış Fiyatı (TL) *</label>
                            <span id="discountBadge" class="hidden text-xs bg-red-600 text-white font-extrabold px-2 py-0.5 rounded-full"></span>
                        </div>
                        <input type="number" id="discountedPrice" name="discounted_price" step="0.01" value="{{ old('discounted_price', $discountedPriceVal) }}" min="0" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: 350.00" oninput="calculateDiscount()">
                        <p class="text-[11px] text-gray-500">Müşteriye <strong>Normal Fiyat</strong> çizili olarak, <strong>İndirimli Fiyat</strong> ve indirim oranı rozeti olarak gösterilecektir.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ahşap Rengi/Türü</label>
                        <input type="text" name="color" value="{{ old('color', $product->features['color'] ?? 'Ceviz') }}" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: Masif Meşe">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ölçü/Boyut</label>
                        <input type="text" name="size" value="{{ old('size', $product->features['size'] ?? '20x25 cm') }}" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: 15x21 cm">
                    </div>
                </div>
            </div>

            <!-- Right Side: Media & 3D Settings -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Görseli Değiştir (Ana Görsel)</label>
                    <input type="file" name="image" class="w-full text-sm border-gray-300 rounded-lg p-2 border focus:border-brand focus:ring-0 outline-none bg-gray-50">
                    
                    @if($product->image)
                        <div class="mt-2 flex items-center gap-3 bg-gray-50 p-2 rounded-lg border border-gray-150 w-fit">
                            <img src="{{ $product->image }}" class="h-12 w-10 object-contain" alt="old image">
                            <span class="text-xs text-gray-500 font-semibold">Mevcut ana görsel saklanıyor.</span>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ek Ürün Görselleri (Galeri Ekle/Yönet)</label>
                    <input type="file" name="gallery[]" multiple accept="image/*" class="w-full text-sm border-gray-300 rounded-lg p-2 border focus:border-brand focus:ring-0 outline-none bg-gray-50">
                    
                    @if(isset($product->features['images']) && is_array($product->features['images']) && count($product->features['images']) > 0)
                        <div class="mt-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Mevcut Galeri Görselleri (Silmek istediklerinizi işaretleyin):</label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($product->features['images'] as $gImg)
                                    <div class="relative group border border-gray-200 rounded-lg p-1 bg-white flex flex-col items-center">
                                        <img src="{{ str_starts_with($gImg, 'http') ? $gImg : url($gImg) }}" class="h-16 w-full object-contain rounded" alt="gallery image">
                                        <label class="mt-1 flex items-center gap-1 text-[11px] text-red-600 font-bold cursor-pointer">
                                            <input type="checkbox" name="remove_gallery[]" value="{{ $gImg }}" class="rounded text-red-600 focus:ring-red-500">
                                            Sil
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fa-brands fa-youtube text-red-600 text-base"></i> YouTube Tanıtım Video Linki (Opsiyonel)
                    </label>
                    <input type="url" name="youtube_url" value="{{ old('youtube_url', $product->features['youtube_url'] ?? '') }}" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: https://www.youtube.com/watch?v=XXXXXX">
                    <p class="text-[10px] text-gray-500 mt-1">Eklenirse ürün detay galerisinin sonuna video kapak resmi eklenir ve tıklanınca video açılır.</p>
                </div>

                <div class="p-4 bg-amber-50/50 border border-amber-200/50 rounded-xl">
                    <h4 class="text-sm font-bold text-amber-900 mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-cube text-brand"></i> 3D Model Entegrasyonu (Şablon)
                    </h4>
                    
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Ürün 3D Şablonu</label>
                        <select name="three_d_template_id" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none bg-white">
                            <option value="">-- Lütfen bir 3D Şablon seçin (Zorunlu) --</option>
                            @foreach($templates as $tpl)
                                <option value="{{ $tpl->id }}" {{ old('three_d_template_id', $product->three_d_template_id) == $tpl->id ? 'selected' : '' }}>{{ $tpl->name }} ({{ $tpl->wood_type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-[10px] text-gray-500">Ürünü anasayfa veya ürün detayındaki 3D Stüdyo/Önizleme ile bağdaştırmak için bir 3D çerçeve şablonu atayın.</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Ürün Açıklaması</label>
            <textarea name="description" rows="5" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Ürünün ahşap kalitesi, özellikleri ve el işçiliği hakkında detaylı bilgi yazın.">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="flex items-center gap-2 mb-6">
            <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded text-[#C87A53] focus:ring-[#C87A53] w-4 h-4">
            <label for="isActive" class="text-sm font-semibold text-gray-700 cursor-pointer">Bu ürünü mağazada hemen satışa aç (Aktif)</label>
        </div>

        <button type="submit" class="py-3 px-8 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-lg text-sm transition">Değişiklikleri Kaydet</button>
    </form>
</div>

<script>
function toggleDiscountBlock() {
    const hasDiscount = document.getElementById('hasDiscount').checked;
    const block = document.getElementById('discountBlock');
    if (hasDiscount) {
        block.classList.remove('hidden');
    } else {
        block.classList.add('hidden');
    }
    calculateDiscount();
}

function calculateDiscount() {
    const hasDiscount = document.getElementById('hasDiscount').checked;
    const normalPrice = parseFloat(document.getElementById('normalPrice').value) || 0;
    const discountedPrice = parseFloat(document.getElementById('discountedPrice').value) || 0;
    const badge = document.getElementById('discountBadge');

    if (hasDiscount && normalPrice > 0 && discountedPrice > 0 && discountedPrice < normalPrice) {
        const percent = Math.round((1 - (discountedPrice / normalPrice)) * 100);
        badge.innerText = '%' + percent + ' İNDİRİM';
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', calculateDiscount);
</script>
@endsection
