<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\HomeBanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MigrateProductImagesToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'r2:migrate-images 
                            {--banners : Vitrin banner görsellerini de R2\'ye aktar} 
                            {--force : Zaten R2\'de olan görselleri de yeniden WebP\'ye dönüştürüp yönünü ve boyutunu optimize ederek yükle}
                            {--quality=82 : WebP sıkıştırma kalitesi (1-100 arası, varsayılan 82)}
                            {--max-dim=1200 : Maksimum piksel genişlik/yükseklik sınırı (varsayılan 1200)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Veritabanındaki ürün ve galeri görsellerini EXIF yönünü düzelterek, web için 1200px boyutlandırıp WebP olarak Cloudflare R2\'ye yükler.';

    private int $totalOriginalBytes = 0;
    private int $totalCompressedBytes = 0;
    private array $xmlProductMap = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $quality = (int) $this->option('quality');
        if ($quality < 1 || $quality > 100) {
            $quality = 82;
        }

        $maxDim = (int) ($this->option('max-dim') ?: 1200);

        $this->info("🚀 Cloudflare R2 WebP Optimizasyonu Başlatılıyor (Kalite: %{$quality}, Max Boyut: {$maxDim}px)...\n");

        $r2Url = rtrim(config('filesystems.disks.r2.url', ''), '/');
        if (empty($r2Url)) {
            $this->error("❌ Hata: config/filesystems.php veya .env içinde CLOUDFLARE_R2_URL tanımlı değil!");
            return Command::FAILURE;
        }

        // Orijinal dosya eşleştirmeleri için urunler.xml haritasını yükle
        $this->loadXmlMappings();
        if (!empty($this->xmlProductMap)) {
            $this->info("📄 urunler.xml içerisinden " . count($this->xmlProductMap) . " adet ürünün orijinal dosya haritası yüklendi.");
        }

        $products = Product::all();
        $this->info("📦 Toplam " . $products->count() . " ürün inceleniyor...");

        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $migratedMain = 0;
        $migratedGallery = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($products as $product) {
            $updated = false;
            $rawImage = $product->getRawOriginal('image');

            // 1. Ana Görsel WebP Sıkıştırma ve Taşıma
            if (!empty($rawImage)) {
                $isWebPOnR2 = (str_contains($rawImage, $r2Url) || str_contains($rawImage, 'r2.dev')) && str_ends_with(strtolower($rawImage), '.webp');
                
                if (!$isWebPOnR2 || $this->option('force')) {
                    // Orijinal yerel JPG dosyasını xml haritasından veya doğrudan bul
                    $preferredOriginal = $this->xmlProductMap[$product->id]['main'] ?? null;
                    $newUrl = $this->processAndUploadToR2($rawImage, 'products', 'product', $quality, $maxDim, $preferredOriginal);
                    if ($newUrl) {
                        $product->image = $newUrl;
                        $updated = true;
                        $migratedMain++;
                    } else {
                        $errors++;
                    }
                } elseif (str_contains($rawImage, 'r2.dev') && !str_starts_with($rawImage, $r2Url)) {
                    // Zaten R2 üzerinde WebP olarak mevcut, sadece yeni custom domaine güncelle
                    $product->image = preg_replace('#^https://[^/]+\.r2\.dev#', $r2Url, $rawImage);
                    $updated = true;
                    $migratedMain++;
                } else {
                    $skipped++;
                }
            }

            // 2. Galeri Görselleri WebP Sıkıştırma ve Taşıma
            $features = $product->features ?? [];
            $galleryImages = $features['images'] ?? [];

            if (is_array($galleryImages) && count($galleryImages) > 0) {
                $newGallery = [];
                $galleryChanged = false;

                foreach ($galleryImages as $idx => $gImg) {
                    if (empty($gImg)) continue;

                    $isWebPOnR2 = (str_contains($gImg, $r2Url) || str_contains($gImg, 'r2.dev')) && str_ends_with(strtolower($gImg), '.webp');
                    if (!$isWebPOnR2 || $this->option('force')) {
                        $preferredOriginal = $this->xmlProductMap[$product->id]['gallery'][$idx] ?? null;
                        $newUrl = $this->processAndUploadToR2($gImg, 'products', 'gal', $quality, $maxDim, $preferredOriginal);
                        if ($newUrl) {
                            $newGallery[] = $newUrl;
                            $galleryChanged = true;
                            $migratedGallery++;
                        } else {
                            $newGallery[] = $gImg;
                            $errors++;
                        }
                    } elseif (str_contains($gImg, 'r2.dev') && !str_starts_with($gImg, $r2Url)) {
                        $newGallery[] = preg_replace('#^https://[^/]+\.r2\.dev#', $r2Url, $gImg);
                        $galleryChanged = true;
                        $migratedGallery++;
                    } else {
                        $newGallery[] = $gImg;
                        $skipped++;
                    }
                }

                if ($galleryChanged) {
                    $features['images'] = $newGallery;
                    $product->features = $features;
                    $updated = true;
                }
            }

            if ($updated) {
                $product->saveQuietly();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // 3. Banner Görselleri
        $migratedBanners = 0;
        if ($this->option('banners')) {
            $banners = HomeBanner::all();
            $this->info("🖼️  Toplam " . $banners->count() . " anasayfa vitrin görseli inceleniyor...");

            foreach ($banners as $banner) {
                $rawBannerImg = $banner->image;
                if (!empty($rawBannerImg)) {
                    $isWebPOnR2 = (str_contains($rawBannerImg, $r2Url) || str_contains($rawBannerImg, 'r2.dev')) && str_ends_with(strtolower($rawBannerImg), '.webp');
                    if (!$isWebPOnR2 || $this->option('force')) {
                        $newUrl = $this->processAndUploadToR2($rawBannerImg, 'banners', 'banner', $quality, $maxDim);
                        if ($newUrl) {
                            $banner->image = $newUrl;
                            $banner->saveQuietly();
                            $migratedBanners++;
                        }
                    } elseif (str_contains($rawBannerImg, 'r2.dev') && !str_starts_with($rawBannerImg, $r2Url)) {
                        $banner->image = preg_replace('#^https://[^/]+\.r2\.dev#', $r2Url, $rawBannerImg);
                        $banner->saveQuietly();
                        $migratedBanners++;
                    }
                }
            }
        }

        $savedMB = ($this->totalOriginalBytes - $this->totalCompressedBytes) / (1024 * 1024);
        $savedPercent = $this->totalOriginalBytes > 0 ? round((($this->totalOriginalBytes - $this->totalCompressedBytes) / $this->totalOriginalBytes) * 100, 1) : 0;

        // Özet Rapor Tablosu
        $this->info("✅ WebP Sıkıştırma ve Yön Düzeltme İşlemi Başarıyla Tamamlandı!\n");
        $this->table(
            ['İşlem / İstatistik', 'Değer'],
            [
                ['WebP\'ye Dönüştürülen Ürün Ana Görselleri', $migratedMain . ' adet'],
                ['WebP\'ye Dönüştürülen Galeri Fotoğrafları', $migratedGallery . ' adet'],
                ['WebP\'ye Dönüştürülen Vitrin Bannerları', $migratedBanners . ' adet'],
                ['Zaten WebP Olan / Atlananlar', $skipped . ' adet'],
                ['Başarısız Dosyalar', $errors . ' adet'],
                ['Orijinal Toplam Boyut', number_format($this->totalOriginalBytes / (1024 * 1024), 2) . ' MB'],
                ['WebP Sıkıştırılmış Boyut', number_format($this->totalCompressedBytes / (1024 * 1024), 2) . ' MB'],
                ['Tasarruf Edilen Alan (Kazanç)', number_format(max(0, $savedMB), 2) . " MB (%{$savedPercent})"],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * urunler.xml dosyasından orijinal dosya yollarını eşleştirir.
     */
    private function loadXmlMappings(): void
    {
        $xmlPath = public_path('urunler.xml');
        if (!file_exists($xmlPath)) {
            return;
        }

        try {
            $xml = @simplexml_load_file($xmlPath);
            if (!$xml || !isset($xml->channel->item)) {
                return;
            }

            foreach ($xml->channel->item as $item) {
                $id = (int) $item->children('g', true)->id;
                $mainImg = (string) $item->children('g', true)->image_link;
                $gallery = [];
                foreach ($item->children('g', true)->additional_image_link as $add) {
                    $gallery[] = (string) $add;
                }

                $this->xmlProductMap[$id] = [
                    'main'    => $mainImg,
                    'gallery' => $gallery,
                ];
            }
        } catch (\Throwable $e) {
            // Hata olursa pas geç
        }
    }

    /**
     * Görseli yerelden veya uzaktan alır, EXIF Orientation'a göre döndürür, orantılı boyutlandırır, WebP formatında sıkıştırır ve R2'ye yükler.
     */
    private function processAndUploadToR2(string $source, string $folder, string $prefix, int $quality, int $maxDim = 1200, ?string $preferredOriginal = null): ?string
    {
        $rawContent = null;

        // Aday kaynaklar: Önce preferredOriginal (orijinal JPG), sonra source
        $candidates = [];
        if ($preferredOriginal) {
            $candidates[] = $preferredOriginal;
        }
        $candidates[] = $source;

        foreach ($candidates as $cand) {
            $cleanPath = ltrim(parse_url($cand, PHP_URL_PATH) ?? $cand, '/');
            $filename = basename($cleanPath);

            $possibleLocalPaths = [
                public_path($cleanPath),
                public_path('uploads/products/' . $filename),
                public_path('uploads/banners/' . $filename),
                public_path('uploads/' . $filename),
                public_path('storage/products/' . $filename),
                public_path('storage/' . $filename),
                storage_path('app/public/products/' . $filename),
                storage_path('app/public/' . $filename),
            ];

            foreach ($possibleLocalPaths as $localPath) {
                if (file_exists($localPath) && is_file($localPath)) {
                    $rawContent = file_get_contents($localPath);
                    break 2;
                }
            }
        }

        // Yerelde bulunamadıysa URL'den indir
        if ($rawContent === null) {
            foreach ($candidates as $cand) {
                if (str_starts_with($cand, 'http://') || str_starts_with($cand, 'https://')) {
                    try {
                        $response = Http::timeout(15)->get($cand);
                        if ($response->successful()) {
                            $rawContent = $response->body();
                            break;
                        }
                    } catch (\Throwable $e) {
                        // İndirme hatası
                    }
                }
            }
        }

        if ($rawContent === null || strlen($rawContent) === 0) {
            $this->warn("\n⚠️  Görsel içeriği bulunamadı: {$source}");
            return null;
        }

        $originalSize = strlen($rawContent);
        $this->totalOriginalBytes += $originalSize;

        // WebP Sıkıştırma + EXIF Yön Düzeltme + Max 1200px Boyutlandırma
        $compressed = $this->compressToWebP($rawContent, $quality, $maxDim);
        $finalContent = $compressed['content'];
        $extension = $compressed['extension'];
        $this->totalCompressedBytes += strlen($finalContent);

        // Cloudflare R2'ye Yükle (Cache-Control ve Public header ile)
        $newFilename = $prefix . '_' . time() . '_' . Str::random(10) . '.' . $extension;
        $targetPath = trim($folder, '/') . '/' . $newFilename;

        try {
            Storage::disk('r2')->put($targetPath, $finalContent, [
                'visibility' => 'public',
                'CacheControl' => 'public, max-age=31536000, immutable',
            ]);
            return Storage::disk('r2')->url($targetPath);
        } catch (\Throwable $e) {
            $this->error("\n❌ R2 yükleme hatası ({$source}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ham görsel baytlarını GD kütüphanesi ile EXIF yönünü düzelterek ve boyutlandırarak WebP formatına dönüştürür.
     */
    private function compressToWebP(string $binaryData, int $quality = 82, int $maxDim = 1200): array
    {
        if (!function_exists('imagewebp') || !function_exists('imagecreatefromstring')) {
            return ['content' => $binaryData, 'extension' => 'jpg'];
        }

        try {
            $image = @imagecreatefromstring($binaryData);
            if (!$image) {
                return ['content' => $binaryData, 'extension' => 'jpg'];
            }

            // 1. EXIF Orientation yön düzeltmesi (cep telefonu dikey çekimlerinin 90 derece dönmesini engeller)
            $image = $this->autoOrient($image, $binaryData);

            // 2. Orantılı yeniden boyutlandırma (Max 1200px)
            $image = $this->resizeIfNeeded($image, $maxDim);

            // 3. Saydamlık (Alpha Channel) desteği
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);

            ob_start();
            imagewebp($image, null, $quality);
            $webpData = ob_get_clean();
            imagedestroy($image);

            if (!empty($webpData) && strlen($webpData) > 0) {
                return ['content' => $webpData, 'extension' => 'webp'];
            }
        } catch (\Throwable $e) {
            // Hata durumunda orijinali döndür
        }

        return ['content' => $binaryData, 'extension' => 'jpg'];
    }

    /**
     * Görseli en-boy oranını bozmadan maksimum piksel sınırına göre orantılı küçültür.
     */
    private function resizeIfNeeded(\GdImage $image, ?int $maxDimension = 1200): \GdImage
    {
        if (!$maxDimension || $maxDimension <= 0) {
            return $image;
        }

        $origW = imagesx($image);
        $origH = imagesy($image);

        if ($origW <= $maxDimension && $origH <= $maxDimension) {
            return $image;
        }

        $ratio = min($maxDimension / $origW, $maxDimension / $origH);
        $newW = (int) round($origW * $ratio);
        $newH = (int) round($origH * $ratio);

        $resized = imagecreatetruecolor($newW, $newH);
        imagepalettetotruecolor($resized);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($image);

        return $resized;
    }

    /**
     * EXIF Orientation bilgisine göre GD görsel nesnesini otomatik döndürür / düzeltir.
     * Cep telefonu ile çekilen fotoğrafların WebP'ye dönüştürülürken sağa veya sola dönmesini engeller.
     */
    private function autoOrient(\GdImage $image, string $binaryData): \GdImage
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }

        try {
            $stream = fopen('php://memory', 'r+');
            if (!$stream) {
                return $image;
            }
            fwrite($stream, $binaryData);
            rewind($stream);
            $exif = @exif_read_data($stream);
            fclose($stream);

            $orientation = $exif['Orientation'] ?? null;
            if ($orientation) {
                switch ((int) $orientation) {
                    case 2:
                        imageflip($image, IMG_FLIP_HORIZONTAL);
                        break;
                    case 3: // 180 derece ters
                        $rotated = imagerotate($image, 180, 0);
                        if ($rotated !== false) {
                            imagedestroy($image);
                            $image = $rotated;
                        }
                        break;
                    case 4:
                        imageflip($image, IMG_FLIP_VERTICAL);
                        break;
                    case 5:
                        imageflip($image, IMG_FLIP_HORIZONTAL);
                        $rotated = imagerotate($image, 90, 0);
                        if ($rotated !== false) {
                            imagedestroy($image);
                            $image = $rotated;
                        }
                        break;
                    case 6: // 90 derece saat yönünde (CW) - telefon dik çekimi
                        $rotated = imagerotate($image, -90, 0);
                        if ($rotated !== false) {
                            imagedestroy($image);
                            $image = $rotated;
                        }
                        break;
                    case 7:
                        imageflip($image, IMG_FLIP_HORIZONTAL);
                        $rotated = imagerotate($image, -90, 0);
                        if ($rotated !== false) {
                            imagedestroy($image);
                            $image = $rotated;
                        }
                        break;
                    case 8: // 90 derece saat yönünün tersine (CCW) - 270 CW
                        $rotated = imagerotate($image, 90, 0);
                        if ($rotated !== false) {
                            imagedestroy($image);
                            $image = $rotated;
                        }
                        break;
                }
            }
        } catch (\Throwable $e) {
            // Hata olursa mevcut görseli koru
        }

        return $image;
    }
}
