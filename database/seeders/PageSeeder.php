<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $contactHtml = <<<HTML
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-4">
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-2">İletişim Bilgilerimiz</h3>
        
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center text-[#C87A53] text-xl shrink-0">
                <i class="fa-solid fa-phone"></i>
            </div>
            <div>
                <div class="font-bold text-gray-700 text-[15px]">Müşteri Hizmetleri</div>
                <a href="tel:+90850xxxxxxx" class="text-gray-600 hover:text-[#C87A53] transition block mt-0.5">0850 XXX XX XX</a>
            </div>
        </div>

        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl shrink-0">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <div class="font-bold text-gray-700 text-[15px]">WhatsApp Destek</div>
                <a href="https://wa.me/905xxxxxxxxx" class="text-gray-600 hover:text-green-600 transition block mt-0.5">05XX XXX XX XX</a>
            </div>
        </div>

        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 text-xl shrink-0">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <div class="font-bold text-gray-700 text-[15px]">Çalışma Saatleri</div>
                <div class="text-gray-600 mt-0.5">Hafta İçi: 09:00 - 18:00<br>Cumartesi: 10:00 - 15:00</div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-xl shrink-0">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
                <div class="font-bold text-gray-700 text-[15px]">Atölye / Mağaza Adresi</div>
                <div class="text-gray-600 mt-0.5">Şehzadeler Mevkii, Merkez<br>Manisa, Türkiye</div>
            </div>
        </div>
    </div>
    
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-2">Konumumuz</h3>
        <div class="w-full h-80 rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-100 relative">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d100001.32837311103!2d27.359288219030202!3d38.61867137839352!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b98d249f0322b7%3A0xc486be78a2e7c4f4!2sManisa%2C%20%C5%9Eehzadeler%2FManisa!5e0!3m2!1str!2str!4v1700000000000!5m2!1str!2str" class="absolute inset-0 w-full h-full" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>
HTML;

        $pages = [
            [
                'slug' => 'iletisim',
                'title' => 'İletişim',
                'content' => $contactHtml,
            ],
            [
                'slug' => 'sikca-sorulanlar',
                'title' => 'Sıkça Sorulanlar',
                'content' => '<p><strong>Siparişim kaç günde ulaşır?</strong><br>Siparişleriniz ortalama 1-3 iş günü içinde kargoya teslim edilmektedir.</p><br><p><strong>İade koşulları nelerdir?</strong><br>Kişiselleştirilmiş ürünler hariç 14 gün içinde iade hakkınız bulunmaktadır.</p><br><p><strong>Ürünleriniz masif ahşap mı?</strong><br>Evet, tüm ürünlerimiz birinci sınıf masif ağaç kullanılarak Manisa atölyemizde el işçiliği ile üretilmektedir.</p>',
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

