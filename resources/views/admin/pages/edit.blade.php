@extends('layouts.admin')

@section('header', 'Sayfayı Düzenle')

@section('content')
<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm max-w-6xl">
    <div class="mb-6 pb-4 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800">{{ $page->title }} — İçerik Düzenleme</h3>
            <p class="text-xs text-gray-500 mt-1">
                @if($page->slug === 'iletisim')
                    İletişim bilgilerini, sosyal hatları ve konum haritasını bu sayfadan hızlıca güncelleyin.
                @elseif($page->slug === 'sikca-sorulanlar')
                    Sıkça sorulan soruları, cevaplarını ve gösterim durumunu bu panelden yönetin.
                @else
                    Bu bilgilendirme sayfasının başlığını ve içeriğini güncelleyin.
                @endif
            </p>
        </div>
        <a href="{{ route('admin.pages.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    @if($page->slug === 'iletisim')
        <!-- İletişim Sayfası İçin Düzenli 2 Sütunlu Compact Form -->
        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Sol Kolon: İletişim Bilgileri (8 Sütun) -->
                <div class="lg:col-span-8 space-y-4">
                    
                    <!-- Sayfa Başlığı -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Sayfa Başlığı *</label>
                                <input type="text" name="title" required value="{{ old('title', $page->title) }}" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">URL Adresi</label>
                                <input type="text" value="/iletisim" disabled class="w-full text-xs border-gray-200 bg-gray-100 text-gray-400 rounded-lg p-2 border font-mono">
                            </div>
                        </div>
                    </div>

                    <!-- İletişim Kanalları -->
                    <div class="bg-orange-50/40 p-4 rounded-xl border border-orange-100">
                        <h4 class="text-xs font-bold text-[#C87A53] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <i class="fa-solid fa-headset"></i> İletişim & Destek Hatları
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    <i class="fa-solid fa-phone text-orange-500 mr-1"></i> Müşteri Hizmetleri
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $contactData['phone'] ?? '') }}" placeholder="0850 123 45 67" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    <i class="fa-brands fa-whatsapp text-green-600 mr-1"></i> WhatsApp Hattı
                                </label>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp', $contactData['whatsapp'] ?? '') }}" placeholder="0532 123 45 67" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    <i class="fa-solid fa-envelope text-blue-500 mr-1"></i> E-Posta Adresi
                                </label>
                                <input type="email" name="email" value="{{ old('email', $contactData['email'] ?? '') }}" placeholder="info@ahsapevim.com" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Çalışma Saatleri & Adres -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80">
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <i class="fa-solid fa-clock text-purple-600"></i> Çalışma Saatleri & Adres
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Hafta İçi Saatleri</label>
                                <input type="text" name="working_hours_weekdays" value="{{ old('working_hours_weekdays', $contactData['working_hours_weekdays'] ?? '') }}" placeholder="09:00 - 18:00" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Cumartesi Saatleri</label>
                                <input type="text" name="working_hours_saturday" value="{{ old('working_hours_saturday', $contactData['working_hours_saturday'] ?? '') }}" placeholder="10:00 - 15:00" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                <i class="fa-solid fa-location-dot text-red-500 mr-1"></i> Açık Adres
                            </label>
                            <textarea name="address" rows="2" placeholder="Atölye ve mağaza adresi..." class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none">{{ old('address', $contactData['address'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Ek Not -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Ek Not / Özel Bilgilendirme (Opsiyonel)</label>
                        <textarea name="note" rows="2" placeholder="Sayfada duyuru veya ek not olarak görünecek kısa metin..." class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none">{{ old('note', $contactData['note'] ?? '') }}</textarea>
                    </div>
                </div>

                <!-- Sağ Kolon: Harita Konumu + Kaydet & Aksiyon Kartı (4 Sütun Sticky) -->
                <div class="lg:col-span-4 space-y-4 lg:sticky lg:top-4">
                    
                    <!-- Kaydet & Aksiyon Kutusu -->
                    <div class="bg-white p-4 rounded-xl border-2 border-[#C87A53]/30 shadow-sm">
                        <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3 flex items-center justify-between">
                            <span>İşlemler</span>
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        </h4>

                        <div class="flex items-center gap-2 mb-4">
                            <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }} class="rounded text-[#C87A53] focus:ring-[#C87A53] w-4 h-4 cursor-pointer">
                            <label for="isActive" class="text-xs font-semibold text-gray-700 cursor-pointer select-none">Sayfayı Yayında Tut (Aktif)</label>
                        </div>

                        <div class="space-y-2">
                            <button type="submit" class="w-full py-2.5 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-lg text-xs transition shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-save"></i> Değişiklikleri Kaydet
                            </button>
                            <a href="{{ url('/iletisim') }}" target="_blank" class="w-full py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-eye"></i> Sayfayı Önizle
                            </a>
                        </div>
                    </div>

                    <!-- Harita Kutusu -->
                    <div class="bg-blue-50/40 p-4 rounded-xl border border-blue-100">
                        <h4 class="text-xs font-bold text-blue-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-map-location-dot"></i> Google Harita Konumu
                        </h4>
                        <p class="text-[11px] text-gray-500 mb-2">Google Maps Embed (`src="..."`) linkini yapıştırın:</p>
                        <input type="text" name="map_url" value="{{ old('map_url', $contactData['map_url'] ?? '') }}" placeholder="https://www.google.com/maps/embed?pb=..." class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-brand focus:ring-0 outline-none font-mono mb-2">

                        @if(!empty($contactData['map_url']))
                            <div class="w-full h-36 rounded-lg overflow-hidden border border-gray-200 bg-gray-100 relative mt-2">
                                <iframe src="{{ $contactData['map_url'] }}" class="w-full h-full border-0" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        </form>
    @elseif($page->slug === 'sikca-sorulanlar')
        <!-- Sıkça Sorulan Sorular (SSS) Yönetimi -->
        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Sol Kolon: SSS Soru & Cevap Listesi (8 Sütun) -->
                <div class="lg:col-span-8 space-y-4">
                    
                    <!-- Sayfa Başlığı ve Açıklama -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200/80">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Sayfa Başlığı *</label>
                                <input type="text" name="title" required value="{{ old('title', $page->title) }}" class="w-full text-xs border-gray-300 rounded-lg p-2 border focus:border-[#C87A53] focus:ring-0 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">URL Adresi</label>
                                <input type="text" value="/sikca-sorulanlar" disabled class="w-full text-xs border-gray-200 bg-gray-100 text-gray-400 rounded-lg p-2 border font-mono">
                            </div>
                        </div>
                    </div>

                    <!-- Soru ve Cevap Listesi -->
                    <div class="bg-amber-50/40 p-4 rounded-xl border border-amber-200/70">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-xs font-bold text-[#C87A53] uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-question"></i> Soru & Cevap Listesi
                            </h4>
                            <button type="button" onclick="addFaqRow()" class="py-1.5 px-3 bg-[#C87A53] hover:bg-[#A65F38] text-white text-xs font-bold rounded-lg transition flex items-center gap-1 shadow-sm">
                                <i class="fa-solid fa-plus text-[10px]"></i> Yeni Soru Ekle
                            </button>
                        </div>

                        <div id="faqContainer" class="space-y-4">
                            @php $faqs = old('faqs', $faqItems ?? []); @endphp
                            @forelse($faqs as $index => $item)
                                <div class="faq-row bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative transition hover:border-amber-300">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-amber-700 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-question text-[#C87A53] mr-1"></i> Soru #<span class="faq-number">{{ $index + 1 }}</span>
                                        </span>
                                        <button type="button" onclick="removeFaqRow(this)" class="text-xs text-red-500 hover:text-red-700 font-bold px-2 py-1 bg-red-50 hover:bg-red-100 rounded-lg transition flex items-center gap-1">
                                            <i class="fa-solid fa-trash-can"></i> Sil
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Soru Metni</label>
                                            <input type="text" name="faqs[{{ $index }}][question]" value="{{ $item['question'] ?? '' }}" placeholder="Örn: Siparişim ne zaman kargoya verilir?" class="w-full text-xs font-semibold border-gray-300 rounded-lg p-2.5 border focus:border-[#C87A53] focus:ring-0 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Cevap Metni</label>
                                            <textarea name="faqs[{{ $index }}][answer]" rows="3" placeholder="Sorunun ayrıntılı yanıtı..." class="w-full text-xs border-gray-300 rounded-lg p-2.5 border focus:border-[#C87A53] focus:ring-0 outline-none">{{ $item['answer'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="faq-row bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative transition hover:border-amber-300">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-amber-700 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-question text-[#C87A53] mr-1"></i> Soru #<span class="faq-number">1</span>
                                        </span>
                                        <button type="button" onclick="removeFaqRow(this)" class="text-xs text-red-500 hover:text-red-700 font-bold px-2 py-1 bg-red-50 hover:bg-red-100 rounded-lg transition flex items-center gap-1">
                                            <i class="fa-solid fa-trash-can"></i> Sil
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Soru Metni</label>
                                            <input type="text" name="faqs[0][question]" value="" placeholder="Örn: Siparişim ne zaman kargoya verilir?" class="w-full text-xs font-semibold border-gray-300 rounded-lg p-2.5 border focus:border-[#C87A53] focus:ring-0 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Cevap Metni</label>
                                            <textarea name="faqs[0][answer]" rows="3" placeholder="Sorunun ayrıntılı yanıtı..." class="w-full text-xs border-gray-300 rounded-lg p-2.5 border focus:border-[#C87A53] focus:ring-0 outline-none"></textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-4 pt-3 border-t border-amber-200/50 flex justify-center">
                            <button type="button" onclick="addFaqRow()" class="py-2 px-4 bg-white border border-dashed border-[#C87A53] text-[#C87A53] hover:bg-amber-50 text-xs font-bold rounded-lg transition flex items-center gap-1.5 w-full justify-center">
                                <i class="fa-solid fa-plus text-xs"></i> Yeni Bir Soru Ekle
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon: Kaydet & İşlemler (4 Sütun Sticky) -->
                <div class="lg:col-span-4 space-y-4 lg:sticky lg:top-4">
                    <div class="bg-white p-4 rounded-xl border-2 border-[#C87A53]/30 shadow-sm">
                        <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3 flex items-center justify-between">
                            <span>İşlemler</span>
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        </h4>

                        <div class="flex items-center gap-2 mb-4">
                            <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }} class="rounded text-[#C87A53] focus:ring-[#C87A53] w-4 h-4 cursor-pointer">
                            <label for="isActive" class="text-xs font-semibold text-gray-700 cursor-pointer select-none">Sayfayı Yayında Tut (Aktif)</label>
                        </div>

                        <div class="space-y-2">
                            <button type="submit" class="w-full py-2.5 px-4 bg-[#C87A53] hover:bg-[#A65F38] text-white font-extrabold rounded-lg text-xs transition shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-save"></i> Değişiklikleri Kaydet
                            </button>
                            <a href="{{ url('/sikca-sorulanlar') }}" target="_blank" class="w-full py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-eye"></i> Sayfayı Önizle
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </form>

        <script>
        function updateFaqIndexes() {
            const rows = document.querySelectorAll('#faqContainer .faq-row');
            rows.forEach((row, index) => {
                const numberEl = row.querySelector('.faq-number');
                if (numberEl) numberEl.textContent = index + 1;
                const qInput = row.querySelector('input[name*="[question]"]');
                const aInput = row.querySelector('textarea[name*="[answer]"]');
                if (qInput) qInput.name = `faqs[${index}][question]`;
                if (aInput) aInput.name = `faqs[${index}][answer]`;
            });
        }

        function addFaqRow() {
            const container = document.getElementById('faqContainer');
            const newIndex = container.querySelectorAll('.faq-row').length;
            
            const div = document.createElement('div');
            div.className = 'faq-row bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative transition hover:border-amber-300';
            div.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-amber-700 flex items-center gap-1">
                        <i class="fa-solid fa-circle-question text-[#C87A53] mr-1"></i> Soru #<span class="faq-number">${newIndex + 1}</span>
                    </span>
                    <button type="button" onclick="removeFaqRow(this)" class="text-xs text-red-500 hover:text-red-700 font-bold px-2 py-1 bg-red-50 hover:bg-red-100 rounded-lg transition flex items-center gap-1">
                        <i class="fa-solid fa-trash-can"></i> Sil
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Soru Metni</label>
                        <input type="text" name="faqs[${newIndex}][question]" placeholder="Örn: Siparişim ne zaman kargoya verilir?" class="w-full text-xs font-semibold border-gray-300 rounded-lg p-2.5 border focus:border-[#C87A53] focus:ring-0 outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Cevap Metni</label>
                        <textarea name="faqs[${newIndex}][answer]" rows="3" placeholder="Sorunun ayrıntılı yanıtı..." class="w-full text-xs border-gray-300 rounded-lg p-2.5 border focus:border-[#C87A53] focus:ring-0 outline-none"></textarea>
                    </div>
                </div>
            `;
            container.appendChild(div);
            updateFaqIndexes();
            div.querySelector('input').focus();
        }

        function removeFaqRow(button) {
            const row = button.closest('.faq-row');
            const container = document.getElementById('faqContainer');
            if (container.querySelectorAll('.faq-row').length > 1) {
                row.remove();
                updateFaqIndexes();
            } else {
                alert('En az bir soru kartı kalmalıdır.');
            }
        }
        </script>
    @else
        <!-- Standart Metin Sayfaları İçin CKEditor Metin Düzenleyici -->
        <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" id="editPageForm">
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
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sayfa İçeriği *</label>
                <div id="editorContainer" class="border border-gray-300 rounded-lg overflow-hidden"></div>
                <textarea id="pageContent" name="content" class="hidden" required>{!! old('content', $page->content) !!}</textarea>
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

        <!-- CKEditor 5 -->
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
            document.getElementById('editPageForm').addEventListener('submit', function() {
                document.getElementById('pageContent').value = editor.getData();
            });

            editor.model.document.on('change:data', () => {
                document.getElementById('pageContent').value = editor.getData();
            });
        }).catch(err => {
            console.error('CKEditor init error:', err);
        });
        </script>
    @endif
</div>
@endsection