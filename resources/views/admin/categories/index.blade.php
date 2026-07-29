@extends('layouts.admin')

@section('header', 'Kategori Yönetimi')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Category Form (Add/Edit) -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm h-fit">
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100" id="formTitle">Yeni Kategori Ekle</h3>
        
        <form action="{{ route('admin.categories.store') }}" method="POST" id="categoryForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Adı *</label>
                <input type="text" name="name" id="categoryName" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: Bebek Hediyelik">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Açıklama</label>
                <textarea name="description" id="categoryDescription" rows="4" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Kategori açıklaması..."></textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold rounded-lg text-sm transition">Kaydet</button>
                <button type="button" onclick="resetForm()" id="btnCancel" class="hidden px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-sm transition">İptal</button>
            </div>
        </form>
    </div>

    <!-- Category List -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Mevcut Kategoriler</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                        <th class="pb-3 w-12 text-center">#</th>
                        <th class="pb-3">Kategori Adı</th>
                        <th class="pb-3">Slug (URL)</th>
                        <th class="pb-3 w-20 text-center">Ürün Sayısı</th>
                        <th class="pb-3 w-24 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($categories as $cat)
                        <tr>
                            <td class="py-3.5 text-center font-semibold text-gray-500">{{ $cat->id }}</td>
                            <td class="py-3.5">
                                <div class="font-bold text-gray-800">{{ $cat->name }}</div>
                                @if($cat->description)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($cat->description, 60) }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 text-gray-500 font-mono text-xs">{{ $cat->slug }}</td>
                            <td class="py-3.5 text-center font-bold text-[#C87A53]">{{ $cat->products_count }}</td>
                            <td class="py-3.5 text-right space-x-2 whitespace-nowrap">
                                <button type="button" onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description) }}')" class="text-blue-600 hover:text-blue-800 font-bold text-xs"><i class="fa-solid fa-edit"></i> Düzenle</button>
                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Kategoriyi silmek istediğinize emin misiniz? Bu kategoriye bağlı tüm ürünler silinecektir!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs"><i class="fa-solid fa-trash"></i> Sil</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">Henüz kategori eklenmemiş.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function editCategory(id, name, description) {
        document.getElementById('formTitle').innerText = "Kategoriyi Düzenle";
        document.getElementById('categoryForm').action = "/yonetim/kategoriler/" + id;
        document.getElementById('formMethod').value = "PUT";
        document.getElementById('categoryName').value = name;
        document.getElementById('categoryDescription').value = description;
        document.getElementById('btnCancel').classList.remove('hidden');
    }

    function resetForm() {
        document.getElementById('formTitle').innerText = "Yeni Kategori Ekle";
        document.getElementById('categoryForm').action = "{{ route('admin.categories.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('categoryName').value = "";
        document.getElementById('categoryDescription').value = "";
        document.getElementById('btnCancel').classList.add('hidden');
    }
</script>
@endsection
