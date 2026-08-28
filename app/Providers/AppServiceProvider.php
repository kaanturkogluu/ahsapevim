<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            // Menü kategorilerini 10 dakika önbellekte tut (her görünümde DB sorgusu yapmaz)
            $navCategories = \Illuminate\Support\Facades\Cache::remember('nav_categories', 600, function () {
                try {
                    return \App\Models\Category::all();
                } catch (\Throwable $e) {
                    return collect();
                }
            });
            $view->with('navCategories', $navCategories);

            // İletişim verisini 10 dakika önbellekte tut
            $contactData = \Illuminate\Support\Facades\Cache::remember('contact_data', 600, function () {
                try {
                    $contactPage = \App\Models\Page::where('slug', 'iletisim')->first();
                    $data = [];
                    if ($contactPage && !empty($contactPage->content)) {
                        $decoded = json_decode($contactPage->content, true);
                        if (is_array($decoded)) {
                            $data = $decoded;
                        }
                    }
                    return array_merge([
                        'phone'                  => '0850 XXX XX XX',
                        'whatsapp'               => '05XX XXX XX XX',
                        'working_hours_weekdays' => '09:00 - 18:00',
                        'working_hours_saturday' => '10:00 - 15:00',
                        'address'                => "Şehzadeler Mevkii, Merkez\nManisa, Türkiye",
                        'email'                  => 'info@ahsapevim.com',
                        'map_url'                => '',
                        'note'                   => '',
                    ], array_filter($data, fn($val) => !is_null($val) && $val !== ''));
                } catch (\Throwable $e) {
                    return [
                        'phone'                  => '0850 XXX XX XX',
                        'whatsapp'               => '05XX XXX XX XX',
                        'working_hours_weekdays' => '09:00 - 18:00',
                        'working_hours_saturday' => '10:00 - 15:00',
                        'address'                => "Şehzadeler Mevkii, Merkez\nManisa, Türkiye",
                        'email'                  => 'info@ahsapevim.com',
                        'map_url'                => '',
                        'note'                   => '',
                    ];
                }
            });
            $view->with('contactData', $contactData);
        });
    }
}
