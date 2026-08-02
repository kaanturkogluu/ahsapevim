<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'welcome_user',
                'name' => 'Kullanıcı Kayıt Hoş Geldin Bildirimi',
                'subject' => 'AhşapEvim Ailesine Hoş Geldiniz! 🌿',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p>AhşapEvim ailesine katıldığınız için teşekkür ederiz! Masif ahşap el işçiliği ürünlerimizi inceleyebilir, beğendiğiniz çerçeveleri özel fotoğraflarınızla kişiselleştirerek sipariş verebilirsiniz.</p>
<p>Hesabınız üzerinden tüm siparişlerinizi ve kargo durumunuzu dilediğiniz an takip edebilirsiniz.</p>',
                'shortcodes' => [
                    '{user_name}' => 'Müşterinin Adı Soyadı',
                    '{user_email}' => 'Müşterinin E-Posta Adresi',
                    '{site_name}' => 'Site İsmi (AhşapEvim)',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'order_success',
                'name' => 'Yeni Sipariş Onay Bildirimi',
                'subject' => 'Siparişiniz Alındı! #{order_id} - {tracking_code}',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p><strong>#{order_id}</strong> numaralı siparişiniz başarıyla alınmıştır. Masif ahşap el işçiliği ile ürettiğimiz ürünleriniz özenle hazırlanıp en kısa sürede kargoya teslim edilecektir.</p>
<div style="background-color: #FAF9F6; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #EFEAE0;">
    <p style="margin: 0 0 6px 0;"><strong>Sipariş No:</strong> #{order_id}</p>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş Takip Kodu:</strong> <span style="color: #C87A53; font-weight: bold; font-family: monospace;">{tracking_code}</span></p>
    <p style="margin: 0 0 6px 0;"><strong>Ödenecek Tutar:</strong> ₺{total_amount}</p>
    <p style="margin: 0;"><strong>Teslimat Adresi:</strong> {delivery_address}</p>
</div>
<p>Siparişinizin durumunu dilediğiniz zaman sitemizdeki <strong>Sipariş Takip</strong> sayfasından sorgulayabilirsiniz.</p>',
                'shortcodes' => [
                    '{user_name}' => 'Müşterinin Adı Soyadı',
                    '{order_id}' => 'Sipariş Numarası',
                    '{tracking_code}' => 'Sipariş Takip Kodu',
                    '{total_amount}' => 'Toplam Sipariş Tutarı',
                    '{delivery_address}' => 'Teslimat Adresi',
                    '{site_name}' => 'Site İsmi',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'order_shipped',
                'name' => 'Sipariş Kargoya Verildi Bildirimi',
                'subject' => 'Siparişiniz Kargoya Verildi! 🚚 #{order_id}',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p><strong>#{order_id}</strong> numaralı siparişiniz hazırlanmış ve kargoya teslim edilmiştir!</p>
<div style="background-color: #F0F7FF; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #D0E3FF;">
    <p style="margin: 0 0 6px 0;"><strong>Sipariş No:</strong> #{order_id}</p>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş Takip Kodu:</strong> <span style="color: #1E40AF; font-weight: bold; font-family: monospace;">{tracking_code}</span></p>
    <p style="margin: 0;"><strong>Teslim Edilecek Adres:</strong> {delivery_address}</p>
</div>
<p>Ürününüzün nerede olduğunu sitemizdeki Sipariş Takip ekranından anlık olarak sorgulayabilirsiniz.</p>',
                'shortcodes' => [
                    '{user_name}' => 'Müşterinin Adı Soyadı',
                    '{order_id}' => 'Sipariş Numarası',
                    '{tracking_code}' => 'Sipariş Takip Kodu',
                    '{delivery_address}' => 'Teslimat Adresi',
                    '{site_name}' => 'Site İsmi',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'order_completed',
                'name' => 'Sipariş Teslim Edildi Bildirimi',
                'subject' => 'Siparişiniz Teslim Edildi! 🎉 #{order_id}',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p><strong>#{order_id}</strong> numaralı siparişiniz başarıyla teslim edilmiştir. AhşapEvim masif ürünlerini güzel günlerde kullanmanızı dileriz!</p>
<p>Deneyiminizi paylaşmak ve yeni ürünlerimizi keşfetmek için sitemizi ziyaret edebilirsiniz.</p>',
                'shortcodes' => [
                    '{user_name}' => 'Müşterinin Adı Soyadı',
                    '{order_id}' => 'Sipariş Numarası',
                    '{tracking_code}' => 'Sipariş Takip Kodu',
                    '{site_name}' => 'Site İsmi',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'order_cancelled',
                'name' => 'Sipariş İptal Edildi Bildirimi',
                'subject' => 'Sipariş İptal Bildirimi #{order_id}',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p><strong>#{order_id}</strong> numaralı siparişiniz iptal edilmiştir. İptal süreci veya iade işlemleri ile ilgili sorularınız için bizimle iletişime geçebilirsiniz.</p>',
                'shortcodes' => [
                    '{user_name}' => 'Müşterinin Adı Soyadı',
                    '{order_id}' => 'Sipariş Numarası',
                    '{tracking_code}' => 'Sipariş Takip Kodu',
                    '{site_name}' => 'Site İsmi',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
