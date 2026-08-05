<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShippingCompany;
use App\Services\NetgsmService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'shippingCompany'])->latest();

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
        $order = Order::with(['items.product', 'shippingCompany'])->findOrFail($id);
        $shippingCompanies = ShippingCompany::where('is_active', true)->get();
        return view('admin.orders.show', compact('order', 'shippingCompanies'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $oldTrackingCode = $order->cargo_tracking_code;

        $request->validate([
            'status' => 'required|string|in:pending,paid,preparing,shipped,completed,cancelled,failed',
            'shipping_company_id' => 'nullable|exists:shipping_companies,id',
            'cargo_tracking_code' => 'nullable|string|max:100',
        ]);

        $newStatus = $request->status;
        // If cargo tracking code is provided, automatically mark status as shipped
        if ($request->filled('cargo_tracking_code') && $newStatus !== 'completed' && $newStatus !== 'cancelled') {
            $newStatus = 'shipped';
        }

        $order->update([
            'status' => $newStatus,
            'shipping_company_id' => $request->shipping_company_id ?: $order->shipping_company_id,
            'cargo_tracking_code' => $request->cargo_tracking_code ?: $order->cargo_tracking_code,
        ]);

        // Refresh order model with relationship
        $order->load('shippingCompany');
        $shippingCompanyName = $order->shippingCompany ? $order->shippingCompany->name : 'Kargo Şirketi';
        $cargoCode = $order->cargo_tracking_code ?: 'Belirtilmedi';

        // Check if order was newly shipped or tracking code was newly added
        $isNewlyShipped = ($newStatus === 'shipped' && ($oldStatus !== 'shipped' || $oldTrackingCode !== $order->cargo_tracking_code));

        if ($isNewlyShipped) {
            // 1. Send SMS via Netgsm
            try {
                $smsMessage = "Sayın {$order->name}, Kargonuz {$shippingCompanyName} ile {$cargoCode} takip numarası ile kargolanmıştır. İyi günler dileriz - AhşapEvim";
                app(NetgsmService::class)->sendSms($order->phone, $smsMessage);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Kargo SMS Gönderim Hatası: ' . $e->getMessage());
            }

            // 2. Queue Email Notification
            try {
                $data = [
                    'user_name' => $order->name,
                    'order_id' => $order->id,
                    'tracking_code' => $order->tracking_code ?: 'AHS-' . $order->id,
                    'shipping_company' => $shippingCompanyName,
                    'cargo_tracking_code' => $cargoCode,
                    'total_amount' => number_format($order->total_amount, 2, ',', '.'),
                    'delivery_address' => $order->address . ' (' . ($order->city ?: 'Manisa') . ')',
                ];

                \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_shipped', $data));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Kargo Mail Gönderim Hatası: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', "Sipariş #{$order->id} kargolandı olarak güncellendi. SMS ve E-Posta bilgilendirmesi gönderildi.");
        }

        // Generic Status Change Email Notification
        if ($oldStatus !== $newStatus) {
            $data = [
                'user_name' => $order->name,
                'order_id' => $order->id,
                'tracking_code' => $order->tracking_code ?: 'AHS-' . $order->id,
                'total_amount' => number_format($order->total_amount, 2, ',', '.'),
                'delivery_address' => $order->address . ' (' . ($order->city ?: 'Manisa') . ')',
            ];

            try {
                if ($newStatus === 'completed') {
                    \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_completed', $data));
                } elseif (in_array($newStatus, ['cancelled', 'failed'])) {
                    \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_cancelled', $data));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Status Change Email Error: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Sipariş durumu #' . $order->id . ' başarıyla güncellendi.');
    }

    public function destroy(Request $request, $id)
    {
        $order = Order::with('items')->findOrFail($id);

        // Security Check 1: Only unpaid / failed / cancelled orders can be deleted
        if (!in_array($order->status, ['pending', 'failed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Yalnızca ödemesi alınmayan, başarısız veya iptal edilmiş siparişler silinebilir.');
        }

        // Security Check 2: Confirm Admin Password
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Girdiğiniz admin şifresi hatalı! Sipariş silinmedi.');
        }

        // Delete uploaded customer photos associated with this order from server storage
        $deletedPhotoCount = 0;
        foreach ($order->items as $item) {
            $features = is_array($item->features) ? $item->features : (json_decode($item->features, true) ?: []);
            
            $possibleKeys = ['front_image', 'back_image', 'custom_image', 'custom_preview'];
            foreach ($possibleKeys as $key) {
                if (!empty($features[$key])) {
                    $relPath = parse_url($features[$key], PHP_URL_PATH);
                    if ($relPath) {
                        $fullPath = public_path(ltrim($relPath, '/'));
                        if (\Illuminate\Support\Facades\File::exists($fullPath) && is_file($fullPath)) {
                            \Illuminate\Support\Facades\File::delete($fullPath);
                            $deletedPhotoCount++;
                        }
                    }
                }
            }
        }

        // Delete order items and order
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', "İptal/Başarısız sipariş (#{$id}) ve ilişkili {$deletedPhotoCount} adet müşteri fotoğrafı sistemden kalıcı olarak silindi.");
    }
}
