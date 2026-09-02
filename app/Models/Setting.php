<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Varsayılan ayar değerleri
     */
    public static function getDefault(string $key): mixed
    {
        $defaults = [
            // Sipariş & Bildirim Ayarları
            'admin_email'            => config('mail.from.address') ?: 'info@ahsapevimmanisa.com',
            'admin_phone'            => config('services.netgsm.admin_phone') ?: '8503074917',
            'notify_admin_email'     => '1',
            'notify_admin_sms'       => '1',
            'notify_customer_email'  => '1',
            'notify_customer_sms'    => '1',
            'admin_sms_template'     => 'Yeni siparis alindi! Siparis No: #{order_id}, Tutar: {total_amount} TL. Musteri: {user_name} ({user_phone})',
            'admin_email_subject'    => 'Yeni Sipariş Alındı! (#{order_id} - ₺{total_amount})',

            // Netgsm SMS Ayarları
            'netgsm_usercode'        => config('services.netgsm.usercode') ?: '8503074917',
            'netgsm_password'        => config('services.netgsm.password') ?: '42874.Kaan',
            'netgsm_header'          => config('services.netgsm.header') ?: 'Mete Almaz',

            // Meta & Facebook (Pixel & Conversions API)
            'facebook_pixel_id'      => config('services.facebook.pixel_id') ?: '1151884751162206',
            'facebook_access_token'  => config('services.facebook.access_token') ?: 'EAAaur7B13B4BSb1P8ZAdbIdT0uNY26NzpwVVtMsoKv3qvUD9kaVJo6nIT9O1XdGPMnbh1B4xT6lg2KItz4F65nfOmGIKPwkNG3vFHluziYhS7UlobwEQedeQZCW1CM5bEt1xXofLJAoKLqqQ5ucXpvjcMmZA7ZA7yuyXs8SA2BNqWi5ERCQdVJ713XJ7lAZDZD',

            // Genel & İletişim Bilgileri
            'site_title'             => config('app.name', 'Ahşap Evim Manisa'),
            'contact_phone'          => '0850 307 49 17',
            'contact_whatsapp'       => '0555 555 55 55',
            'contact_email'          => 'info@ahsapevimmanisa.com',
            'contact_address'        => "Şehzadeler Mevkii, Merkez\nManisa, Türkiye",
        ];

        return $defaults[$key] ?? null;
    }

    /**
     * Ayar değerini getirir (Önbellekli ve güvenli fallback ile)
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember('setting_' . $key, 3600, function () use ($key, $default) {
            try {
                if (Schema::hasTable('settings')) {
                    $setting = self::where('key', $key)->first();
                    if ($setting !== null && $setting->value !== null) {
                        return $setting->value;
                    }
                }
            } catch (\Throwable $e) {
                // Veritabanı henüz hazır değilse veya bağlantı yoksa sessizce fallback'e geç
            }

            return $default ?? self::getDefault($key);
        });
    }

    /**
     * Ayar değerini kaydeder veya günceller
     */
    public static function set(string $key, mixed $value, string $group = 'general'): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string)$value,
                'group' => $group,
            ]
        );

        Cache::forget('setting_' . $key);
        Cache::forget('settings_all_grouped');

        return $setting;
    }

    /**
     * Tüm ayarları gruplandırılmış ve varsayılanları birleştirilmiş şekilde döner
     */
    public static function getAllGrouped(): array
    {
        return Cache::remember('settings_all_grouped', 3600, function () {
            $dbSettings = [];
            try {
                if (Schema::hasTable('settings')) {
                    $all = self::all();
                    foreach ($all as $item) {
                        $dbSettings[$item->key] = $item->value;
                    }
                }
            } catch (\Throwable $e) {
                // ignore
            }

            $keys = [
                'notifications' => [
                    'admin_email', 'admin_phone',
                    'notify_admin_email', 'notify_admin_sms',
                    'notify_customer_email', 'notify_customer_sms',
                    'admin_sms_template', 'admin_email_subject',
                ],
                'sms' => [
                    'netgsm_usercode', 'netgsm_password', 'netgsm_header',
                ],
                'facebook' => [
                    'facebook_pixel_id', 'facebook_access_token',
                ],
            ];

            $result = [];
            foreach ($keys as $group => $groupKeys) {
                $result[$group] = [];
                foreach ($groupKeys as $k) {
                    $result[$group][$k] = $dbSettings[$k] ?? self::getDefault($k);
                }
            }

            return $result;
        });
    }
}
