@extends('layouts.admin')

@section('header', '3D Şablon Yönetimi')

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
        <div>
            <h3 class="text-lg font-bold text-gray-800">3D Çerçeve Şablonları</h3>
            <p class="text-xs text-gray-500 mt-1">Oluşturulan 3D çerçeve tasarımlarını ürünlerinizde kullanmak üzere şablon olarak kaydedin.</p>
        </div>
        <a href="{{ route('admin.templates.create') }}" class="py-2.5 px-5 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold rounded-lg text-sm transition flex items-center gap-2">
            <i class="fa-solid fa-cube"></i> Yeni 3D Şablon Oluştur
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <th class="pb-3 w-12 text-center">#</th>
                    <th class="pb-3">Şablon Adı</th>
                    <th class="pb-3">Ahşap Rengi / Türü</th>
                    <th class="pb-3 text-center">Dış Ebatlar (X x Y x Z)</th>
                    <th class="pb-3 text-center">İç Ebatlar (X x Y x Z)</th>
                    <th class="pb-3 text-center w-24">Kullanılan Ürünler</th>
                    <th class="pb-3 w-32 text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($templates as $tpl)
                    <tr>
                        <td class="py-3.5 text-center font-semibold text-gray-500">{{ $tpl->id }}</td>
                        <td class="py-3.5">
                            <div class="font-bold text-gray-800">{{ $tpl->name }}</div>
                        </td>
                        <td class="py-3.5">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-800 rounded-md font-semibold text-xs border border-amber-200/50">{{ $tpl->wood_type }}</span>
                        </td>
                        <td class="py-3.5 text-center font-mono text-xs text-gray-600">
                            {{ $tpl->width }} x {{ $tpl->height }} x {{ $tpl->depth }} (Et: {{ $tpl->thickness }})
                        </td>
                        <td class="py-3.5 text-center font-mono text-xs text-gray-600">
                            {{ $tpl->inner_width }} x {{ $tpl->inner_height }} x {{ $tpl->inner_depth }} (Kenar: {{ $tpl->inner_border }})
                        </td>
                        <td class="py-3.5 text-center font-bold text-[#C87A53]">{{ $tpl->products_count }}</td>
                        <td class="py-3.5 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.templates.edit', $tpl->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs"><i class="fa-solid fa-edit"></i> Düzenle</a>
                            <form action="{{ route('admin.templates.destroy', $tpl->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu şablonu silmek istediğinize emin misiniz? Bu şablonu kullanan ürünlerin 3D modeli pasif duruma düşecektir!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs"><i class="fa-solid fa-trash"></i> Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-500">Kayıtlı 3D şablon bulunamadı.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
