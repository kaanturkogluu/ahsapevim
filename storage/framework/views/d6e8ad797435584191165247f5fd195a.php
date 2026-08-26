<?php $__env->startSection('title', 'Gelir Tablosu & İstatistikler'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800"><i class="fa-solid fa-chart-line text-[#C87A53] mr-2"></i>Gelir Tablosu & İstatistikler</h1>
    <p class="text-xs text-gray-500 mt-1">Siparişlerinizden elde edilen ciro ve satış istatistiklerini anlık olarak takip edin.</p>
</div>

<!-- Özet Kartları -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-turkish-lira-sign"></i>
        </div>
        <div>
            <div class="text-xs text-gray-500 font-medium">Toplam Ciro</div>
            <div class="text-2xl font-black text-emerald-600"><?php echo e(number_format($totalRevenue, 2, ',', '.')); ?> ₺</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div>
            <div class="text-xs text-gray-500 font-medium">Bu Ayki Gelir</div>
            <div class="text-2xl font-black text-blue-600"><?php echo e(number_format($thisMonthRevenue, 2, ',', '.')); ?> ₺</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-orange-100 text-[#C87A53] flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-bag-shopping"></i>
        </div>
        <div>
            <div class="text-xs text-gray-500 font-medium">Toplam Sipariş</div>
            <div class="text-2xl font-black text-[#C87A53]"><?php echo e(number_format($totalOrders)); ?></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-box"></i>
        </div>
        <div>
            <div class="text-xs text-gray-500 font-medium">Satılan Ürün</div>
            <div class="text-2xl font-black text-purple-600"><?php echo e(number_format($totalProductsSold)); ?></div>
        </div>
    </div>
</div>

<!-- Gelir Grafiği (Son 30 Gün) -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <h2 class="text-base font-bold text-gray-800"><i class="fa-solid fa-chart-area text-[#C87A53] mr-1.5"></i>Son 30 Günlük Gelir Grafiği</h2>
            <p class="text-xs text-gray-500 mt-0.5">İptal edilen siparişler hariç tutulmaktadır.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span class="w-3 h-3 rounded-full bg-[#C87A53] inline-block"></span>
            <span class="text-gray-600 font-medium">Günlük Ciro (₺)</span>
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
                    <th scope="col" class="px-6 py-4 text-right">Günlük Ciro</th>
                    <th scope="col" class="px-6 py-4 text-right">Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = array_reverse(range(0, 29)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $idx = 29 - $i;
                        $label = $last30Days[$i];
                        $amount = $revenueData[$i];
                    ?>
                    <tr class="bg-white border-b border-gray-50 hover:bg-orange-50/20 transition">
                        <td class="px-6 py-3 text-gray-600 text-xs font-medium"><?php echo e($label); ?></td>
                        <td class="px-6 py-3 text-right font-black <?php echo e($amount > 0 ? 'text-emerald-600' : 'text-gray-300'); ?>">
                            <?php echo e(number_format($amount, 2, ',', '.')); ?> ₺
                        </td>
                        <td class="px-6 py-3 text-right">
                            <?php if($amount > 0): ?>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full">
                                    <i class="fa-solid fa-circle-check"></i> Satış Var
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 bg-gray-100 text-gray-400 rounded-full">
                                    <i class="fa-solid fa-circle-minus"></i> Satış Yok
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js CDN & Init Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('revenueChart').getContext('2d');

    const labels = <?php echo json_encode($last30Days, 15, 512) ?>;
    const data   = <?php echo json_encode($revenueData, 15, 512) ?>;

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u111121823/domains/ahsapevimmanisa.com/public_html/resources/views/admin/revenue/index.blade.php ENDPATH**/ ?>