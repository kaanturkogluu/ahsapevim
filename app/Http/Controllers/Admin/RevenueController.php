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
    private array $paidStatuses = ['paid', 'preparing', 'shipped', 'completed', 'delivered'];

    public function index(Request $request)
    {
        // Temel metrikler – sadece ödemesi alınmış siparişler (Net Ciro)
        $totalRevenue = (float) Order::whereIn('status', $this->paidStatuses)->sum('total_amount');

        $thisMonthRevenue = (float) Order::whereIn('status', $this->paidStatuses)
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->sum('total_amount');

        $totalOrders = Order::whereIn('status', $this->paidStatuses)->count();

        $totalProductsSold = (int) OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', $this->paidStatuses)
            ->sum('order_items.quantity');

        // İndirim ve Brüt Satış Hesaplamaları (Gelir Tablosu Doğrulama ve Denetimi)
        $paidItems = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', $this->paidStatuses)
            ->whereNotNull('order_items.features')
            ->select('order_items.quantity', 'order_items.features', 'orders.created_at as order_created_at')
            ->get();

        $totalDiscount = 0.0;
        $thisMonthDiscount = 0.0;
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        foreach ($paidItems as $item) {
            $features = is_array($item->features) ? $item->features : json_decode($item->features ?? '[]', true);
            if (!empty($features['cart_discount_amount'])) {
                $discountAmount = (float) $features['cart_discount_amount'] * (int) $item->quantity;
                $totalDiscount += $discountAmount;

                $orderDate = Carbon::parse($item->order_created_at);
                if ($orderDate->month === $currentMonth && $orderDate->year === $currentYear) {
                    $thisMonthDiscount += $discountAmount;
                }
            }
        }

        $totalDiscount = round($totalDiscount, 2);
        $thisMonthDiscount = round($thisMonthDiscount, 2);
        $totalGrossRevenue = round($totalRevenue + $totalDiscount, 2);
        $thisMonthGrossRevenue = round($thisMonthRevenue + $thisMonthDiscount, 2);

        // Son indirimli sipariş kayıtları (Denetim tablosu için)
        $discountedOrders = Order::with(['items.product'])
            ->whereIn('status', $this->paidStatuses)
            ->whereHas('items', function ($q) {
                $q->where('features', 'like', '%cart_discount%');
            })
            ->latest()
            ->take(20)
            ->get();

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
            'totalGrossRevenue',
            'thisMonthGrossRevenue',
            'totalDiscount',
            'thisMonthDiscount',
            'totalOrders',
            'totalProductsSold',
            'discountedOrders',
            'last30Days',
            'revenueData'
        ));
    }
}
