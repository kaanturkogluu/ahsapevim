@extends('layouts.admin')

@section('header', 'Ürün Yönetimi')

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Ürün Listesi</h3>
            <p class="text-xs text-gray-500 mt-1">Mağazadaki tüm aktif ve pasif ürünleri yönetin.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="py-2.5 px-5 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold rounded-lg text-sm transition flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i> Yeni Ürün Ekle
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
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
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($products as $product)
                    <tr>
                        <td class="py-3.5 text-center">
                            <div class="w-12 h-14 bg-gray-50 rounded-lg border border-gray-150 overflow-hidden flex items-center justify-center p-1">
                                <img src="{{ $product->image ?: '/cerceve.png' }}" class="max-w-full max-h-full object-contain" alt="product">
                            </div>
                        </td>
                        <td class="py-3.5">
                            <div class="font-bold text-gray-800">{{ $product->name }}</div>
                            @if($product->discount_percent > 0)
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 font-extrabold text-[10px] rounded-full">%{{ $product->discount_percent }} İNDİRİMLİ</span>
                                </div>
                            @endif
                        </td>
                        <td class="py-3.5">
                            <span class="px-2.5 py-1 bg-stone-100 text-stone-700 rounded-md font-semibold text-xs">{{ $product->category->name ?? 'Kategorisiz' }}</span>
                        </td>
                        <td class="py-3.5">
                            @if($product->threeDTemplate)
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-800 rounded-md font-bold text-xs border border-amber-200/50 flex items-center gap-1.5 w-max">
                                    <i class="fa-solid fa-cube text-amber-600"></i> {{ $product->threeDTemplate->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 font-semibold"><i class="fa-solid fa-ban mr-1"></i> Yok (Sadece 2D)</span>
                            @endif
                        </td>
                        <td class="py-3.5 text-right font-bold text-gray-900">
                            @if($product->discount_percent > 0)
                                <div class="text-xs text-gray-400 line-through">{{ number_format($product->original_price, 2, ',', '.') }} TL</div>
                            @endif
                            <div class="text-[#C87A53]">{{ number_format($product->price, 2, ',', '.') }} TL</div>
                        </td>
                        <td class="py-3.5 text-center font-semibold text-gray-600">{{ $product->stock }}</td>
                        <td class="py-3.5 text-center">
                            @if($product->is_active)
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-bold text-[10px]">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-150 text-gray-500 rounded-full font-bold text-[10px]">Pasif</span>
                            @endif
                        </td>
                        <td class="py-3.5 text-right space-x-2 whitespace-nowrap">
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
                        <td colspan="8" class="py-12 text-center text-gray-500">Mağazaya henüz ürün eklenmemiş.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
