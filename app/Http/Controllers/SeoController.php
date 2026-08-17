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
     * Dynamic sitemap.xml Content Generator
     */
    public function generateSitemapXml(): string
    {
        $baseUrl = config('app.url', url('/'));

        $urls = [];

        // Ana Sayfa & Genel Sayfalar
        $urls[] = ['loc' => $baseUrl, 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '1.0'];
        $urls[] = ['loc' => url('/urunler'), 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '0.9'];
        $urls[] = ['loc' => url('/giris'), 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.6'];
        $urls[] = ['loc' => url('/kayit'), 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.5'];
        $urls[] = ['loc' => url('/siparis-takip'), 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.5'];

        // Kurumsal Sayfalar (Page Model + Sabit Liste)
        $knownSlugs = ['iletisim', 'hakkimizda', 'gizlilik-politikasi', 'mesafeli-satis-sozlesmesi', 'teslimat-ve-iade', 'sikca-sorulanlar'];
        $addedSlugs = [];

        $pages = Page::where('is_active', true)->get();
        foreach ($pages as $page) {
            $urls[] = [
                'loc' => url('/' . $page->slug),
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
                    'loc' => url('/' . $ks),
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
                'loc' => url('/urunler?category=' . $category->slug),
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // Ürünler
        $products = Product::where('is_active', true)->latest()->get();
        foreach ($products as $product) {
            $urls[] = [
                'loc' => url('/urun/' . ($product->slug ?: $product->id)),
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
        $siteName = config('app.name', 'Ahşap Evim Manisa');
        $siteUrl  = url('/');

        $products = Product::with('category')->where('is_active', true)->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
        $xml .= '  <channel>' . "\n";
        $xml .= '    <title>' . htmlspecialchars($siteName, ENT_XML1, 'UTF-8') . '</title>' . "\n";
        $xml .= '    <link>' . htmlspecialchars($siteUrl, ENT_XML1, 'UTF-8') . '</link>' . "\n";
        $xml .= '    <description>Ahşap Evim Ürün Kataloğu ve XML Beslemesi</description>' . "\n";

        foreach ($products as $p) {
            $productUrl = url('/urun/' . ($p->slug ?: $p->id));
            $imageUrl   = $p->image ? (str_starts_with($p->image, 'http') ? $p->image : url($p->image)) : url('/cerceve.png');
            $categoryName = $p->category ? $p->category->name : 'Ahşap Çerçeve';
            
            $priceFormatted = number_format($p->price, 2, '.', '') . ' TRY';
            $isDiscounted   = $p->original_price && $p->original_price > $p->price;
            $origPriceFormatted = $isDiscounted ? number_format($p->original_price, 2, '.', '') . ' TRY' : null;

            $availability = ($p->stock > 0) ? 'in_stock' : 'out_of_stock';
            $description  = !empty($p->description) ? strip_tags($p->description) : $p->name . ' - Kaliteli masif ahşap çerçeve';

            $xml .= "    <item>\n";
            $xml .= "      <g:id>" . $p->id . "</g:id>\n";
            $xml .= "      <title>" . htmlspecialchars($p->name, ENT_XML1, 'UTF-8') . "</title>\n";
            $xml .= "      <description>" . htmlspecialchars($description, ENT_XML1, 'UTF-8') . "</description>\n";
            $xml .= "      <link>" . htmlspecialchars($productUrl, ENT_XML1, 'UTF-8') . "</link>\n";
            $xml .= "      <g:image_link>" . htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8') . "</g:image_link>\n";
            
            if ($isDiscounted && $origPriceFormatted) {
                $xml .= "      <g:price>" . $origPriceFormatted . "</g:price>\n";
                $xml .= "      <g:sale_price>" . $priceFormatted . "</g:sale_price>\n";
            } else {
                $xml .= "      <g:price>" . $priceFormatted . "</g:price>\n";
            }

            $xml .= "      <g:availability>" . $availability . "</g:availability>\n";
            $xml .= "      <g:brand>Ahşap Evim</g:brand>\n";
            $xml .= "      <g:condition>new</g:condition>\n";
            $xml .= "      <g:product_type>" . htmlspecialchars($categoryName, ENT_XML1, 'UTF-8') . "</g:product_type>\n";
            if (!empty($p->barcode)) {
                $xml .= "      <g:barcode>" . htmlspecialchars($p->barcode, ENT_XML1, 'UTF-8') . "</g:barcode>\n";
            }
            $xml .= "    </item>\n";
        }

        $xml .= '  </channel>' . "\n";
        $xml .= '</rss>';

        return $xml;
    }
}
