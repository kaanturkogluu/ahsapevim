<?php

namespace App\Jobs;

use App\Mail\AdminNewOrderMail;
use App\Mail\DynamicMail;
use App\Models\Order;
use App\Models\Setting;
use App\Services\MailService;
use App\Services\NetgsmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewOrderNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public int $tries = 3;
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(NetgsmService $netgsm, MailService $mailService): void
    {
        $order = Order::with('items.product')->find($this->orderId);
        if (!$order) {
            Log::warning("SendNewOrderNotificationJob: Order #{$this->orderId} not found.");
            return;
        }

        Log::info("SendNewOrderNotificationJob: Processing notifications for Order #{$order->id} (Status: {$order->status}).");

        // ── 1. Ayarları Oku ──────────────────────────────────────────────
        $adminEmail           = Setting::get('admin_email', config('mail.from.address') ?: 'info@ahsapevimmanisa.com');
        $adminPhone           = Setting::get('admin_phone', config('services.netgsm.admin_phone') ?: '8503074917');
        $notifyAdminEmail     = (string)Setting::get('notify_admin_email', '1') === '1';
        $notifyAdminSms       = (string)Setting::get('notify_admin_sms', '1') === '1';
        $notifyCustomerEmail  = (string)Setting::get('notify_customer_email', '1') === '1';
        $notifyCustomerSms    = (string)Setting::get('notify_customer_sms', '1') === '1';
        $adminSmsTemplate     = Setting::get('admin_sms_template', 'Yeni siparis alindi! Siparis No: #{order_id}, Tutar: {total_amount} TL. Musteri: {user_name} ({user_phone})');

        // ── 2. Yöneticiye E-Posta Gönderimi ─────────────────────────────
        if ($notifyAdminEmail && !empty($adminEmail)) {
            try {
                Mail::to($adminEmail)->send(new AdminNewOrderMail($order));
                $mailService->logMailable(
                    $adminEmail,
                    "Yeni Sipariş Yönetici Bildirimi (#{$order->id})",
                    "Yönetici e-posta bildirimi gönderildi.",
                    'success',
                    null,
                    $order->id
                );
                Log::info("SendNewOrderNotificationJob: Admin email sent to {$adminEmail} for Order #{$order->id}");
            } catch (\Throwable $e) {
                Log::error("SendNewOrderNotificationJob: Admin email error: " . $e->getMessage());
                $mailService->logMailable(
                    $adminEmail,
                    "Yeni Sipariş Yönetici Bildirimi (#{$order->id})",
                    "Yönetici e-postası",
                    'failed',
                    $e->getMessage(),
                    $order->id
                );
            }
        }

        // ── 3. Yöneticiye SMS Gönderimi (Netgsm) ────────────────────────
        if ($notifyAdminSms && !empty($adminPhone)) {
            try {
                $smsText = str_replace(
                    ['{order_id}', '{total_amount}', '{user_name}', '{user_phone}', '{tracking_code}'],
                    [
                        $order->id,
                        number_format($order->total_amount, 2, ',', '.'),
                        $order->name,
                        $order->phone,
                        $order->tracking_code ?: 'AHS-' . $order->id,
                    ],
                    $adminSmsTemplate
                );

                $netgsm->sendSms($adminPhone, $smsText, $order->id, 'automated');
                Log::info("SendNewOrderNotificationJob: Admin SMS dispatched to {$adminPhone} for Order #{$order->id}");
            } catch (\Throwable $e) {
                Log::error("SendNewOrderNotificationJob: Admin SMS error: " . $e->getMessage());
            }
        }

        // ── 4. Müşteriye Sipariş Onay E-Postası Gönderimi ────────────────
        if ($notifyCustomerEmail && !empty($order->email)) {
            try {
                $orderData = [
                    'user_name'        => $order->name,
                    'order_id'         => $order->id,
                    'tracking_code'    => $order->tracking_code ?: 'AHS-' . $order->id,
                    'total_amount'     => number_format($order->total_amount, 2, ',', '.'),
                    'delivery_address' => $order->address . ' (' . ($order->city ?: 'Manisa') . ')',
                    'product_details'  => $this->formatOrderItemsHtml($order),
                ];

                Mail::to($order->email)->send(new DynamicMail('order_success', $orderData));
                $mailService->logMailable(
                    $order->email,
                    "Siparişiniz Alındı (#{$order->id})",
                    "Sipariş onay e-postası müşteriye iletildi.",
                    'success',
                    null,
                    $order->id
                );
            } catch (\Throwable $e) {
                Log::error("SendNewOrderNotificationJob: Customer email error: " . $e->getMessage());
                $mailService->logMailable(
                    $order->email,
                    "Siparişiniz Alındı (#{$order->id})",
                    "Sipariş onay e-postası",
                    'failed',
                    $e->getMessage(),
                    $order->id
                );
            }
        }

        // ── 5. Müşteriye Sipariş Onay SMS Gönderimi ─────────────────────
        if ($notifyCustomerSms && !empty($order->phone)) {
            try {
                $customerMsg = "Degerli musterimiz, #" . $order->id . " nolu siparisiniz basariyla alinmistir. Siparisiniz en kisa surede kargolanacaktir. Bizi tercih ettiginiz icin tesekkur ederiz.";
                $netgsm->sendSms($order->phone, $customerMsg, $order->id, 'automated');
            } catch (\Throwable $e) {
                Log::error("SendNewOrderNotificationJob: Customer SMS error: " . $e->getMessage());
            }
        }

        // ── 6. admin_notified_at Güncelle ───────────────────────────────
        try {
            $order->update(['admin_notified_at' => now()]);
        } catch (\Throwable $e) {
            Log::error("SendNewOrderNotificationJob: admin_notified_at update error: " . $e->getMessage());
        }
    }

    protected function formatOrderItemsHtml(Order $order): string
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
}
