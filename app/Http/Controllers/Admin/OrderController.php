<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items.product')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $oldStatus = $order->status;

        $request->validate([
            'status' => 'required|string|in:pending,paid,preparing,shipped,completed,cancelled,failed',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        // Send Dynamic Notification Emails based on new status
        if ($oldStatus !== $request->status) {
            $data = [
                'user_name' => $order->name,
                'order_id' => $order->id,
                'tracking_code' => $order->tracking_code ?: 'AHS-' . $order->id,
                'total_amount' => number_format($order->total_amount, 2, ',', '.'),
                'delivery_address' => $order->address . ' (' . ($order->city ?: 'Manisa') . ')',
            ];

            try {
                if ($request->status === 'shipped') {
                    \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_shipped', $data));
                } elseif ($request->status === 'completed') {
                    \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_completed', $data));
                } elseif (in_array($request->status, ['cancelled', 'failed'])) {
                    \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_cancelled', $data));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Status Change Email Error: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Sipariş durumu #' . $order->id . ' başarıyla güncellendi ve bilgilendirme e-postası gönderildi.');
    }
}
