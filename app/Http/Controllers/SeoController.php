<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class SeoController extends Controller
{
    /**
     * sitemap.xml Oluştur ve Sun
     */
    public function sitemap(): Response
    {
        $xml = $this->generateSitemapXml();
        
        // Static sitemap.xml dosyasına da yaz
        try {
            File::put(public_path('sitemap.xml'), $xml);
        } catch (\Throwable $e) {
            // Log or ignore file system write permission issue
        }

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8'
        ]);
    }

    /**
     * urunler.xml (Google Merchant / XML Ürün Kataloğu) Oluştur ve Sun
     */
    public function urunlerXml(): Response
    {
        $xml = $this->generateUrunlerXml();

        // Static urunler.xml dosyasına da yaz
        try {
            File::put(public_path('urunler.xml'), $xml);
        } catch (\Throwable $e) {
            // Log or ignore file system write permission issue
        }

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8'
        ]);
    }

    /**
     * facebook-catalog.xml (Meta & Facebook Commerce Manager XML Feed) Oluştur ve Sun
     */
    public function facebookCatalogXml(): Response
    {
        $xml = $this->generateFacebookCatalogXml();

        // Static facebook-catalog.xml dosyasına da yaz
        try {
            File::put(public_path('facebook-catalog.xml'), $xml);
        } catch (\Throwable $e) {
            // Log or ignore file system write permission issue
        }

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8'
        ]);
    }

    /**
     * Dynamic sitemap.xml Content Generator
     */
    public function generateSitemapXml(): string
    {
        $liveDomain = 'https://ahsapevimmanisa.com';

        $urls = [];

        // Ana Sayfa & Genel Sayfalar
        $urls[] = ['loc' => $liveDomain, 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '1.0'];
        $urls[] = ['loc' => $liveDomain . '/urunler', 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '0.9'];
        $urls[] = ['loc' => $liveDomain . '/giris', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'];
        $urls[] = ['loc' => $liveDomain . '/kayit', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.5'];
        $urls[] = ['loc' => $liveDomain . '/siparis-takip', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.5'];

        // Kurumsal Sayfalar (Page Model + Sabit Liste)
        $knownSlugs = ['iletisim', 'hakkimizda', 'gizlilik-politikasi', 'mesafeli-satis-sozlesmesi', 'teslimat-ve-iade', 'sikca-sorulanlar'];
        $addedSlugs = [];

        $pages = Page::where('is_active', true)->get();
        foreach ($pages as $page) {
            $urls[] = [
                'loc' => $liveDomain . '/' . $page->slug,
                'lastmod' => $page->updated_at ? $page->updated_at->format('Y-m-d') : date('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
            $addedSlugs[] = $page->slug;
        }

        // Eksik kalan bilinen kurumsal sayfaları da garanti ekle
        foreach ($knownSlugs as $ks) {
            if (!in_array($ks, $addedSlugs)) {
                $urls[] = [
                    'loc' => $liveDomain . '/' . $ks,
                    'lastmod' => date('Y-m-d'),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            }
        }

        // Kategoriler
        $categories = Category::all();
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => $liveDomain . '/urunler?category=' . $category->slug,
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // Ürünler
        $products = Product::where('is_active', true)->ordered()->get();
        foreach ($products as $product) {
            $urls[] = [
                'loc' => $liveDomain . '/urun/' . ($product->slug ?: $product->id),
                'lastmod' => $product->updated_at ? $product->updated_at->format('Y-m-d') : date('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . $u['lastmod'] . "</lastmod>\n";
            $xml .= "    <changefreq>" . $u['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $u['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Dynamic urunler.xml (Google Shopping / Merchant XML Feed) Generator
     */
    public function generateUrunlerXml(): string
    {
        $products = Product::with('category')->where('is_active', true)->ordered()->get();

        return view('seo.urunler_xml', compact('products'))->render();
    }

    /**
     * Dynamic facebook-catalog.xml (Facebook Commerce Manager XML Feed) Generator
     */
    public function generateFacebookCatalogXml(): string
    {
        $products = Product::with('category')->where('is_active', true)->ordered()->get();

        return view('seo.facebook_catalog_xml', compact('products'))->render();
    }
}
