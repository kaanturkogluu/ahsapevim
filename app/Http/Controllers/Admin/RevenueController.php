<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        // Temel metrikler
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $thisMonthRevenue = Order::where('status', '!=', 'cancelled')
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->sum('total_amount');
        $totalOrders = Order::where('status', '!=', 'cancelled')->count();
        $totalProductsSold = OrderItem::whereHas('order', function($q) {
            $q->where('status', '!=', 'cancelled');
        })->sum('quantity');

        // Grafikler için son 30 günün gelir verisi
        $last30Days = [];
        $revenueData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $last30Days[] = $date->format('d M');
            
            $dayRevenue = Order::where('status', '!=', 'cancelled')
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
