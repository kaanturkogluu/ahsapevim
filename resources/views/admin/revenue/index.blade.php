@extends('layouts.admin')

@section('title', 'Gelir Tablosu & İstatistikler')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><i class="fa-solid fa-chart-line text-[#C87A53] mr-2"></i>Gelir Tablosu & İstatistikler</h1>
    <p class="text-xs text-gray-500 mt-1">Siparişlerinizden elde edilen ciro ve satış istatistiklerini anlık olarak takip edin.</p>
</div>

<!-- Özet Kartları -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Net Ciro (Kasa)</span>
            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-turkish-lira-sign"></i>
            </div>
        </div>
        <div>
            <div class="text-xl font-black text-emerald-600">{{ number_format($totalRevenue, 2, ',', '.') }} ₺</div>
            <p class="text-[10px] text-gray-400 mt-0.5">İndirimler düşülmüş net tahsilat</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Verilen İndirim</span>
            <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-tags"></i>
            </div>
        </div>
        <div>
            <div class="text-xl font-black text-rose-600">{{ number_format($totalDiscount, 2, ',', '.') }} ₺</div>
            <p class="text-[10px] text-gray-400 mt-0.5">Sepette uygulanan indirim toplamı</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Brüt Satış (Liste)</span>
            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-calculator"></i>
            </div>
        </div>
        <div>
            <div class="text-xl font-black text-amber-700">{{ number_format($totalGrossRevenue, 2, ',', '.') }} ₺</div>
            <p class="text-[10px] text-gray-400 mt-0.5">İndirimsiz liste fiyatı değeri</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Bu Ayki Ciro</span>
            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
        <div>
            <div class="text-xl font-black text-blue-600">{{ number_format($thisMonthRevenue, 2, ',', '.') }} ₺</div>
            <p class="text-[10px] text-gray-400 mt-0.5">Bu ayki net tahsilat</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Toplam Sipariş</span>
            <div class="w-8 h-8 rounded-full bg-orange-100 text-[#C87A53] flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
        </div>
        <div>
            <div class="text-xl font-black text-[#C87A53]">{{ number_format($totalOrders) }}</div>
            <p class="text-[10px] text-gray-400 mt-0.5">Ödemesi onaylanan siparişler</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Satılan Ürün</span>
            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>
        <div>
            <div class="text-xl font-black text-purple-600">{{ number_format($totalProductsSold) }}</div>
            <p class="text-[10px] text-gray-400 mt-0.5">Toplam satılan adet</p>
        </div>
    </div>
</div>

<!-- Veri Bütünlüğü & Güvence Bilgilendirme Kutusu -->
<div class="bg-gradient-to-r from-emerald-50 via-white to-orange-50 border border-emerald-200/80 rounded-2xl p-4 sm:p-5 mb-8 shadow-xs">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-base shrink-0 shadow-xs">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <h4 class="text-xs font-black text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                    İndirimli Satış & Gelir Kayıt Güvencesi
                    <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-300">
                        <i class="fa-solid fa-check-double mr-1"></i>Doğrulandı
                    </span>
                </h4>
                <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                    Sistemimizde yapılan tüm indirimli siparişlerde tahsil edilen net tutar (<strong>total_amount</strong>), sipariş kalemlerindeki indirimli birim fiyatlar (<strong>price</strong>) ve uygulanan indirim oranı (<strong>cart_discount_rate</strong>) veritabanında şeffaf bir şekilde tutulmaktadır. Gelir tablosuna ve banka çekimlerine (İyzico / Havale) kesinlikle şişirilmiş liste fiyatı değil, <strong>müşteriden fiilen tahsil edilen net ciro</strong> kaydedilir.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0 text-xs">
            <span class="px-3 py-1.5 bg-white border border-gray-200 rounded-xl text-gray-700 font-bold shadow-2xs">
                Formül: Brüt = Net + İndirim
            </span>
        </div>
    </div>
</div>

<!-- Son İndirimli Satışlar & Gelir Denetimi Tablosu (Varsa) -->
@if(isset($discountedOrders) && $discountedOrders->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-gray-800"><i class="fa-solid fa-percent text-rose-500 mr-1.5"></i>Son İndirimli Satışlar & Gelir Kayıt Denetimi</h2>
                <p class="text-xs text-gray-500 mt-0.5">Uygulanan sepet indirimlerinin brüt liste fiyatı, indirim tutarı ve kasaya giren net tutar karşılaştırması.</p>
            </div>
            <span class="text-xs font-bold text-gray-500 bg-white px-2.5 py-1 rounded-lg border border-gray-200">
                Toplam {{ $discountedOrders->count() }} İndirimli Sipariş
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4">Sipariş No & Tarih</th>
                        <th scope="col" class="px-6 py-4">Müşteri</th>
                        <th scope="col" class="px-6 py-4">İndirimli Ürün(ler)</th>
                        <th scope="col" class="px-6 py-4 text-right">Brüt (Liste)</th>
                        <th scope="col" class="px-6 py-4 text-right text-rose-600">İndirim</th>
                        <th scope="col" class="px-6 py-4 text-right text-emerald-600">Net Ciro (Kasa)</th>
                        <th scope="col" class="px-6 py-4 text-center">Ödeme & Denetim</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($discountedOrders as $discOrder)
                        @php
                            $orderGross = 0;
                            $orderDisc = 0;
                            foreach($discOrder->items as $oItem) {
                                $features = is_array($oItem->features) ? $oItem->features : json_decode($oItem->features ?? '[]', true);
                                $unitPrice = (float) $oItem->price;
                                $origPrice = isset($features['original_price']) ? (float) $features['original_price'] : $unitPrice;
                                $unitDisc = isset($features['cart_discount_amount']) ? (float) $features['cart_discount_amount'] : max(0, $origPrice - $unitPrice);
                                $orderGross += ($origPrice * $oItem->quantity);
                                $orderDisc += ($unitDisc * $oItem->quantity);
                            }
                            $isMatched = abs(($orderGross - $orderDisc) - (float)$discOrder->total_amount) < 0.05;
                            $isEft = str_starts_with($discOrder->payment_id ?? '', 'EFT');
                        @endphp
                        <tr class="bg-white hover:bg-orange-50/20 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $discOrder->id) }}" class="font-extrabold text-[#C87A53] hover:underline">
                                    #{{ $discOrder->id }}
                                </a>
                                <span class="block text-[11px] text-gray-400 font-mono mt-0.5">
                                    {{ $discOrder->created_at ? $discOrder->created_at->format('d.m.Y H:i') : '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800 block text-xs">{{ $discOrder->name }}</span>
                                <span class="text-[10px] text-gray-400 block">{{ $discOrder->city }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @foreach($discOrder->items as $oItem)
                                        @php
                                            $features = is_array($oItem->features) ? $oItem->features : json_decode($oItem->features ?? '[]', true);
                                            $rate = $features['cart_discount_rate'] ?? null;
                                        @endphp
                                        <div class="text-xs text-gray-700 flex items-center gap-1.5 flex-wrap">
                                            <span>{{ $oItem->quantity }}x {{ $oItem->product ? $oItem->product->name : 'Ahşap Ürün' }}</span>
                                            @if($rate)
                                                <span class="px-1.5 py-0.2 rounded bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-black">
                                                    %{{ $rate }} İndirim
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-700 text-xs">
                                {{ number_format($orderGross, 2, ',', '.') }} ₺
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-rose-600 text-xs">
                                -{{ number_format($orderDisc, 2, ',', '.') }} ₺
                            </td>
                            <td class="px-6 py-4 text-right font-black text-emerald-600 text-sm">
                                {{ number_format($discOrder->total_amount, 2, ',', '.') }} ₺
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isMatched)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check"></i> Doğrulandı (0 Fark)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Kontrol Edilmeli
                                    </span>
                                @endif
                                <span class="block text-[10px] text-gray-400 mt-1">
                                    {{ $isEft ? 'Havale / EFT' : 'Kredi Kartı' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Gelir Grafiği (Son 30 Gün) -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <h2 class="text-base font-bold text-gray-800"><i class="fa-solid fa-chart-area text-[#C87A53] mr-1.5"></i>Son 30 Günlük Gelir Grafiği</h2>
            <p class="text-xs text-gray-500 mt-0.5">İptal edilen siparişler hariç tutulmaktadır.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="w-3 h-3 rounded-full bg-[#C87A53] inline-block"></span>
            <span class="text-gray-600 font-medium">Günlük Net Ciro (₺)</span>
        </div>
    </div>
    <div style="position:relative; height:320px;">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<!-- Aylık Gelir Özeti Tablosu -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 bg-gray-50/50">
        <h2 class="text-base font-bold text-gray-800"><i class="fa-solid fa-table text-gray-500 mr-1.5"></i>Son 30 Günlük Veri Tablosu</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-4">Tarih</th>
                    <th scope="col" class="px-6 py-4 text-right">Günlük Net Ciro</th>
                    <th scope="col" class="px-6 py-4 text-right">Durum</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_reverse(range(0, 29)) as $i)
                    @php
                        $idx = 29 - $i;
                        $label = $last30Days[$i];
                        $amount = $revenueData[$i];
                    @endphp
                    <tr class="bg-white border-b border-gray-50 hover:bg-orange-50/20 transition">
                        <td class="px-6 py-3 text-gray-600 text-xs font-medium">{{ $label }}</td>
                        <td class="px-6 py-3 text-right font-black {{ $amount > 0 ? 'text-emerald-600' : 'text-gray-300' }}">
                            {{ number_format($amount, 2, ',', '.') }} ₺
                        </td>
                        <td class="px-6 py-3 text-right">
                            @if($amount > 0)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full">
                                    <i class="fa-solid fa-circle-check"></i> Satış Var
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 bg-gray-100 text-gray-400 rounded-full">
                                    <i class="fa-solid fa-circle-minus"></i> Satış Yok
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js CDN & Init Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('revenueChart').getContext('2d');

    const labels = @json($last30Days);
    const data   = @json($revenueData);

    const gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(200, 122, 83, 0.25)');
    gradient.addColorStop(1, 'rgba(200, 122, 83, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Günlük Ciro (₺)',
                data: data,
                borderColor: '#C87A53',
                backgroundColor: gradient,
                borderWidth: 2.5,
                pointBackgroundColor: '#C87A53',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 13 },
                    padding: 12,
                    callbacks: {
                        label: function(ctx) {
                            return ' ' + new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(ctx.raw);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, maxRotation: 45 }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        font: { size: 11 },
                        callback: function(value) {
                            return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY', maximumFractionDigits: 0 }).format(value);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
