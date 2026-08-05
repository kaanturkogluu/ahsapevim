<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminNewOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:notify-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Son 10 dakika içinde gelen ödemesi onaylanmış yeni siparişleri tek mailde toplayıp yöneticiye bildirim gönderir.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Query paid orders created recently that haven't been notified yet
        $orders = Order::with('items.product')
            ->whereIn('status', ['paid', 'preparing', 'shipped', 'completed'])
            ->whereNull('admin_notified_at')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Bildirim gönderilecek yeni ödenmiş sipariş bulunamadı.');
            return 0;
        }

        // Get admin email addresses
        $adminEmails = User::where('is_admin', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter()
            ->unique()
            ->toArray();

        if (empty($adminEmails)) {
            $fallbackEmail = config('mail.from.address', 'admin@ahsapevim.com');
            $adminEmails = [$fallbackEmail];
        }

        $orderCount = $orders->count();
        $subject = "🔔 Yeni Siparişiniz Var ({$orderCount} Adet Yeni Sipariş)";

        // Build HTML email body for consolidated orders
        $html = '<div style="font-family: Arial, sans-serif; color: #333; max-width: 680px; margin: 0 auto; border: 1px solid #EFEAE0; border-radius: 12px; overflow: hidden; background-color: #ffffff;">';
        $html .= '<div style="background-color: #C87A53; color: #ffffff; padding: 20px; text-align: center;">';
        $html .= '<h2 style="margin: 0; font-size: 20px;">🔔 AhşapEvim - Yeni Sipariş Bildirimi</h2>';
        $html .= "<p style=\"margin: 5px 0 0 0; font-size: 13px; opacity: 0.9;\">Son 10 dakika içinde <strong>{$orderCount} adet</strong> yeni sipariş alındı.</p>";
        $html .= '</div>';
        $html .= '<div style="padding: 20px;">';

        foreach ($orders as $index => $order) {
            $html .= '<div style="background-color: #FFFBF5; border: 1px solid #F5E8D8; border-radius: 10px; padding: 15px; margin-bottom: 20px;">';
            $html .= "<div style=\"display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #EFEAE0; padding-bottom: 10px; margin-bottom: 10px;\">";
            $html .= "<h3 style=\"margin: 0; color: #C87A53; font-size: 16px;\">Sipariş #" . e($order->id) . " (" . e($order->tracking_code ?: 'AHS-' . $order->id) . ")</h3>";
            $html .= "<span style=\"font-size: 12px; color: #777;\">" . $order->created_at->format('d.m.Y H:i') . "</span>";
            $html .= "</div>";

            // Customer details
            $html .= "<p style=\"margin: 4px 0; font-size: 13px;\"><strong>Müşteri:</strong> " . e($order->name) . "</p>";
            $html .= "<p style=\"margin: 4px 0; font-size: 13px;\"><strong>İletişim:</strong> " . e($order->phone) . " | " . e($order->email) . "</p>";
            $html .= "<p style=\"margin: 4px 0; font-size: 13px;\"><strong>Teslimat Adresi:</strong> " . e($order->address) . " (" . e($order->city ?: 'Manisa') . " / " . e($order->district ?: 'Merkez') . ")</p>";

            // General Order Note
            if (!empty($order->note)) {
                $html .= "<div style=\"background-color: #FFF; border: 1px solid #EFEAE0; border-left: 4px solid #C87A53; padding: 8px 12px; margin: 10px 0; border-radius: 4px;\">";
                $html .= "<strong style=\"color: #C87A53; font-size: 12px;\">📝 Müşteri Genel Sipariş Notu:</strong>";
                $html .= "<p style=\"margin: 4px 0 0 0; font-size: 12px; font-style: italic; color: #444;\">\"" . e($order->note) . "\"</p>";
                $html .= "</div>";
            }

            // Products Table
            $html .= '<h4 style="margin: 12px 0 6px 0; font-size: 13px; color: #555;">Sipariş Edilen Ürünler:</h4>';
            $html .= '<table style="width: 100%; border-collapse: collapse; font-size: 12px; background-color: #fff; border: 1px solid #EFEAE0; border-radius: 6px; overflow: hidden;">';
            $html .= '<thead><tr style="background-color: #F8F5EE; color: #666; text-align: left;"><th style="padding: 8px; border-bottom: 1px solid #EFEAE0;">Ürün Adı</th><th style="padding: 8px; text-align: center; border-bottom: 1px solid #EFEAE0;">Adet</th><th style="padding: 8px; text-align: right; border-bottom: 1px solid #EFEAE0;">Tutar</th></tr></thead>';
            $html .= '<tbody>';

            foreach ($order->items as $item) {
                $pName = e($item->product ? $item->product->name : 'Ahşap Ürün');
                $qty = intval($item->quantity);
                $priceFormatted = '₺' . number_format($item->price * $qty, 2, ',', '.');

                $giftText = '';
                if (!empty($item->features['is_gift']) || !empty($item->features['gift_note'])) {
                    $note = e($item->features['gift_note'] ?? 'Hediye Paketi');
                    $giftText = "<br><span style=\"color: #C87A53; font-size: 11px; font-weight: bold;\">🎁 Hediye Notu: {$note}</span>";
                }

                $html .= "<tr><td style=\"padding: 8px; border-bottom: 1px solid #EFEAE0;\">{$pName}{$giftText}</td><td style=\"padding: 8px; text-align: center; border-bottom: 1px solid #EFEAE0;\">{$qty}</td><td style=\"padding: 8px; text-align: right; border-bottom: 1px solid #EFEAE0;\">{$priceFormatted}</td></tr>";
            }

            $html .= '</tbody></table>';

            // Order Total
            $html .= "<div style=\"text-align: right; margin-top: 10px;\">";
            $html .= "<span style=\"font-size: 14px; font-weight: bold; color: #C87A53;\">Sipariş Toplam Tutarı: ₺" . number_format($order->total_amount, 2, ',', '.') . "</span>";
            $html .= "</div>";

            $html .= '</div>';
        }

        $html .= '<p style="text-align: center; font-size: 12px; color: #999; margin-top: 20px;">AhşapEvim Otomatik Yönetici Bildirim Sistemi</p>';
        $html .= '</div></div>';

        // Send mail to all admin recipients
        $mailService = app(MailService::class);
        foreach ($adminEmails as $adminEmail) {
            try {
                $mailService->sendMail($adminEmail, $subject, $html);
                $this->info("Yönetici e-postası başarıyla gönderildi: {$adminEmail}");
            } catch (\Throwable $e) {
                Log::error("Yönetici sipariş bildirim maili gönderim hatası ({$adminEmail}): " . $e->getMessage());
                $this->error("Mail gönderim hatası ({$adminEmail}): " . $e->getMessage());
            }
        }

        // Mark orders as notified
        Order::whereIn('id', $orders->pluck('id'))->update([
            'admin_notified_at' => now(),
        ]);

        $this->info("Toplam {$orderCount} adet yeni sipariş yöneticiye bildirildi.");
        return 0;
    }
}
