@extends('layouts.admin')

@section('header', 'Bilgilendirme Sayfaları Yönetimi')

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Bilgilendirme Sayfaları</h3>
            <p class="text-xs text-gray-500 mt-1">İletişim, SSS, Gizlilik, Sözleşme ve İade sayfalarının içeriklerini güncelleyin.</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="py-2.5 px-5 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold rounded-lg text-sm transition flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i> Yeni Sayfa Ekle
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <th class="pb-3">Sayfa Başlığı</th>
                    <th class="pb-3">URL Adresi (Slug)</th>
                    <th class="pb-3 text-center w-24">Durum</th>
                    <th class="pb-3 text-center w-32">Son Güncelleme</th>
                    <th class="pb-3 w-36 text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($pages as $page)
                    <tr>
                        <td class="py-4 font-bold text-gray-800">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-file-lines text-[#C87A53]"></i>
                                {{ $page->title }}
                            </div>
                        </td>
                        <td class="py-4 text-xs font-mono text-gray-500">
                            <a href="{{ url('/' . $page->slug) }}" target="_blank" class="hover:text-brand hover:underline">
                                /{{ $page->slug }} <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-1"></i>
                            </a>
                        </td>
                        <td class="py-4 text-center">
                            @if($page->is_active)
                                <span class="px-2.5 py-0.5 bg-green-100 text-green-700 rounded-full font-bold text-[10px]">Aktif</span>
                            @else
                                <span class="px-2.5 py-0.5 bg-gray-150 text-gray-500 rounded-full font-bold text-[10px]">Pasif</span>
                            @endif
                        </td>
                        <td class="py-4 text-center text-xs text-gray-500">
                            {{ $page->updated_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="py-4 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="py-1.5 px-3 bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold text-xs rounded-lg transition inline-flex items-center gap-1">
                                <i class="fa-solid fa-edit"></i> Düzenle
                            </a>
                            <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu sayfayı silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="py-1.5 px-3 bg-red-50 text-red-600 hover:bg-red-100 font-bold text-xs rounded-lg transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-trash"></i> Sil
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-500">Henüz kaydedilmiş bilgilendirme sayfası yok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection