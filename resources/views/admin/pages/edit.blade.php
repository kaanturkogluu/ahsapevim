@extends('layouts.admin')

@section('header', 'Sayfayı Düzenle')

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm max-w-4xl">
    <div class="mb-6 pb-4 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800">{{ $page->title }} — İçerik Düzenleme</h3>
            <p class="text-xs text-gray-500 mt-1">Bu bilgilendirme sayfasının başlığını ve metin/HTML içeriğini güncelleyin.</p>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Geri Dön
        </a>
    </div>

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sayfa Başlığı *</label>
                <input type="text" name="title" required value="{{ old('title', $page->title) }}" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">URL Adresi (Slug) *</label>
                <div class="flex items-center">
                    <span class="bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg px-3 py-2 text-xs text-gray-500 font-mono">{{ url('/') }}/</span>
                    <input type="text" name="slug" required value="{{ old('slug', $page->slug) }}" class="w-full text-sm border-gray-300 rounded-r-lg p-2.5 border focus:border-brand focus:ring-0 outline-none font-mono">
                </div>
            </div>
        </div>

        <div class="mb-6">
            <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-semibold text-gray-700">Sayfa İçeriği (HTML / Metin) *</label>
                <span class="text-[11px] text-gray-400">HTML etiketleri (&lt;p&gt;, &lt;h3&gt;, &lt;strong&gt;, &lt;div&gt; vb.) kullanılabilir.</span>
            </div>
            <textarea name="content" rows="16" required class="w-full text-sm border-gray-300 rounded-lg p-3 border focus:border-brand focus:ring-0 outline-none font-mono leading-relaxed bg-gray-50/50">{{ old('content', $page->content) }}</textarea>
        </div>

        <div class="flex items-center gap-2 mb-6">
            <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }} class="rounded text-[#C87A53] focus:ring-[#C87A53] w-4 h-4 cursor-pointer">
            <label for="isActive" class="text-sm font-semibold text-gray-700 cursor-pointer select-none">Bu sayfayı yayında tut (Aktif)</label>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="py-3 px-8 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-lg text-sm transition shadow-sm">
                <i class="fa-solid fa-save mr-1"></i> Değişiklikleri Kaydet
            </button>
            <a href="{{ url('/' . $page->slug) }}" target="_blank" class="py-3 px-5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-sm transition">
                <i class="fa-solid fa-eye mr-1"></i> Önizle
            </a>
        </div>
    </form>
</div>
@endsection