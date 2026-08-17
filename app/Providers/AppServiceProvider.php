<?php

namespace App\Providers;

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
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('categories')) {
                    $view->with('navCategories', \App\Models\Category::all());
                }
            } catch (\Throwable $e) {
                $view->with('navCategories', collect());
            }

            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('pages')) {
                    // Cache iletisim verisini 10 dakika boyunca sakla (her istekte DB sorgusu yapmaz)
                    $contactData = \Illuminate\Support\Facades\Cache::remember('contact_data', 600, function () {
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
                    });

                    $view->with('contactData', $contactData);
                }
            } catch (\Throwable $e) {
                $view->with('contactData', [
                    'phone'                  => '0850 XXX XX XX',
                    'whatsapp'               => '05XX XXX XX XX',
                    'working_hours_weekdays' => '09:00 - 18:00',
                    'working_hours_saturday' => '10:00 - 15:00',
                    'address'                => "Şehzadeler Mevkii, Merkez\nManisa, Türkiye",
                    'email'                  => 'info@ahsapevim.com',
                    'map_url'                => '',
                    'note'                   => '',
                ]);
            }
        });
    }
}
