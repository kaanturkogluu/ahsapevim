<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Merchant Center Yapılandırması
    |--------------------------------------------------------------------------
    |
    | Google Merchant Center Content API v2.1 entegrasyonu için gerekli
    | ayarlar. Bu değerleri .env dosyanızdan alır.
    |
    */

    // Merchant Center hesabınızdaki Merchant ID
    // Merchant Center > Ayarlar > İşletme bilgileri'nden bulabilirsiniz
    'merchant_id' => env('GOOGLE_MERCHANT_ID', ''),

    // Service Account JSON key dosyasının tam yolu
    // Örnek: storage_path('app/google-service-account.json')
    'service_account_json' => env('GOOGLE_SERVICE_ACCOUNT_JSON', ''),

    // Hedef ülke kodu (ISO 3166-1 alpha-2)
    'country' => env('MERCHANT_COUNTRY', 'TR'),

    // Para birimi (ISO 4217)
    'currency' => env('MERCHANT_CURRENCY', 'TRY'),

    // Dil kodu (BCP 47)
    'language' => env('MERCHANT_LANGUAGE', 'tr'),

    // Marka adı (tüm ürünlere uygulanır)
    'brand' => env('MERCHANT_BRAND', 'Ahsap Evim Manisa'),

    // Varsayılan kargo süresi (gün)
    'shipping_min_days' => env('MERCHANT_SHIPPING_MIN', 3),
    'shipping_max_days' => env('MERCHANT_SHIPPING_MAX', 7),

    // Kargo bedeli (TRY, 0 = ücretsiz)
    'shipping_price' => env('MERCHANT_SHIPPING_PRICE', 0),

    // Ürün durumu (new / refurbished / used)
    'condition' => 'new',

    // Content API scopes
    'scopes' => [
        'https://www.googleapis.com/auth/content',
    ],

    /*
    |--------------------------------------------------------------------------
    | Kategori → Google Ürün Kategorisi Eşlemesi
    |--------------------------------------------------------------------------
    | Kategorinizin slug/adı ile Google'ın standart kategori ID'sini eşleyin.
    | Tam liste: https://www.google.com/basepages/producttype/taxonomy-with-ids.tr-TR.txt
    |
    | Ahşap çerçeve / tablo için: 500044 (Ev & Bahçe > Ev Dekorasyonu > Çerçeveler)
    */
    'category_map' => [
        'default'           => '500044',  // Ev Dekorasyonu > Resim Çerçeveleri
        'cerceve'           => '500044',  // Resim Çerçeveleri
        'ahsap-cerceve'     => '500044',
        'fotograf-cerceve'  => '500044',
        'tablo'             => '500044',
        'dekorasyon'        => '599',     // Ev Dekorasyonu
    ],
];
