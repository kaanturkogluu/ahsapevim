<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNewOrderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing('items.product');
    }

    public function build()
    {
        $order = $this->order;
        $adminEmailSubject = Setting::get('admin_email_subject', 'Yeni Sipariş Alındı! (#{order_id} - ₺{total_amount})');
        
        $subject = str_replace(
            ['{order_id}', '{total_amount}', '{user_name}', '{tracking_code}'],
            [
                $order->id,
                number_format($order->total_amount, 2, ',', '.'),
                $order->name,
                $order->tracking_code ?: 'AHS-' . $order->id
            ],
            $adminEmailSubject
        );

        $fromAddress = config('mail.from.address') ?: 'info@ahsapevimmanisa.com';
        $fromName = config('mail.from.name') ?: 'AhşapEvim Bildirim';

        if (empty($fromName) || in_array($fromName, ['Laravel', '{APP_NAME}', 'null'])) {
            $fromName = 'AhşapEvim';
        }

        $html = $this->renderAdminEmailHtml($order, $subject);

        return $this->from($fromAddress, $fromName)
                    ->subject($subject)
                    ->html($html);
    }

    protected function renderAdminEmailHtml(Order $order, string $subject): string
    {
        $siteUrl = url('/');
        $adminOrderUrl = url('/yonetim/siparisler/' . $order->id);
        $totalFormatted = number_format($order->total_amount, 2, ',', '.') . ' ₺';
        $orderDate = $order->created_at ? $order->created_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i');
        
        $paymentLabel = 'Kredi / Banka Kartı (Iyzico)';
        if (str_starts_with($order->payment_id ?? '', 'EFT_') || $order->status === 'pending') {
            $paymentLabel = 'Banka Havalesi / EFT';
        }

        $statusLabels = [
            'pending'   => 'Ödeme Bekliyor',
            'paid'      => 'Ödeme Alındı',
            'preparing' => 'Hazırlanıyor',
            'shipped'   => 'Kargoya Verildi',
            'completed' => 'Tamamlandı',
            'cancelled' => 'İptal Edildi',
            'failed'    => 'Başarısız',
        ];
        $statusLabel = $statusLabels[$order->status] ?? $order->status;

        $itemsRows = '';
        foreach ($order->items as $item) {
            $productName = e($item->product ? $item->product->name : 'Ahşap Ürün');
            $quantity = intval($item->quantity);
            $priceFormatted = number_format($item->price * $quantity, 2, ',', '.') . ' ₺';
            $features = is_array($item->features) ? $item->features : (json_decode($item->features, true) ?: []);
            
            $frontImg = $features['front_image'] ?? ($features['custom_image'] ?? null);
            $backImg = $features['back_image'] ?? null;
            $previewImg = $features['custom_preview'] ?? null;

            $customDetails = '';
            if ($frontImg) {
                $customDetails .= '<div style="margin-top: 4px; font-size: 11px;"><a href="' . url($frontImg) . '" target="_blank" style="color: #C87A53; text-decoration: underline;">📷 Ön Fotoğrafı Gör</a></div>';
            }
            if ($backImg) {
                $customDetails .= '<div style="margin-top: 2px; font-size: 11px;"><a href="' . url($backImg) . '" target="_blank" style="color: #C87A53; text-decoration: underline;">📷 Arka Fotoğrafı Gör</a></div>';
            }
            if ($previewImg) {
                $customDetails .= '<div style="margin-top: 2px; font-size: 11px;"><a href="' . url($previewImg) . '" target="_blank" style="color: #4A6B82; text-decoration: underline;">🖼️ 3D Önizleme Görseli</a></div>';
            }
            if (!empty($features['is_gift']) || !empty($features['gift_note'])) {
                $note = e($features['gift_note'] ?? 'Hediye Paketi');
                $customDetails .= '<div style="margin-top: 4px; padding: 4px 8px; background: #FFF8E7; border-left: 3px solid #C87A53; font-size: 11px; color: #8A532B;">🎁 <strong>Hediye Notu:</strong> ' . $note . '</div>';
            }

            $itemsRows .= <<<TR
            <tr style="border-bottom: 1px solid #EFEAE0;">
                <td style="padding: 12px 10px; font-size: 13px; color: #2E251E;">
                    <strong>{$productName}</strong>
                    {$customDetails}
                </td>
                <td style="padding: 12px 10px; text-align: center; font-size: 13px; font-weight: bold; color: #2E251E;">
                    {$quantity}
                </td>
                <td style="padding: 12px 10px; text-align: right; font-size: 13px; font-weight: bold; color: #C87A53;">
                    {$priceFormatted}
                </td>
            </tr>
TR;
        }

        $customerNoteHtml = '';
        if (!empty($order->note)) {
            $orderNote = nl2br(e($order->note));
            $customerNoteHtml = <<<NOTE
            <div style="margin-top: 15px; padding: 12px 16px; background-color: #FFF9E6; border: 1px solid #FFE08A; border-radius: 8px;">
                <strong style="color: #946200; font-size: 12px; display: block; margin-bottom: 4px;">📝 Müşteri Sipariş Notu:</strong>
                <span style="font-size: 13px; color: #4A3B18;">{$orderNote}</span>
            </div>
NOTE;
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>{$subject}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F7F5F0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2E251E;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F7F5F0; padding: 30px 10px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="620" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #EFEAE0; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #29221C; padding: 25px 30px; text-align: center; border-bottom: 4px solid #C87A53;">
                            <h1 style="margin: 0; color: #FAF3EE; font-size: 24px; font-weight: 800; letter-spacing: 0.5px;">AhşapEvim</h1>
                            <p style="margin: 6px 0 0 0; color: #F3C99F; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">🔔 Yeni Sipariş Yönetici Bildirimi</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 30px; line-height: 1.5; color: #333333;">
                            
                            <!-- Alert Badge -->
                            <div style="background-color: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px;">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <strong style="color: #166534; font-size: 15px;">🎉 Mağazanızdan Yeni Bir Sipariş Alındı!</strong>
                                            <div style="color: #15803D; font-size: 12px; margin-top: 2px;">Tarih: {$orderDate} | Sipariş No: <strong>#{$order->id}</strong></div>
                                        </td>
                                        <td align="right" style="font-size: 18px; font-weight: 800; color: #166534;">
                                            {$totalFormatted}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Customer & Order Summary Grid -->
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td width="48%" valign="top" style="background-color: #FAF9F6; border: 1px solid #EFEAE0; border-radius: 10px; padding: 14px;">
                                        <div style="font-size: 11px; font-weight: bold; color: #8C6239; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid #E8E0D2; padding-bottom: 4px;">👤 Müşteri Bilgileri</div>
                                        <div style="font-size: 13px; font-weight: bold; color: #2E251E; margin-bottom: 3px;">{$order->name}</div>
                                        <div style="font-size: 12px; color: #555; margin-bottom: 2px;">📞 <a href="tel:{$order->phone}" style="color: #C87A53; text-decoration: none; font-weight: 600;">{$order->phone}</a></div>
                                        <div style="font-size: 12px; color: #555; margin-bottom: 2px;">✉️ <a href="mailto:{$order->email}" style="color: #C87A53; text-decoration: none;">{$order->email}</a></div>
                                        <div style="font-size: 11px; color: #888;">TC/Vergi: {$order->identity_number}</div>
                                    </td>
                                    <td width="4%"></td>
                                    <td width="48%" valign="top" style="background-color: #FAF9F6; border: 1px solid #EFEAE0; border-radius: 10px; padding: 14px;">
                                        <div style="font-size: 11px; font-weight: bold; color: #8C6239; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid #E8E0D2; padding-bottom: 4px;">📦 Sipariş Detayı</div>
                                        <div style="font-size: 12px; color: #444; margin-bottom: 3px;"><strong>Takip Kodu:</strong> <span style="font-family: monospace; color: #C87A53; font-weight: bold;">{$order->tracking_code}</span></div>
                                        <div style="font-size: 12px; color: #444; margin-bottom: 3px;"><strong>Ödeme Yöntemi:</strong> {$paymentLabel}</div>
                                        <div style="font-size: 12px; color: #444;"><strong>Durum:</strong> <span style="display: inline-block; background-color: #E6F4EA; color: #137333; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 4px;">{$statusLabel}</span></div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Delivery Address -->
                            <div style="background-color: #FAF9F6; border: 1px solid #EFEAE0; border-radius: 10px; padding: 14px; margin-bottom: 20px;">
                                <div style="font-size: 11px; font-weight: bold; color: #8C6239; text-transform: uppercase; margin-bottom: 6px; border-bottom: 1px solid #E8E0D2; padding-bottom: 4px;">📍 Teslimat & Fatura Adresi</div>
                                <div style="font-size: 12px; color: #333; line-height: 1.4;">
                                    {$order->address}<br>
                                    <strong>{$order->district} / {$order->city}</strong>
                                </div>
                            </div>

                            {$customerNoteHtml}

                            <!-- Products Table -->
                            <div style="margin-top: 25px;">
                                <div style="font-size: 13px; font-weight: bold; color: #29221C; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">🛍️ Sipariş Edilen Ürünler</div>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; background-color: #FAF9F6; border-radius: 8px; overflow: hidden; border: 1px solid #EFEAE0;">
                                    <thead>
                                        <tr style="background-color: #EFEAE0; color: #555; text-transform: uppercase; font-size: 11px;">
                                            <th style="padding: 10px; text-align: left;">Ürün</th>
                                            <th style="padding: 10px; text-align: center; width: 60px;">Adet</th>
                                            <th style="padding: 10px; text-align: right; width: 90px;">Tutar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$itemsRows}
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color: #FAF3EE;">
                                            <td colspan="2" style="padding: 12px 10px; font-size: 13px; font-weight: bold; color: #2E251E; text-align: right;">
                                                Toplam Sipariş Tutarı:
                                            </td>
                                            <td style="padding: 12px 10px; font-size: 15px; font-weight: 800; color: #C87A53; text-align: right;">
                                                {$totalFormatted}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Action Button -->
                            <div style="margin-top: 30px; text-align: center;">
                                <a href="{$adminOrderUrl}" target="_blank" style="display: inline-block; background-color: #C87A53; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: bold; padding: 14px 28px; border-radius: 12px; box-shadow: 0 4px 12px rgba(200, 122, 83, 0.3);">
                                    👉 Siparişi Yönetim Panelinde Görüntüle (#{$order->id})
                                </a>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #29221C; color: #D7CCC8; padding: 20px; font-size: 12px; line-height: 1.5; border-top: 1px solid #3D332B;">
                            <p style="margin: 0; font-weight: bold; color: #FAF3EE;">AhşapEvim Manisa — Yönetim Otomasyonu</p>
                            <p style="margin: 4px 0 0 0; opacity: 0.7;">Bu bildirim yeni sipariş geldiğinde otomatik olarak iletilmiştir.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}
