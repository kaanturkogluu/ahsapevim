@extends('layouts.admin')

@section('header', 'Ürünü Düzenle')

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm max-w-4xl">
    <div class="mb-6 pb-4 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Ürün Bilgileri</h3>
            <p class="text-xs text-gray-500 mt-1">Düzenlenen ürünün temel ve 3D özelliklerini güncelleyin.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Geri Dön
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Left Side: Basic Info -->
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

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Satış Fiyatı (TL) *</label>
                        <input type="number" name="price" required step="0.01" value="{{ old('price', $product->price) }}" min="0" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">İndirim Öncesi Fiyat (TL)</label>
                        <input type="number" name="original_price" step="0.01" value="{{ old('original_price', $product->original_price) }}" min="0" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="0.00">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ürün Kodu</label>
                        <input type="text" name="model_code" value="{{ old('model_code', $product->model_code) }}" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: AE-360-CEV">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Barkod</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: 8680000000">
                    </div>
                </div>
            </div>

            <!-- Right Side: Design & 3D Settings -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Görseli Değiştir</label>
                    <input type="file" name="image" class="w-full text-sm border-gray-300 rounded-lg p-2 border focus:border-brand focus:ring-0 outline-none bg-gray-50">
                    
                    @if($product->image)
                        <div class="mt-2 flex items-center gap-3 bg-gray-50 p-2 rounded-lg border border-gray-150 w-fit">
                            <img src="{{ $product->image }}" class="h-12 w-10 object-contain" alt="old image">
                            <span class="text-xs text-gray-500 font-semibold">Mevcut görsel saklanıyor.</span>
                        </div>
                    @endif
                </div>

                <div class="p-4 bg-amber-50/50 border border-amber-200/50 rounded-xl">
                    <h4 class="text-sm font-bold text-amber-900 mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-cube text-brand"></i> 3D Model Entegrasyonu (Şablon)
                    </h4>
                    
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Ürün 3D Şablonu</label>
                        <select name="three_d_template_id" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none bg-white">
                            <option value="">-- Şablon Yok (Yalnızca 2D Görsel) --</option>
                            @foreach($templates as $tpl)
                                <option value="{{ $tpl->id }}" {{ old('three_d_template_id', $product->three_d_template_id) == $tpl->id ? 'selected' : '' }}>{{ $tpl->name }} ({{ $tpl->wood_type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-[10px] text-gray-500">Ürünü anasayfa veya ürün detayındaki 3D Stüdyo/Önizleme ile bağdaştırmak için bir 3D çerçeve şablonu atayın. Yeni şablon oluşturmak için <a href="{{ route('admin.templates.create') }}" target="_blank" class="text-brand font-bold hover:underline">3D Şablon Oluşturucu</a> sayfasını kullanabilirsiniz.</p>
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
@endsection
