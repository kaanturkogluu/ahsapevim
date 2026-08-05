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
                'name' => 'Sipariş Alındı Bildirimi',
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
                'shortcodes' => [
                    '{user_name}' => 'Müşterinin Adı Soyadı',
                    '{order_id}' => 'Sipariş Numarası',
                    '{tracking_code}' => 'Sipariş Takip Kodu',
                    '{product_details}' => 'Satın Alınan Ürünler Listesi ve Fiyatlar',
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
<p><strong>#{order_id}</strong> numaralı siparişinizin üretimi ve kontrolü tamamlanmış olup kargoya teslim edilmiştir!</p>

<div style="background-color: #F0F7FF; padding: 16px; border-radius: 12px; margin: 16px 0; border: 1px solid #D0E3FF;">
    <h3 style="margin: 0 0 10px 0; color: #1E40AF; font-size: 15px; border-bottom: 1px solid #BFDBFE; padding-bottom: 6px;">Kargo Teslimat Detayları</h3>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş No:</strong> #{order_id}</p>
    <p style="margin: 0 0 6px 0;"><strong>Kargo Firması:</strong> <strong style="color: #1E40AF;">{shipping_company}</strong></p>
    <p style="margin: 0 0 6px 0;"><strong>Kargo Takip No:</strong> <span style="color: #1E40AF; font-weight: bold; font-family: monospace;">{cargo_tracking_code}</span></p>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş Takip Kodu:</strong> <span style="font-family: monospace;">{tracking_code}</span></p>
    <p style="margin: 0;"><strong>Teslimat Adresi:</strong> {delivery_address}</p>
</div>

<p>Kargonuzun nerede olduğunu kargo firmasının resmi sitesinden veya AhşapEvim <strong>Sipariş Takip</strong> ekranından canlı olarak takip edebilirsiniz.</p>',
                'shortcodes' => [
                    '{user_name}' => 'Müşterinin Adı Soyadı',
                    '{order_id}' => 'Sipariş Numarası',
                    '{shipping_company}' => 'Kargo Firması Adı (Örn: Yurtiçi Kargo)',
                    '{cargo_tracking_code}' => 'Kargo Takip Numarası',
                    '{tracking_code}' => 'Sipariş Takip Kodu',
                    '{delivery_address}' => 'Teslimat Adresi',
                    '{site_name}' => 'Site İsmi',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'order_cancelled',
                'name' => 'Sipariş İptal Edildi Bildirimi',
                'subject' => 'Sipariş İptal Bildirimi #{order_id}',
                'content' => '<p>Sayın <strong>{user_name}</strong>,</p>
<p><strong>#{order_id}</strong> numaralı siparişiniz iptal olarak güncellenmiştir.</p>

<div style="background-color: #FFF1F2; padding: 16px; border-radius: 12px; margin: 16px 0; border: 1px solid #FECDD3;">
    <h3 style="margin: 0 0 10px 0; color: #BE123C; font-size: 15px; border-bottom: 1px solid #FDA4AF; padding-bottom: 6px;">İptal Bilgisi</h3>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş No:</strong> #{order_id}</p>
    <p style="margin: 0 0 6px 0;"><strong>Sipariş Takip Kodu:</strong> <span style="font-family: monospace;">{tracking_code}</span></p>
    <p style="margin: 0;"><strong>İptal / Nedeni:</strong> {cancellation_reason}</p>
</div>

<p>Süreç ile ilgili her türlü soru, iade takibi veya destek için Müşteri Hizmetlerimiz ile iletişime geçebilirsiniz.</p>',
                'shortcodes' => [
                    '{user_name}' => 'Müşterinin Adı Soyadı',
                    '{order_id}' => 'Sipariş Numarası',
                    '{tracking_code}' => 'Sipariş Takip Kodu',
                    '{cancellation_reason}' => 'İptal Nedeni veya Hata Açıklaması',
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
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
