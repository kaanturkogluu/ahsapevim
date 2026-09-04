<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kargo & Teslimat Etiketi — AhşapEvim</title>
    <!-- Tailwind CSS CDN for screen layout -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- JsBarcode CDN for crisp barcodes -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Libre+Barcode+128&display=swap');

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: #111827;
            background-color: #f3f4f6;
        }

        /* Print Specific Styles */
        @media print {
            @page {
                size: auto;
                margin: 4mm;
            }

            body {
                background-color: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .page-break {
                page-break-after: always;
                break-after: page;
            }

            .thermal-mode {
                width: 100mm !important;
                max-width: 100mm !important;
                min-height: 145mm !important;
                border: 2px solid #000 !important;
                margin: 0 auto !important;
            }
        }

        /* Screen Preview Styles */
        .shipping-label {
            background: #ffffff;
            color: #000000;
            border: 2px solid #000000;
            position: relative;
        }

        .barcode-svg {
            max-width: 100%;
            height: auto;
        }

        /* Thermal 100x150 mm ratio */
        .label-thermal {
            width: 100mm;
            min-height: 145mm;
            margin-left: auto;
            margin-right: auto;
        }

        /* A4 Full Sheet */
        .label-a4 {
            width: 100%;
            max-width: 210mm;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body class="min-h-screen py-4 md:py-8">

    @php
        $orderList = isset($orders) ? $orders : (isset($order) ? collect([$order]) : collect());
        $siteTitle = \App\Models\Setting::get('site_title', 'Ahşap Evim Manisa');
        $senderPhone = \App\Models\Setting::get('contact_phone', '0850 307 49 17');
        $senderAddress = \App\Models\Setting::get('contact_address', 'Şehzadeler Mevkii, Merkez, Manisa');
    @endphp

    <!-- Top Action Toolbar (Hidden in Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 px-4">
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-orange-100 text-[#C87A53] flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-barcode"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-gray-800">Kargo & Teslimat Barkod Etiketi</h1>
                    <p class="text-xs text-gray-500">
                        Toplam <strong>{{ $orderList->count() }}</strong> sipariş için etiket hazırlandı.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Layout Toggle -->
                <div class="inline-flex rounded-lg border border-gray-300 p-0.5 bg-gray-100 text-xs font-semibold">
                    <button type="button" onclick="setLayoutMode('thermal')" id="btnThermal" class="px-3 py-1.5 rounded-md bg-white text-gray-800 shadow-sm transition">
                        <i class="fa-solid fa-receipt mr-1"></i> Termal Etiket (100x150)
                    </button>
                    <button type="button" onclick="setLayoutMode('a4')" id="btnA4" class="px-3 py-1.5 rounded-md text-gray-600 hover:text-gray-900 transition">
                        <i class="fa-solid fa-file-lines mr-1"></i> A4 Paketleme Fişi
                    </button>
                </div>

                <!-- Print Button -->
                <button type="button" onclick="window.print()" class="bg-[#C87A53] hover:bg-[#A65F38] text-white px-5 py-2 rounded-lg font-bold text-xs shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i> Yazdır (CTRL+P)
                </button>

                <!-- Close Button -->
                <button type="button" onclick="window.close()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3.5 py-2 rounded-lg font-bold text-xs transition">
                    <i class="fa-solid fa-xmark"></i> Kapat
                </button>
            </div>
        </div>

        <!-- Quick Help Note -->
        <div class="mt-2 text-center text-xs text-gray-500">
            <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i>
            Yazıcı penceresinde <strong>"Üstbilgi ve altbilgiler" (Headers & Footers)</strong> seçeneğini kapatmanız, etiketin temiz çıkmasını sağlar.
        </div>
    </div>

    <!-- Printable Area -->
    <div id="labelsContainer" class="print-container max-w-4xl mx-auto px-2">
        @forelse($orderList as $index => $ord)
            <div class="shipping-label label-thermal p-4 sm:p-5 mb-6 rounded-lg bg-white relative {{ !$loop->last ? 'page-break' : '' }}" id="label-{{ $ord->id }}">
                
                <!-- 1. Header: Gönderici & Kargo Firması -->
                <div class="border-b-2 border-black pb-3 mb-3 flex justify-between items-start">
                    <div class="w-7/12 pr-2">
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm font-black tracking-tight uppercase text-black">AHŞAPEVİM</span>
                            <span class="text-[9px] bg-black text-white px-1.5 py-0.5 rounded font-bold uppercase">GÖNDERİCİ</span>
                        </div>
                        <div class="text-[10px] leading-tight text-gray-800 mt-1">
                            <p class="font-bold">{{ $siteTitle }}</p>
                            <p class="truncate">{{ $senderAddress }}</p>
                            <p><strong>Tel:</strong> {{ $senderPhone }}</p>
                        </div>
                    </div>

                    <div class="w-5/12 text-right border-l-2 border-black pl-2">
                        <div class="text-[10px] font-bold text-gray-600 uppercase">Kargo Firması</div>
                        <div class="text-sm font-black uppercase tracking-wide text-black truncate">
                            {{ $ord->shippingCompany?->name ?: 'Yurtiçi / Aras Kargo' }}
                        </div>
                        <div class="text-[9px] font-semibold text-gray-600 mt-0.5">
                            Tarih: {{ $ord->created_at?->format('d.m.Y H:i') ?? date('d.m.Y') }}
                        </div>
                    </div>
                </div>

                <!-- 2. Sipariş Barkod Alanı (CODE128) -->
                <div class="border-b-2 border-black pb-3 mb-3 text-center bg-gray-50/50 p-2 rounded">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-gray-600 mb-1 flex justify-between items-center px-1">
                        <span>Sipariş No: <strong>#{{ $ord->id }}</strong></span>
                        <span>Takip Kodu: <strong>{{ $ord->tracking_code ?: 'AHS-' . $ord->id }}</strong></span>
                    </div>

                    <!-- SVG Barcode for Order Tracking Code -->
                    <div class="flex justify-center my-1">
                        <svg class="barcode-svg" id="barcode-{{ $ord->id }}"
                             data-barcode="{{ $ord->tracking_code ?: 'AHS-' . $ord->id }}"></svg>
                    </div>

                    @if(!empty($ord->cargo_tracking_code))
                        <div class="mt-2 pt-2 border-t border-dashed border-gray-400">
                            <div class="text-[9px] font-extrabold uppercase text-blue-900 mb-0.5">
                                Kargo Takip No: {{ $ord->cargo_tracking_code }}
                            </div>
                            <div class="flex justify-center">
                                <svg class="barcode-svg" id="cargo-barcode-{{ $ord->id }}"
                                     data-barcode="{{ $ord->cargo_tracking_code }}"></svg>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 3. Alıcı / Teslimat Bilgileri (Büyük & Çok Net) -->
                <div class="border-2 border-black p-3 mb-3 bg-white rounded">
                    <div class="flex justify-between items-center border-b border-black pb-1.5 mb-2">
                        <span class="text-xs font-black bg-black text-white px-2 py-0.5 uppercase tracking-wider">
                            ALICI (TESLİMAT BİLGİLERİ)
                        </span>
                        @if($ord->identity_number)
                            <span class="text-[10px] font-mono font-bold text-gray-700">TC: {{ $ord->identity_number }}</span>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <!-- İsim Soyisim -->
                        <div class="text-base font-extrabold text-black uppercase tracking-tight">
                            {{ $ord->name }}
                        </div>

                        <!-- Telefon Numarası -->
                        <div class="text-sm font-black text-black font-mono tracking-wider">
                            <i class="fa-solid fa-phone text-xs mr-1 text-black"></i> {{ $ord->phone }}
                        </div>

                        <!-- Adres -->
                        <div class="text-xs font-medium text-black leading-snug pt-1 border-t border-gray-300 mt-1 whitespace-pre-line">
                            {{ $ord->address }}
                        </div>

                        <!-- İlçe / İl Vurgusu -->
                        <div class="pt-2">
                            <div class="inline-block bg-black text-white px-3 py-1 text-xs font-black uppercase tracking-wider rounded">
                                {{ $ord->district ?: 'MERKEZ' }} / {{ $ord->city ?: 'MANİSA' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Sipariş Özeti & Paket İçi İçerik -->
                <div class="border-b-2 border-black pb-2 mb-2">
                    <div class="text-[10px] font-black uppercase text-gray-700 mb-1 flex justify-between">
                        <span>Paket İçeriği ({{ $ord->items->sum('quantity') }} Adet)</span>
                        <span>Ödeme: <strong>ÖDENDİ (Kredi Kartı)</strong></span>
                    </div>

                    <table class="w-full text-[10px] text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-300 text-gray-500">
                                <th class="py-0.5 font-bold">Ürün</th>
                                <th class="py-0.5 text-center w-12 font-bold">Adet</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($ord->items as $item)
                                @php
                                    $features = is_array($item->features) ? $item->features : (json_decode($item->features, true) ?: []);
                                    $isDouble = !empty($features['front_image']) && !empty($features['back_image']);
                                    $isSingle = !empty($features['front_image']) || !empty($features['custom_image']);
                                    $giftNote = $features['gift_note'] ?? null;
                                @endphp
                                <tr>
                                    <td class="py-1 pr-1 font-semibold text-gray-900 leading-tight">
                                        {{ $item->product ? $item->product->name : 'Ahşap Özel Ürün' }}
                                        @if($isDouble)
                                            <span class="block text-[9px] text-emerald-800 font-bold">✓ Çift Yüzlü Kişiselleştirme</span>
                                        @elseif($isSingle)
                                            <span class="block text-[9px] text-orange-800 font-bold">✓ Tek Yüzlü Kişiselleştirme</span>
                                        @endif
                                        @if($giftNote)
                                            <span class="block text-[9px] text-rose-700 font-bold">🎁 Hediye Notu: {{ $giftNote }}</span>
                                        @endif
                                    </td>
                                    <td class="py-1 text-center font-bold text-black align-top">
                                        {{ $item->quantity }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- 5. Varsa Müşteri Notu -->
                @if(!empty($ord->note))
                    <div class="p-2 bg-amber-50 border border-amber-300 rounded text-[10px] text-amber-900 mb-2">
                        <strong>Müşteri Notu:</strong> {{ $ord->note }}
                    </div>
                @endif

                <!-- 6. Alt Bilgi / Teşekkür -->
                <div class="text-center pt-1 text-[9px] font-semibold text-gray-600 flex justify-between items-center">
                    <span>AhşapEvim — Bizi tercih ettiğiniz için teşekkür ederiz!</span>
                    <span class="font-mono">#{{ $ord->id }}</span>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-xl shadow p-6">
                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-3xl mb-2"></i>
                <p class="text-sm font-bold text-gray-700">Yazdırılacak sipariş bilgisi bulunamadı.</p>
            </div>
        @endforelse
    </div>

    <!-- Barcode Initialization Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Render barcodes for all SVG elements
            document.querySelectorAll('svg[data-barcode]').forEach(function(svg) {
                const code = svg.getAttribute('data-barcode');
                if (code && typeof JsBarcode !== 'undefined') {
                    try {
                        JsBarcode(svg, code, {
                            format: "CODE128",
                            lineColor: "#000000",
                            width: 2,
                            height: 42,
                            displayValue: true,
                            fontSize: 11,
                            font: "Inter, sans-serif",
                            textMargin: 2,
                            margin: 0
                        });
                    } catch (e) {
                        console.error('Barcode error for ' + code + ':', e);
                        // Fallback text if barcode format throws
                        svg.outerHTML = '<div class="font-mono font-black text-sm tracking-wider py-1 border border-black">' + code + '</div>';
                    }
                }
            });

            // If auto print parameter is present
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('auto_print') === '1') {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        });

        // Layout switcher between thermal (100x150mm) and A4 packing slip
        function setLayoutMode(mode) {
            const labels = document.querySelectorAll('.shipping-label');
            const btnThermal = document.getElementById('btnThermal');
            const btnA4 = document.getElementById('btnA4');

            if (mode === 'thermal') {
                labels.forEach(l => {
                    l.classList.remove('label-a4');
                    l.classList.add('label-thermal');
                });
                btnThermal.className = "px-3 py-1.5 rounded-md bg-white text-gray-800 shadow-sm transition";
                btnA4.className = "px-3 py-1.5 rounded-md text-gray-600 hover:text-gray-900 transition";
            } else {
                labels.forEach(l => {
                    l.classList.remove('label-thermal');
                    l.classList.add('label-a4');
                });
                btnA4.className = "px-3 py-1.5 rounded-md bg-white text-gray-800 shadow-sm transition";
                btnThermal.className = "px-3 py-1.5 rounded-md text-gray-600 hover:text-gray-900 transition";
            }
        }
    </script>
</body>
</html>
