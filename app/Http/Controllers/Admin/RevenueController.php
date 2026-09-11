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

        $totalProductsSold = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', $this->paidStatuses)
            ->sum('order_items.quantity');

        // Son 30 günlük günlük gelir verisi (grafik) - Tek sorguda grup olarak çekilir (N+1 engellendi)
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $dailyRevenues = Order::whereIn('status', $this->paidStatuses)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as order_date, SUM(total_amount) as total')
            ->groupBy('order_date')
            ->pluck('total', 'order_date')
            ->toArray();

        $last30Days   = [];
        $revenueData  = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateKey = $date->toDateString();
            $last30Days[]  = $date->format('d M');
            $revenueData[] = (float) ($dailyRevenues[$dateKey] ?? 0);
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
