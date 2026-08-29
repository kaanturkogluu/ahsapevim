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
        $query = Order::with(['items', 'shippingCompany'])->latest();

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
            'payment_error_reason' => 'nullable|string|max:500',
            'send_notification' => 'nullable',
        ]);

        $newStatus = $request->status;
        $hasNewTrackingCode = $request->filled('cargo_tracking_code') && ($oldTrackingCode !== $request->cargo_tracking_code);
        $hasTrackingCode = $request->filled('cargo_tracking_code');

        // Kargo takip kodu girildiyse ve durum henüz 'paid', 'pending' veya 'preparing' ise otomatik olarak 'shipped' (Kargolandı) yap
        if ($hasTrackingCode && in_array($newStatus, ['paid', 'pending', 'preparing'])) {
            $newStatus = 'shipped';
        }

        $orderUpdateData = [
            'status' => $newStatus,
            'shipping_company_id' => $request->shipping_company_id ?: $order->shipping_company_id,
            'cargo_tracking_code' => $request->cargo_tracking_code ?: $order->cargo_tracking_code,
        ];

        if ($request->filled('payment_error_reason')) {
            $orderUpdateData['payment_error_reason'] = $request->payment_error_reason;
        }

        $order->update($orderUpdateData);

        // Refresh order model with relationship
        $order->load(['shippingCompany', 'items.product']);
        $shippingCompanyName = $order->shippingCompany ? $order->shippingCompany->name : 'Kargo Firması';
        $cargoCode = $order->cargo_tracking_code ?: 'Belirtilmedi';

        $shouldNotify = $request->boolean('send_notification', true);

        if ($shouldNotify) {
            $data = [
                'user_name'           => $order->name,
                'user_email'          => $order->email,
                'order_id'            => $order->id,
                'tracking_code'       => $order->tracking_code ?: 'AHS-' . $order->id,
                'shipping_company'    => $shippingCompanyName,
                'cargo_tracking_code' => $cargoCode,
                'cancellation_reason' => $order->payment_error_reason ?: 'Müşteri talebi / sipariş iptali',
                'total_amount'        => number_format($order->total_amount, 2, ',', '.'),
                'delivery_address'    => $order->address . ' (' . ($order->city ?: 'Manisa') . ')',
                'product_details'     => $this->formatOrderItemsHtml($order),
                'site_name'           => 'AhşapEvim',
            ];

            // 1. Kargoya Verildi Durumu (Yeni kargo kodu girildiğinde veya durum 'shipped' olduğunda)
            $isShippedEvent = ($newStatus === 'shipped' && ($oldStatus !== 'shipped' || $hasNewTrackingCode));

            if ($isShippedEvent) {
                // SMS Gönderimi (Netgsm)
                try {
                    $smsMessage = "Sayın {$order->name}, #{$order->id} numaralı siparişiniz {$shippingCompanyName} firmasına teslim edilmiştir. Kargo Takip No: {$cargoCode}. AhşapEvim";
                    app(NetgsmService::class)->sendSms($order->phone, $smsMessage, $order->id, 'automated');
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Kargo SMS Gönderim Hatası: ' . $e->getMessage());
                }

                // E-Posta Bildirimi (DynamicMail order_shipped)
                try {
                    \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_shipped', $data));
                    app(\App\Services\MailService::class)->logMailable($order->email, "Siparişiniz Kargoya Verildi (#{$order->id})", "Kargonuz {$shippingCompanyName} ile {$cargoCode} takip numarasıyla teslim edilmiştir.", 'success', null, $order->id);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Kargo Mail Gönderim Hatası: ' . $e->getMessage());
                    app(\App\Services\MailService::class)->logMailable($order->email, "Siparişiniz Kargoya Verildi (#{$order->id})", "Kargo bilgilendirme e-postası", 'failed', $e->getMessage(), $order->id);
                }
            } elseif ($oldStatus !== $newStatus) {
                // 2. Diğer Durum Değişiklikleri Bildirimi
                try {
                    if ($newStatus === 'completed') {
                        // Tamamlandı E-Posta
                        \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_completed', $data));
                        app(\App\Services\MailService::class)->logMailable($order->email, "Siparişiniz Teslim Edildi (#{$order->id})", "Sipariş teslim edildi ve tamamlandı olarak işaretlendi.", 'success', null, $order->id);

                        // Tamamlandı SMS
                        try {
                            $completedSms = "Sayın {$order->name}, #{$order->id} numaralı siparişiniz teslim edilmiştir. AhşapEvim'i tercih ettiğiniz için teşekkür ederiz.";
                            app(NetgsmService::class)->sendSms($order->phone, $completedSms, $order->id, 'automated');
                        } catch (\Throwable $smsEx) {
                            \Illuminate\Support\Facades\Log::error('Teslim SMS Gönderim Hatası: ' . $smsEx->getMessage());
                        }
                    } elseif ($newStatus === 'cancelled') {
                        // İptal E-Postası Gönder
                        \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_cancelled', $data));
                        app(\App\Services\MailService::class)->logMailable($order->email, "Sipariş İptal Edildi (#{$order->id})", "Sipariş iptal olarak işaretlendi.", 'success', null, $order->id);

                        // İptal SMS Gönderimi (Netgsm)
                        try {
                            $cancelSms = "Sayın {$order->name}, #{$order->id} numaralı siparişiniz iptal edilmiştir. Detaylı bilgi veya sorularınız için bizimle iletişime geçebilirsiniz. AhşapEvim";
                            app(NetgsmService::class)->sendSms($order->phone, $cancelSms, $order->id, 'automated');
                        } catch (\Throwable $smsEx) {
                            \Illuminate\Support\Facades\Log::error('İptal SMS Gönderim Hatası: ' . $smsEx->getMessage());
                        }

                        // Stokları geri yükle
                        if (in_array($oldStatus, ['paid', 'preparing', 'pending'])) {
                            foreach ($order->items as $item) {
                                if ($item->product) {
                                    $item->product->increment('stock', $item->quantity);
                                }
                            }
                        }
                    } elseif ($newStatus === 'failed') {
                        // Başarısız E-Posta
                        \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_failed', $data));
                        app(\App\Services\MailService::class)->logMailable($order->email, "Sipariş Ödeme / İşlem Başarısız (#{$order->id})", "Sipariş başarısız olarak işaretlendi.", 'success', null, $order->id);

                        // Başarısız SMS
                        try {
                            $failedSms = "Sayın {$order->name}, #{$order->id} numaralı siparişinizin işlemi tamamlanamamıştır. Detaylı bilgi için bizimle iletişime geçebilirsiniz. AhşapEvim";
                            app(NetgsmService::class)->sendSms($order->phone, $failedSms, $order->id, 'automated');
                        } catch (\Throwable $smsEx) {
                            \Illuminate\Support\Facades\Log::error('Başarısız SMS Gönderim Hatası: ' . $smsEx->getMessage());
                        }
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Status Change Email Error: ' . $e->getMessage());
                    app(\App\Services\MailService::class)->logMailable($order->email, "Sipariş Durum Bilgilendirmesi (#{$order->id})", "Durum güncelleme e-postası", 'failed', $e->getMessage(), $order->id);
                }
            }
        }

        return redirect()->back()->with('success', "Sipariş #{$order->id} durumu ve kargo bilgileri başarıyla güncellendi.");
    }

    protected function formatOrderItemsHtml($order)
    {
        $html = '<table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px;">';
        $html .= '<thead><tr style="background-color: #F5F2EB; text-align: left; color: #666;"><th style="padding: 8px; border-bottom: 1px solid #EFEAE0;">Ürün</th><th style="padding: 8px; text-align: center; border-bottom: 1px solid #EFEAE0;">Adet</th><th style="padding: 8px; text-align: right; border-bottom: 1px solid #EFEAE0;">Fiyat</th></tr></thead>';
        $html .= '<tbody>';

        foreach ($order->items as $item) {
            $pName = e($item->product ? $item->product->name : 'Ahşap Ürün');
            $qty = intval($item->quantity);
            $price = number_format($item->price * $qty, 2, ',', '.');

            $giftHtml = '';
            if (!empty($item->features['is_gift']) || !empty($item->features['gift_note'])) {
                $gNote = e($item->features['gift_note'] ?? 'Hediye Paketi');
                $giftHtml = "<br><span style=\"color: #C87A53; font-size: 11px; font-weight: bold;\">🎁 Hediye Notu: {$gNote}</span>";
            }

            $html .= "<tr><td style=\"padding: 8px; border-bottom: 1px solid #EFEAE0;\">{$pName}{$giftHtml}</td><td style=\"padding: 8px; text-align: center; border-bottom: 1px solid #EFEAE0;\">{$qty}</td><td style=\"padding: 8px; text-align: right; border-bottom: 1px solid #EFEAE0;\">₺{$price}</td></tr>";
        }

        $html .= '</tbody></table>';
        return $html;
    }

    public function destroy(Request $request, $id)
    {
        $order = Order::with('items')->findOrFail($id);

        // Security Check: Confirm Admin Password
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

        return redirect()->route('admin.orders.index')->with('success', "Sipariş (#{$id}) ve ilişkili {$deletedPhotoCount} adet müşteri fotoğrafı sistemden kalıcı olarak silindi.");
    }

    public function downloadImage(Request $request)
    {
        $path = $request->query('path');
        if (!$path) {
            abort(404, 'Görsel yolu bulunamadı.');
        }

        $relativePath = parse_url($path, PHP_URL_PATH);
        if (!$relativePath) {
            $relativePath = $path;
        }

        $relativePath = ltrim($relativePath, '/');

        if (!str_starts_with($relativePath, 'uploads/')) {
            abort(403, 'Erişim yetkiniz bulunmamaktadır.');
        }

        $fullPath = public_path($relativePath);

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            abort(404, 'İstenen görsel dosyası bulunamadı.');
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $customName = $request->query('filename');

        if ($customName) {
            $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $customName);
            $downloadFileName = $cleanName . '.' . $extension;
        } else {
            $downloadFileName = basename($fullPath);
        }

        return response()->download($fullPath, $downloadFileName, [
            'Content-Type' => mime_content_type($fullPath) ?: 'application/octet-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}

