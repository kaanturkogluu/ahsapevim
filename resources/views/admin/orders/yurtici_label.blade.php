<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yurtiçi Kargo Etiketi — Sipariş #{{ $order->id }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- JsBarcode CDN -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: #000000;
            background-color: #f3f4f6;
        }

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

            .thermal-mode {
                width: 100mm !important;
                max-width: 100mm !important;
                min-height: 145mm !important;
                border: 2px solid #000 !important;
                margin: 0 auto !important;
            }
        }

        .thermal-label {
            width: 100mm;
            min-height: 145mm;
            background: #ffffff;
            border: 2px solid #000000;
            margin: 0 auto;
        }
    </style>
</head>
<body class="min-h-screen py-4 md:py-8">

    @php
        $yurticiService = app(\App\Services\YurticiKargoService::class);
        $paymentType = strtoupper($order->yurtici_payment_type ?: 'GO');
        $paymentLabel = ($paymentType === 'AO') ? 'AÖ — ALICI ÖDEMELİ' : 'GÖ — GÖNDERİCİ ÖDEMELİ';
        // Primary barcode for branch scanning is cargoKey
        $barcodeValue = $order->yurtici_cargo_key ?: $order->cargo_tracking_code ?: ('AHS-' . $order->id);
        $cleanAddress = $yurticiService->formatReceiverAddress($order->address, $order->city ?: 'Manisa', $order->district ?: 'Merkez');
        $cleanPhone = $yurticiService->cleanPhoneNumber($order->phone);
        $formattedPhone = strlen($cleanPhone) === 10
            ? ('0 (' . substr($cleanPhone, 0, 3) . ') ' . substr($cleanPhone, 3, 3) . ' ' . substr($cleanPhone, 6, 2) . ' ' . substr($cleanPhone, 8, 2))
            : ($order->phone ?: '-');

        $respData = json_decode($order->yurtici_response_data ?? '{}', true) ?: [];
        $desi = $respData['desi'] ?? 1.0;
        $kg = $respData['kg'] ?? 1.0;
        $cargoCount = $respData['cargoCount'] ?? 1;
    @endphp

    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print max-w-xl mx-auto mb-6 px-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-gray-800">Yurtiçi Kargo Barkodlu Sevk Etiketi</span>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="py-2 px-4 bg-[#ED1C24] hover:bg-[#C81016] text-white text-xs font-black rounded-lg shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Etiketi Yazdır
                </button>
                <button onclick="window.close()" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition">
                    Kapat
                </button>
            </div>
        </div>
    </div>

    <!-- Printable Thermal Container -->
    <div class="print-container max-w-xl mx-auto px-4">
        <div class="thermal-label p-4 flex flex-col justify-between rounded-lg shadow-sm">
            
            <!-- Label Header -->
            <div>
                <div class="flex items-center justify-between border-b-2 border-black pb-2 mb-2">
                    <div class="flex items-center gap-2">
                        <div class="bg-[#ED1C24] text-white font-black px-2 py-1 text-xs tracking-tight rounded">
                            YURTİÇİ KARGO
                        </div>
                        <span class="text-[10px] font-black tracking-wider uppercase text-gray-800">Standart Giden Kargo</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] font-bold block text-gray-600">Sipariş No</span>
                        <span class="text-xs font-black font-mono">#{{ $order->id }}</span>
                    </div>
                </div>

                <!-- Payment Type Badge & Branch Info -->
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div class="border border-black p-1.5 rounded bg-gray-50 text-center">
                        <span class="text-[9px] font-bold block text-gray-600 uppercase">Ödeme Tipi</span>
                        <span class="text-xs font-black tracking-wide {{ $paymentType === 'AO' ? 'text-blue-700' : 'text-emerald-700' }}">
                            {{ $paymentLabel }}
                        </span>
                    </div>
                    <div class="border border-black p-1.5 rounded bg-gray-50 text-center">
                        <span class="text-[9px] font-bold block text-gray-600 uppercase">Çıkış Birimi</span>
                        <span class="text-xs font-black text-gray-900">
                            3150 — SPİL ŞUBE
                        </span>
                    </div>
                </div>

                <!-- Barcode Section -->
                <div class="border-2 border-black p-2 rounded text-center mb-3 bg-white">
                    <span class="text-[9px] font-black uppercase text-gray-700 tracking-wider block mb-1">
                        Kargo Takip / Sevk Numarası (Kargo Anahtarı)
                    </span>
                    <svg id="yurticiBarcode" class="w-full max-h-16 mx-auto"></svg>
                    <div class="text-xs font-mono font-black tracking-widest mt-0.5">{{ $barcodeValue }}</div>
                    @if($order->yurtici_job_id)
                        <span class="text-[9px] font-mono text-gray-500 block mt-0.5">Yurtiçi Talep No (Job ID): #{{ $order->yurtici_job_id }}</span>
                    @endif
                </div>

                <!-- Recipient (Alıcı) Box -->
                <div class="border-2 border-black p-2.5 rounded mb-3 bg-white">
                    <div class="flex items-center justify-between border-b border-gray-300 pb-1 mb-1.5">
                        <span class="text-[10px] font-black uppercase tracking-wider text-[#ED1C24] flex items-center gap-1">
                            <i class="fa-solid fa-user"></i> Alıcı (Teslim Edilecek Kişi)
                        </span>
                        <span class="text-[10px] font-black font-mono">{{ $formattedPhone }}</span>
                    </div>
                    <div class="text-xs font-black text-gray-900 mb-1">
                        {{ $order->name }}
                    </div>
                    <div class="text-[11px] text-gray-800 leading-tight mb-2 whitespace-pre-line">
                        {{ $cleanAddress }}
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-gray-200 text-xs">
                        <span class="font-black uppercase tracking-wide">
                            {{ $order->district ?: 'Merkez' }} / {{ $order->city ?: 'MANİSA' }}
                        </span>
                        @if($order->identity_number)
                            <span class="text-[10px] font-mono text-gray-600">TC/VKN: {{ $order->identity_number }}</span>
                        @endif
                    </div>
                </div>

                <!-- Sender (Gönderici) Box -->
                <div class="border border-black p-2 rounded mb-3 bg-gray-50 text-[10px] text-gray-800 leading-tight">
                    <div class="font-black uppercase tracking-wider text-gray-900 mb-0.5 flex items-center justify-between">
                        <span>Gönderici: METE ALMAZ (Ahşap Evim)</span>
                        <span class="font-mono">Müşteri No: 178821492</span>
                    </div>
                    <div>Şehzadeler Mevkii, SPİL Şube Çıkışlı / MANİSA</div>
                </div>
            </div>

            <!-- Footer: Package Specs & Date -->
            <div class="border-t-2 border-black pt-2 text-[10px]">
                <div class="grid grid-cols-4 gap-1 text-center font-bold">
                    <div class="bg-gray-100 p-1 rounded border border-gray-300">
                        <span class="text-[8px] text-gray-500 block uppercase">Desi</span>
                        <span class="font-mono font-black">{{ $desi }}</span>
                    </div>
                    <div class="bg-gray-100 p-1 rounded border border-gray-300">
                        <span class="text-[8px] text-gray-500 block uppercase">Kg</span>
                        <span class="font-mono font-black">{{ $kg }}</span>
                    </div>
                    <div class="bg-gray-100 p-1 rounded border border-gray-300">
                        <span class="text-[8px] text-gray-500 block uppercase">Paket</span>
                        <span class="font-mono font-black">{{ $cargoCount }}</span>
                    </div>
                    <div class="bg-gray-100 p-1 rounded border border-gray-300">
                        <span class="text-[8px] text-gray-500 block uppercase">Tarih</span>
                        <span class="font-mono font-black">{{ now()->format('d.m.Y') }}</span>
                    </div>
                </div>
                <div class="mt-2 text-center text-[8px] text-gray-500 uppercase tracking-wider">
                    AhşapEvim E-Ticaret Otomasyonu — Yurtiçi Kargo API Entegrasyonu
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            try {
                JsBarcode("#yurticiBarcode", "{{ $barcodeValue }}", {
                    format: "CODE128",
                    lineColor: "#000000",
                    width: 2.2,
                    height: 50,
                    displayValue: false,
                    margin: 0
                });
            } catch (e) {
                console.error("Barkod oluşturulamadı:", e);
            }
        });
    </script>
</body>
</html>
