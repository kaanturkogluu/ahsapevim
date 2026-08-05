@extends('pages.layout')

@section('page_content')
<div>
    <!-- FAQ Üst Bilgi Kartı -->
    <div class="bg-gradient-to-r from-orange-50 via-amber-50 to-orange-50 p-6 rounded-2xl border border-orange-100 mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/80 border border-orange-200 text-[#C87A53] rounded-full text-xs font-bold shadow-2xs mb-2">
                <i class="fa-solid fa-circle-question"></i> Yardım Merkezi
            </span>
            <h2 class="text-xl font-extrabold text-gray-800">Sıkça Sorulan Sorular</h2>
            <p class="text-xs text-gray-500 mt-1">Sipariş, kargo, teslimat ve ahşap ürünlerimiz hakkında merak ettiğiniz tüm yanıtlar burada.</p>
        </div>
        <div class="hidden sm:flex items-center justify-center w-12 h-12 rounded-2xl bg-[#C87A53] text-white shrink-0 shadow-md">
            <i class="fa-solid fa-lightbulb text-xl"></i>
        </div>
    </div>

    <!-- SSS Akordiyon Listesi -->
    @if(!empty($faqItems) && is_array($faqItems) && count($faqItems) > 0)
        <div class="space-y-3" id="faqAccordion">
            @foreach($faqItems as $index => $item)
                <div class="faq-item border border-gray-200 rounded-xl overflow-hidden transition-all duration-200 bg-white hover:border-amber-300">
                    <button type="button" 
                            onclick="toggleFaqAccordion({{ $index }})" 
                            class="w-full p-4 text-left flex items-center justify-between gap-4 bg-white hover:bg-orange-50/30 transition cursor-pointer select-none">
                        <span class="font-bold text-gray-800 text-sm md:text-base flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-orange-100 text-[#C87A53] text-xs font-extrabold flex items-center justify-center shrink-0">
                                {{ $index + 1 }}
                            </span>
                            {{ $item['question'] ?? '' }}
                        </span>
                        <span id="faqChevron-{{ $index }}" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center shrink-0 transition-transform duration-300">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </span>
                    </button>
                    <div id="faqAnswer-{{ $index }}" class="hidden px-5 pb-5 pt-1 text-gray-600 text-sm leading-relaxed border-t border-gray-100 bg-gray-50/50">
                        {!! nl2br(e($item['answer'] ?? '')) !!}
                    </div>
                </div>
            @endforeach
        </div>
    @elseif(!empty($rawContent) && is_string($rawContent))
        <!-- Eski Metin Formatı İçin Yedek Görünüm -->
        <div class="prose max-w-none text-gray-600 text-sm">
            {!! $rawContent !!}
        </div>
    @else
        <div class="text-center py-12 text-gray-400">
            <i class="fa-solid fa-circle-info text-3xl mb-2 text-gray-300"></i>
            <p>Henüz eklenmiş soru ve cevap bulunmamaktadır.</p>
        </div>
    @endif

    <!-- Destek / İletişim Çağrısı Kutusu -->
    <div class="mt-10 p-6 rounded-2xl bg-gray-900 text-white flex flex-col md:flex-row items-center justify-between gap-4 shadow-md">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center text-2xl shrink-0">
                <i class="fa-solid fa-headset"></i>
            </div>
            <div>
                <h4 class="font-bold text-base text-white">Farklı bir sorunuz mu var?</h4>
                <p class="text-xs text-gray-400 mt-0.5">Destek ekibimiz size seve seve yardımcı olmaya hazır.</p>
            </div>
        </div>
        <a href="{{ url('/iletisim') }}" class="py-2.5 px-5 bg-[#C87A53] hover:bg-[#A65F38] text-white font-bold rounded-xl text-xs transition shrink-0 flex items-center gap-2">
            <i class="fa-solid fa-paper-plane"></i> Bize Ulaşın
        </a>
    </div>
</div>

<script>
function toggleFaqAccordion(index) {
    const answerEl = document.getElementById(`faqAnswer-${index}`);
    const chevronEl = document.getElementById(`faqChevron-${index}`);
    
    if (!answerEl) return;

    const isHidden = answerEl.classList.contains('hidden');

    // Opsiyonel: Diğer tüm akordiyonları kapatmak isterseniz aşağıdaki döngüyü kullanabilirsiniz.
    // Şimdilik birden fazla soru açık tutulabilir şekilde bırakılmıştır.
    
    if (isHidden) {
        answerEl.classList.remove('hidden');
        if (chevronEl) {
            chevronEl.classList.add('rotate-180', 'bg-orange-100', 'text-[#C87A53]');
            chevronEl.classList.remove('bg-gray-100', 'text-gray-500');
        }
    } else {
        answerEl.classList.add('hidden');
        if (chevronEl) {
            chevronEl.classList.remove('rotate-180', 'bg-orange-100', 'text-[#C87A53]');
            chevronEl.classList.add('bg-gray-100', 'text-gray-500');
        }
    }
}
</script>
@endsection
