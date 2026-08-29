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
        $template = $this->getSystemTemplate($this->slug);
        $subject = $template['subject'];
        $body = $template['content'];

        // Add default site_name
        if (!isset($this->data['site_name'])) {
            $this->data['site_name'] = 'AhşapEvim';
        }

        // Replace placeholders with real values
        foreach ($this->data as $key => $value) {
            $placeholder = '{' . $key . '}';
            $subject = str_replace($placeholder, (string)$value, $subject);
            $body = str_replace($placeholder, (string)$value, $body);
        }

        // Wrap in clean, warm wood-themed master HTML email layout
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
<p><strong>#{order_id}</strong> numaralı siparişinizin üretimi ve kalite kontrolü tamamlanmış olup kargoya teslim edilmiştir!</p>

<div style="background-color: #F0F7FF; padding: 16px; border-radius: 12px; margin: 16px 0; border: 1px solid #D0E3FF;">
    <h3 style="margin: 0 0 10px 0; color: #1E40AF; font-size: 15px; border-bottom: 1px solid #BFDBFE; padding-bottom: 6px;">Kargo Teslimat Detayları</h3>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş No:</strong> #{order_id}</p>
    <p style="margin: 0 0 6px 0;"><strong>Kargo Firması:</strong> <strong style="color: #1E40AF;">{shipping_company}</strong></p>
    <p style="margin: 0 0 6px 0;"><strong>Kargo Takip No:</strong> <span style="color: #1E40AF; font-weight: bold; font-family: monospace;">{cargo_tracking_code}</span></p>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş Takip Kodu:</strong> <span style="font-family: monospace;">{tracking_code}</span></p>
    <p style="margin: 0;"><strong>Teslimat Adresi:</strong> {delivery_address}</p>
</div>

<p>Kargonuzun nerede olduğunu sitemizdeki <strong>Sipariş Takip</strong> sayfasından takip edebilirsiniz.</p>',
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
            'order_completed' => [
                'subject' => 'Siparişiniz Teslim Edildi! 🎉 #{order_id}',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p><strong>#{order_id}</strong> numaralı siparişiniz başarıyla teslim edilmiştir. AhşapEvim masif ahşap el işçiliği ürünlerini güzel günlerde kullanmanızı dileriz!</p>
<p>Bizi tercih ettiğiniz için teşekkür ederiz.</p>',
            ],
            'welcome_user' => [
                'subject' => 'AhşapEvim Ailesine Hoş Geldiniz! 🌿',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p>AhşapEvim ailesine katıldığınız için teşekkür ederiz! Masif ahşap el işçiliği ürünlerimizi inceleyebilir, beğendiğiniz modelleri özel fotoğraflarınızla kişiselleştirerek sipariş verebilirsiniz.</p>',
            ],
        ];

        return $templates[$slug] ?? [
            'subject' => 'AhşapEvim Bilgilendirme',
            'content' => '<p>Sayın {user_name}, bilgilendirme mesajınız ekte yer almaktadır.</p>',
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
