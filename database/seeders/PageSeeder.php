<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $contactData = [
            'phone' => '0850 XXX XX XX',
            'whatsapp' => '05XX XXX XX XX',
            'working_hours_weekdays' => '09:00 - 18:00',
            'working_hours_saturday' => '10:00 - 15:00',
            'address' => "Şehzadeler Mevkii, Merkez\nManisa, Türkiye",
            'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d100001.32837311103!2d27.359288219030202!3d38.61867137839352!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b98d249f0322b7%3A0xc486be78a2e7c4f4!2sManisa%2C%20%C5%9Eehzadeler%2FManisa!5e0!3m2!1str!2str!4v1700000000000!5m2!1str!2str',
            'email' => 'info@ahsapevim.com',
            'note' => '',
        ];

        $pages = [
            [
                'slug' => 'iletisim',
                'title' => 'İletişim',
                'content' => json_encode($contactData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ],
            [
                'slug' => 'sikca-sorulanlar',
                'title' => 'Sıkça Sorulanlar',
                'content' => json_encode([
                    [
                        'question' => 'Siparişim kaç günde ulaşır?',
                        'answer' => 'Siparişleriniz ortalama 1-3 iş günü içinde kargoya teslim edilmektedir.'
                    ],
                    [
                        'question' => 'İade koşulları nelerdir?',
                        'answer' => 'Kişiselleştirilmiş ürünler hariç 14 gün içinde iade hakkınız bulunmaktadır.'
                    ],
                    [
                        'question' => 'Ürünleriniz masif ahşap mı?',
                        'answer' => 'Evet, tüm ürünlerimiz birinci sınıf masif ağaç kullanılarak Manisa atölyemizde el işçiliği ile üretilmektedir.'
                    ]
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ],
            [
                'slug' => 'mesafeli-satis-sozlesmesi',
                'title' => 'Mesafeli Satış Sözleşmesi',
                'content' => '<h3 class="font-bold text-gray-800 mb-2">Madde 1: Taraflar</h3><p class="mb-4">İşbu sözleşme AhşapEvim Manisa (Satıcı) ile alıcı arasında elektronik ortamda gerçekleştirilen satışa ilişkindir.</p><h3 class="font-bold text-gray-800 mb-2">Madde 2: Sözleşmenin Konusu</h3><p class="mb-4">Sözleşmenin konusu, alıcının satıcıya ait web sitesinden siparişini yaptığı ürünün satışı ve teslimi ile ilgili 6502 sayılı Tüketicinin Korunması Hakkında Kanun hükümleridir.</p>',
            ],
            [
                'slug' => 'gizlilik-politikasi',
                'title' => 'Gizlilik Politikası',
                'content' => '<h3 class="font-bold text-gray-800 mb-2">Kişisel Verilerin Korunması</h3><p class="mb-4">Kişisel verileriniz 6698 sayılı KVKK kapsamında gizlilik içerisinde işlenmektedir ve üçüncü taraflarla paylaşılmamaktadır.</p><h3 class="font-bold text-gray-800 mb-2">Ödeme Güvenliği</h3><p class="mb-4">Ödemeleriniz Iyzico 256-bit SSL korumalı altyapısı ile güvence altındadır. Kredi kartı bilgileriniz sistemimizde saklanmaz.</p>',
            ],
            [
                'slug' => 'hakkimizda',
                'title' => 'Hakkımızda',
                'content' => '<h3 class="font-bold text-gray-800 mb-2">AhşapEvim Manisa - Doğanın Zarafeti Evinizde</h3><p class="mb-4">Manisa atölyemizde birinci sınıf masif ağaçlar kullanarak el işçiliği ile ürettiğimiz çerçeveler ve ev dekorasyon ürünleri ile yaşam alanlarınıza sıcaklık katıyoruz.</p><h3 class="font-bold text-gray-800 mb-2">Vizyonumuz</h3><p class="mb-4">Geleneksel ahşap ustalığını modern tasarımlar ve 3D önizleme teknolojileri ile buluşturarak müşterilerimize eşsiz bir alışveriş deneyimi sunmaktır.</p>',
            ],
            [
                'slug' => 'teslimat-ve-iade',
                'title' => 'Teslimat ve İade Şartları',
                'content' => '<h3 class="font-bold text-gray-800 mb-2">Teslimat Bilgileri</h3><p class="mb-4">Siparişleriniz Manisa atölyemizden özenle paketlenerek anlaşmalı kargo firmalarına teslim edilir. Kargo takip numaranız SMS ile tarafınıza iletilmektedir.</p><h3 class="font-bold text-gray-800 mb-2">İade ve Değişim</h3><p class="mb-4">Standart ürünlerde 14 gün koşulsuz iade hakkınız vardır. Kişiye özel fotoğraflı veya isimli hazırlanan ürünlerde cayma hakkı yönetmelik gereği geçerli değildir.</p>',
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}

