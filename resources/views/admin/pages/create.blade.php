@extends('layouts.admin')

@section('header', 'Yeni Bilgilendirme Sayfası Ekle')

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm max-w-5xl">
    <div class="mb-6 pb-4 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Yeni Sayfa Bilgileri</h3>
            <p class="text-xs text-gray-500 mt-1">Eklenecek bilgilendirme sayfasının başlığını ve içeriğini giriniz.</p>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Geri Dön
        </a>
    </div>

    <form action="{{ route('admin.pages.store') }}" method="POST" id="createPageForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sayfa Başlığı *</label>
                <input type="text" name="title" required value="{{ old('title') }}" class="w-full text-sm border-gray-300 rounded-lg p-2.5 border focus:border-brand focus:ring-0 outline-none" placeholder="Örn: Kargo ve Teslimat Koşulları">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">URL Adresi / Slug (Opsiyonel)</label>
                <div class="flex items-center">
                    <span class="bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg px-3 py-2 text-xs text-gray-500 font-mono">{{ url('/') }}/</span>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="w-full text-sm border-gray-300 rounded-r-lg p-2.5 border focus:border-brand focus:ring-0 outline-none font-mono" placeholder="örn: kargo-kosullari">
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Sayfa İçeriği *</label>
            <div id="editorContainer" class="border border-gray-300 rounded-lg overflow-hidden"></div>
            <textarea id="pageContent" name="content" class="hidden" required>{{ old('content') }}</textarea>
        </div>

        <div class="flex items-center gap-2 mb-6">
            <input type="checkbox" name="is_active" id="isActive" value="1" checked class="rounded text-[#C87A53] focus:ring-[#C87A53] w-4 h-4 cursor-pointer">
            <label for="isActive" class="text-sm font-semibold text-gray-700 cursor-pointer select-none">Bu sayfayı mağazada yayınla (Aktif)</label>
        </div>

        <button type="submit" class="py-3 px-8 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-lg text-sm transition shadow-sm">
            <i class="fa-solid fa-plus mr-1"></i> Sayfayı Oluştur
        </button>
    </form>
</div>

<!-- CKEditor 5 - No API Key Required -->
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css" />
<script src="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.umd.js"></script>

<style>
.ck-editor__editable_inline {
    min-height: 480px !important;
    font-size: 14px !important;
}
.ck.ck-editor__main>.ck-editor__editable {
    min-height: 480px;
    padding: 16px 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    font-size: 14px;
    color: #374151;
    line-height: 1.7;
}
.ck.ck-toolbar {
    background: #f9fafb !important;
    border-color: #e5e7eb !important;
    padding: 4px 8px !important;
}
.ck.ck-toolbar__separator {
    background: #d1d5db !important;
}
.ck.ck-button.ck-on {
    color: #C87A53 !important;
    background: #fef3ec !important;
}
.ck.ck-button:hover:not(.ck-disabled) {
    background: #fef3ec !important;
    color: #C87A53 !important;
}
.ck.ck-editor__editable.ck-focused {
    border-color: #C87A53 !important;
    box-shadow: 0 0 0 2px rgba(200,122,83,0.15) !important;
}
</style>

<script>
const {
    ClassicEditor,
    Essentials,
    Bold, Italic, Underline, Strikethrough,
    Font,
    Paragraph,
    Heading,
    BlockQuote,
    Link,
    List,
    Indent, IndentBlock,
    Alignment,
    Table, TableToolbar, TableProperties, TableCellProperties,
    HorizontalLine,
    Undo
} = CKEDITOR;

ClassicEditor.create(document.querySelector('#editorContainer'), {
    plugins: [
        Essentials, Bold, Italic, Underline, Strikethrough,
        Font, Paragraph, Heading, BlockQuote, Link,
        List, Indent, IndentBlock, Alignment,
        Table, TableToolbar, TableProperties, TableCellProperties,
        HorizontalLine, Undo
    ],
    toolbar: {
        items: [
            'heading', '|',
            'bold', 'italic', 'underline', 'strikethrough', '|',
            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
            'alignment', '|',
            'bulletedList', 'numberedList', 'outdent', 'indent', '|',
            'blockQuote', 'link', 'insertTable', 'horizontalLine', '|',
            'undo', 'redo'
        ],
        shouldNotGroupWhenFull: true
    },
    heading: {
        options: [
            { model: 'paragraph', title: 'Normal Metin', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Başlık 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Başlık 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Başlık 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Başlık 4', class: 'ck-heading_heading4' },
        ]
    },
    fontSize: {
        options: [ 9, 10, 11, 12, 14, 16, 18, 20, 24, 28, 32, 36, 48 ],
        supportAllValues: true
    },
    fontFamily: {
        options: [
            'default',
            'Arial, Helvetica, sans-serif',
            'Georgia, serif',
            'Tahoma, Geneva, sans-serif',
            'Times New Roman, Times, serif',
            'Trebuchet MS, Helvetica, sans-serif',
            'Verdana, Geneva, sans-serif',
            'Courier New, Courier, monospace'
        ]
    },
    table: {
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
    },
    initialData: document.getElementById('pageContent').value
}).then(editor => {
    // Sync editor content to hidden textarea on form submit
    document.getElementById('createPageForm').addEventListener('submit', function() {
        document.getElementById('pageContent').value = editor.getData();
    });
    
    // Also update on any change
    editor.model.document.on('change:data', () => {
        document.getElementById('pageContent').value = editor.getData();
    });
}).catch(err => {
    console.error('CKEditor init error:', err);
});
</script>
@endsection