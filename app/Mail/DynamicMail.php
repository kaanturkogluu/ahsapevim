<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynamicMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $slug;
    public array $data;

    public function __construct(string $slug, array $data = [])
    {
        $this->slug = $slug;
        $this->data = $data;
    }

    public function build()
    {
        // 1. Veritabanındaki aktif şablonu sorgula
        $template = null;
        try {
            $dbTemplate = \App\Models\EmailTemplate::where('slug', $this->slug)->first();
            if ($dbTemplate && $dbTemplate->is_active) {
                $template = [
                    'subject' => $dbTemplate->subject,
                    'content' => $dbTemplate->content,
                ];
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("DynamicMail DB lookup failed for {$this->slug}: " . $e->getMessage());
        }

        // 2. Veritabanında yoksa veya inaktifse yerleşik sistem şablonuna düş (fallback)
        if (!$template) {
            $template = $this->getSystemTemplate($this->slug);
        }

        $subject = $template['subject'] ?? 'AhşapEvim Bilgilendirme';
        $body = $template['content'] ?? '<p>Sayın {user_name}, bilgilendirme mesajınız ekte yer almaktadır.</p>';

        // Varsayılan değişken değerleri
        $defaults = [
            'site_name'           => 'AhşapEvim',
            'site_url'            => url('/'),
            'order_tracking_url'  => url('/siparis-takip'),
            'user_name'           => 'Değerli Müşterimiz',
            'user_email'          => '',
            'order_id'            => '',
            'tracking_code'       => '',
            'shipping_company'    => 'Kargo Şirketi',
            'cargo_tracking_code' => 'Belirtilmedi',
            'cancellation_reason' => 'Müşteri talebi / sistem işlemi',
            'total_amount'        => '0,00',
            'delivery_address'    => '',
            'product_details'     => '',
        ];

        $mergedData = array_merge($defaults, $this->data);

        // Değişkenleri dinamik değerlerle değiştir
        foreach ($mergedData as $key => $value) {
            $placeholder = '{' . $key . '}';
            $valStr = is_scalar($value) ? (string)$value : '';
            $subject = str_replace($placeholder, $valStr, $subject);
            $body = str_replace($placeholder, $valStr, $body);
        }

        // Kalan boş / eşleşmeyen {etiket} varsa temizle
        $subject = preg_replace('/\{[a-zA-Z0-9_-]+\}/', '', $subject);
        $body = preg_replace('/\{[a-zA-Z0-9_-]+\}/', '', $body);

        // Ahşap temalı kurumsal ana HTML şablonuna sar
        $htmlContent = $this->wrapInMasterLayout($subject, $body);

        $fromAddress = config('mail.from.address') ?: 'info@ahsapevimmanisa.com';
        $fromName = config('mail.from.name') ?: config('app.name');

        if (empty($fromName) || in_array($fromName, ['Laravel', '{APP_NAME}', 'null'])) {
            $fromName = 'AhşapEvim';
        }

        return $this->from($fromAddress, $fromName)
                    ->subject($subject)
                    ->html($htmlContent);
    }

    protected function getSystemTemplate(string $slug): array
    {
        $templates = [
            'order_success' => [
                'subject' => 'Siparişiniz Alındı! #{order_id} - {tracking_code}',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p><strong>#{order_id}</strong> numaralı siparişiniz başarıyla alınmıştır. Masif ahşap el işçiliği ile hazırlanan ürünlerinizin detayları aşağıdadır:</p>

<div style="background-color: #FAF9F6; padding: 16px; border-radius: 12px; margin: 16px 0; border: 1px solid #EFEAE0;">
    <h3 style="margin: 0 0 10px 0; color: #C87A53; font-size: 15px; border-bottom: 1px solid #E5DFD5; padding-bottom: 6px;">Sipariş Özeti</h3>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş No:</strong> #{order_id}</p>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş Takip Kodu:</strong> <span style="color: #C87A53; font-weight: bold; font-family: monospace;">{tracking_code}</span></p>
    <p style="margin: 0 0 6px 0;"><strong>Teslimat Adresi:</strong> {delivery_address}</p>
</div>

<h3 style="margin: 20px 0 10px 0; color: #29221C; font-size: 15px;">Satın Alınan Ürünler</h3>
{product_details}

<div style="margin-top: 15px; text-align: right; font-size: 16px; font-weight: bold; color: #C87A53;">
    Toplam Ödenen Tutar: ₺{total_amount}
</div>

<p style="margin-top: 20px;">Siparişinizin durumunu sitemizdeki <strong>Sipariş Takip</strong> sayfasından anlık olarak takip edebilirsiniz.</p>',
            ],
            'order_shipped' => [
                'subject' => 'Siparişiniz Kargoya Verildi! 🚚 #{order_id}',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p><strong>#{order_id}</strong> numaralı siparişinizin üretimi ve kalite kontrolü tamamlanmış olup <strong>{shipping_company}</strong> firmasına teslim edilmiştir!</p>

<div style="background-color: #F0F7FF; padding: 16px; border-radius: 12px; margin: 16px 0; border: 1px solid #D0E3FF;">
    <h3 style="margin: 0 0 10px 0; color: #1E40AF; font-size: 15px; border-bottom: 1px solid #BFDBFE; padding-bottom: 6px;">Kargo Teslimat Detayları</h3>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş No:</strong> #{order_id}</p>
    <p style="margin: 0 0 6px 0;"><strong>Kargo Firması:</strong> <strong style="color: #1E40AF;">{shipping_company}</strong></p>
    <p style="margin: 0 0 6px 0;"><strong>Kargo Takip No:</strong> <span style="color: #1E40AF; font-weight: bold; font-family: monospace;">{cargo_tracking_code}</span></p>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş Takip Kodu:</strong> <span style="font-family: monospace;">{tracking_code}</span></p>
    <p style="margin: 0;"><strong>Teslimat Adresi:</strong> {delivery_address}</p>
</div>

<p>Kargonuzun nerede olduğunu kargo firmasının resmi sayfasından veya AhşapEvim <strong>Sipariş Takip</strong> ekranından anlık olarak takip edebilirsiniz.</p>',
            ],
            'order_completed' => [
                'subject' => 'Siparişiniz Teslim Edildi! 🎉 #{order_id}',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p><strong>#{order_id}</strong> numaralı siparişiniz başarıyla teslim edilmiştir. AhşapEvim masif ahşap el işçiliği ürünlerimizi güzel günlerde kullanmanızı dileriz!</p>

<div style="background-color: #F0FDF4; padding: 16px; border-radius: 12px; margin: 16px 0; border: 1px solid #BBF7D0;">
    <p style="margin: 0 0 6px 0;"><strong>Sipariş No:</strong> #{order_id}</p>
    <p style="margin: 0;"><strong>Sipariş Takip Kodu:</strong> <span style="font-family: monospace;">{tracking_code}</span></p>
</div>

<p style="margin-top: 15px;">Bizi tercih ettiğiniz için teşekkür ederiz.</p>',
            ],
            'order_cancelled' => [
                'subject' => 'Sipariş İptal Bildirimi #{order_id}',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p><strong>#{order_id}</strong> numaralı siparişiniz iptal olarak güncellenmiştir.</p>

<div style="background-color: #FFF1F2; padding: 16px; border-radius: 12px; margin: 16px 0; border: 1px solid #FECDD3;">
    <h3 style="margin: 0 0 10px 0; color: #BE123C; font-size: 15px; border-bottom: 1px solid #FDA4AF; padding-bottom: 6px;">İptal Bilgisi</h3>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş No:</strong> #{order_id}</p>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş Takip Kodu:</strong> <span style="font-family: monospace;">{tracking_code}</span></p>
    <p style="margin: 0;"><strong>İptal Nedeni:</strong> {cancellation_reason}</p>
</div>

<p>Her türlü soru veya destek için Müşteri Hizmetlerimiz ile iletişime geçebilirsiniz.</p>',
            ],
            'order_failed' => [
                'subject' => 'Sipariş Ödeme / İşlem Bildirimi #{order_id}',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p><strong>#{order_id}</strong> numaralı siparişinizin ödeme veya işlem adımı tamamlanamamıştır.</p>

<div style="background-color: #FFF7ED; padding: 16px; border-radius: 12px; margin: 16px 0; border: 1px solid #FFEDD5;">
    <h3 style="margin: 0 0 10px 0; color: #C2410C; font-size: 15px; border-bottom: 1px solid #FED7AA; padding-bottom: 6px;">İşlem Durumu</h3>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş No:</strong> #{order_id}</p>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş Takip Kodu:</strong> <span style="font-family: monospace;">{tracking_code}</span></p>
    <p style="margin: 0;"><strong>Hata / Açıklama:</strong> {cancellation_reason}</p>
</div>

<p>Farklı bir ödeme kartı veya yöntemi ile siparişinizi yeniden deneyebilir, destek için müşteri hizmetlerimize danışabilirsiniz.</p>',
            ],
            'welcome_user' => [
                'subject' => 'AhşapEvim Ailesine Hoş Geldiniz! 🌿',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p>AhşapEvim ailesine katıldığınız için teşekkür ederiz! Masif ahşap el işçiliği ürünlerimizi inceleyebilir, beğendiğiniz modelleri özel fotoğraflarınızla kişiselleştirerek sipariş verebilirsiniz.</p>
<p>Hesabınız üzerinden tüm siparişlerinizi ve kargo durumunuzu dilediğiniz an takip edebilirsiniz.</p>',
            ],
        ];

        return $templates[$slug] ?? [
            'subject' => 'AhşapEvim Bilgilendirme',
            'content' => '<p>Sayın {user_name}, bilgilendirme mesajınız iletilmiştir.</p>',
        ];
    }

    protected function wrapInMasterLayout(string $subject, string $body): string
    {
        $siteUrl = url('/');
        return <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>{$subject}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F7F5F0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2E251E;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F7F5F0; padding: 30px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #EFEAE0; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #FAF3EE; padding: 25px; border-bottom: 2px solid #E6DFD5;">
                            <a href="{$siteUrl}" style="text-decoration: none;">
                                <h1 style="margin: 0; color: #C87A53; font-size: 26px; font-weight: 800; letter-spacing: 0.5px;">AhşapEvim</h1>
                            </a>
                            <p style="margin: 5px 0 0 0; color: #8C6239; font-size: 13px;">Masif Ahşap El İşçiliği ve Kişiye Özel Tasarımlar</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 35px; line-height: 1.6; color: #333333; font-size: 14px;">
                            {$body}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #2E251E; color: #E6DFD5; padding: 20px; font-size: 12px; line-height: 1.6;">
                            <p style="margin: 0;"><strong>AhşapEvim Manisa Atölyesi</strong></p>
                            <p style="margin: 4px 0 0 0; opacity: 0.8;">Bizi tercih ettiğiniz için teşekkür ederiz!</p>
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
