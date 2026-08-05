<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class RevenueController extends Controller
{
    /**
     * Ödeme alınmış sipariş durumları:
     * - 'paid'       : İyzico kart ödemesi başarılı
     * - 'preparing'  : Siparişe alındı
     * - 'shipped'    : Kargoya verildi
     * - 'completed'  : Teslim edildi / tamamlandı
     * 'pending' (EFT bekliyor), 'failed', 'cancelled' dahil edilmez.
     */
    private array $paidStatuses = ['paid', 'preparing', 'shipped', 'completed'];

    public function index(Request $request)
    {
        // Temel metrikler – sadece ödemesi alınmış siparişler
        $totalRevenue = Order::whereIn('status', $this->paidStatuses)->sum('total_amount');

        $thisMonthRevenue = Order::whereIn('status', $this->paidStatuses)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->sum('total_amount');

        $totalOrders = Order::whereIn('status', $this->paidStatuses)->count();

        $totalProductsSold = OrderItem::whereHas('order', function ($q) {
            $q->whereIn('status', $this->paidStatuses);
        })->sum('quantity');

        // Son 30 günlük günlük gelir verisi (grafik)
        $last30Days   = [];
        $revenueData  = [];

        for ($i = 29; $i >= 0; $i--) {
            $date          = Carbon::now()->subDays($i);
            $last30Days[]  = $date->format('d M');

            $dayRevenue = Order::whereIn('status', $this->paidStatuses)
                               ->whereDate('created_at', $date->toDateString())
                               ->sum('total_amount');

            $revenueData[] = (float) $dayRevenue;
        }

        return view('admin.revenue.index', compact(
            'totalRevenue',
            'thisMonthRevenue',
            'totalOrders',
            'totalProductsSold',
            'last30Days',
            'revenueData'
        ));
    }
}
