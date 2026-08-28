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
        $siteName = 'Ahşap Evim Manisa';
        $siteUrl  = 'https://ahsapevimmanisa.com';
        $liveDomain = 'https://ahsapevimmanisa.com';

        $products = Product::with('category')->where('is_active', true)->ordered()->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
        $xml .= '  <channel>' . "\n";
        $xml .= '    <title>' . htmlspecialchars($siteName, ENT_XML1, 'UTF-8') . '</title>' . "\n";
        $xml .= '    <link>' . htmlspecialchars($siteUrl, ENT_XML1, 'UTF-8') . '</link>' . "\n";
        $xml .= '    <description>Ahşap Evim Kişiye Özel Ahşap Çerçeve ve Dekorasyon Ürünleri</description>' . "\n";

        foreach ($products as $p) {
            // Canlı Domain URL'leri
            $productSlugOrId = $p->slug ?: $p->id;
            $productUrl = $liveDomain . '/urun/' . $productSlugOrId;

            // Ana Görsel
            $rawImg = $p->getRawOriginal('image') ?: $p->image;
            if ($rawImg) {
                if (str_starts_with($rawImg, 'http://') || str_starts_with($rawImg, 'https://')) {
                    $imageUrl = str_replace(['http://localhost', 'https://localhost'], $liveDomain, $rawImg);
                } else {
                    $imageUrl = $liveDomain . (str_starts_with($rawImg, '/') ? '' : '/') . $rawImg;
                }
            } else {
                $imageUrl = $liveDomain . '/cerceve.png';
            }

            // Ek Görseller (features.images)
            $additionalImages = [];
            if (!empty($p->features['images']) && is_array($p->features['images'])) {
                foreach ($p->features['images'] as $addImg) {
                    if (empty($addImg)) continue;
                    if (str_starts_with($addImg, 'http://') || str_starts_with($addImg, 'https://')) {
                        $addImgUrl = str_replace(['http://localhost', 'https://localhost'], $liveDomain, $addImg);
                    } else {
                        $addImgUrl = $liveDomain . (str_starts_with($addImg, '/') ? '' : '/') . $addImg;
                    }
                    if ($addImgUrl !== $imageUrl && !in_array($addImgUrl, $additionalImages)) {
                        $additionalImages[] = $addImgUrl;
                    }
                }
            }

            $categoryName = $p->category ? $p->category->name : 'Ahşap Çerçeve';
            
            // Fiyatlandırma
            $priceFormatted = number_format($p->price, 2, '.', '') . ' TRY';
            $isDiscounted   = $p->original_price && $p->original_price > $p->price;
            $origPriceFormatted = $isDiscounted ? number_format($p->original_price, 2, '.', '') . ' TRY' : null;

            $availability = ($p->stock > 0) ? 'in_stock' : 'out_of_stock';
            
            $cleanDesc = !empty($p->description) 
                ? trim(preg_replace('/\s+/', ' ', strip_tags($p->description))) 
                : ($p->name . ' - Kişiye özel el yapımı masif ahşap çerçeve ve dekorasyon ürünü');

            $xml .= "    <item>\n";
            $xml .= "      <g:id>" . $p->id . "</g:id>\n";
            $xml .= "      <title><![CDATA[" . $p->name . "]]></title>\n";
            $xml .= "      <description><![CDATA[" . $cleanDesc . "]]></description>\n";
            $xml .= "      <link>" . htmlspecialchars($productUrl, ENT_XML1, 'UTF-8') . "</link>\n";
            $xml .= "      <g:image_link>" . htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8') . "</g:image_link>\n";
            
            foreach (array_slice($additionalImages, 0, 10) as $extraImg) {
                $xml .= "      <g:additional_image_link>" . htmlspecialchars($extraImg, ENT_XML1, 'UTF-8') . "</g:additional_image_link>\n";
            }

            if ($isDiscounted && $origPriceFormatted) {
                $xml .= "      <g:price>" . $origPriceFormatted . "</g:price>\n";
                $xml .= "      <g:sale_price>" . $priceFormatted . "</g:sale_price>\n";
            } else {
                $xml .= "      <g:price>" . $priceFormatted . "</g:price>\n";
            }

            $xml .= "      <g:availability>" . $availability . "</g:availability>\n";
            $xml .= "      <g:brand>Ahşap Evim</g:brand>\n";
            $xml .= "      <g:condition>new</g:condition>\n";
            $xml .= "      <g:google_product_category>632</g:google_product_category>\n";
            $xml .= "      <g:product_type><![CDATA[Ev & Yaşam > Ev Dekorasyon > " . $categoryName . "]]></g:product_type>\n";
            
            // GTIN / Barkod / Tanımlayıcı Kontrolü
            if (!empty($p->barcode)) {
                $xml .= "      <g:gtin>" . htmlspecialchars($p->barcode, ENT_XML1, 'UTF-8') . "</g:gtin>\n";
                $xml .= "      <g:identifier_exists>yes</g:identifier_exists>\n";
            } elseif (!empty($p->model_code)) {
                $xml .= "      <g:mpn>" . htmlspecialchars($p->model_code, ENT_XML1, 'UTF-8') . "</g:mpn>\n";
                $xml .= "      <g:identifier_exists>yes</g:identifier_exists>\n";
            } else {
                $xml .= "      <g:identifier_exists>no</g:identifier_exists>\n";
            }

            // Renk ve Boyut
            if (!empty($p->features['color'])) {
                $xml .= "      <g:color><![CDATA[" . $p->features['color'] . "]]></g:color>\n";
            }
            if (!empty($p->features['size'])) {
                $xml .= "      <g:size><![CDATA[" . $p->features['size'] . "]]></g:size>\n";
            }
            $xml .= "      <g:material>Ahşap</g:material>\n";

            // Kargo ve Teslimat Bilgisi
            $xml .= "      <g:shipping>\n";
            $xml .= "        <g:country>TR</g:country>\n";
            $xml .= "        <g:service>Standart Kargo</g:service>\n";
            $xml .= "        <g:price>0.00 TRY</g:price>\n";
            $xml .= "      </g:shipping>\n";
            $xml .= "      <g:min_handling_time>0</g:min_handling_time>\n";
            $xml .= "      <g:max_handling_time>1</g:max_handling_time>\n";
            $xml .= "      <g:min_transit_time>1</g:min_transit_time>\n";
            $xml .= "      <g:max_transit_time>3</g:max_transit_time>\n";

            $xml .= "    </item>\n";
        }

        $xml .= '  </channel>' . "\n";
        $xml .= '</rss>';

        return $xml;
    }
}
