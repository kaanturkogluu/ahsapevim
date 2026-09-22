<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class R2StorageService
{
    /**
     * Upload an uploaded file instance to Cloudflare R2 after compressing to WebP.
     * Returns the full public URL of the uploaded file.
     *
     * @param UploadedFile $file
     * @param string $folder (e.g. 'products', 'customizations', 'banners')
     * @param string|null $customPrefix
     * @param int $quality (1-100, default 82)
     * @param int|null $maxDimension (e.g. 1200, or null to keep original size)
     * @return string
     */
    public static function upload(UploadedFile $file, string $folder = 'uploads', ?string $customPrefix = null, int $quality = 82, ?int $maxDimension = 1200): string
    {
        $rawBinary = file_get_contents($file->getRealPath());
        $compressed = self::convertToWebP($rawBinary, $file->getClientOriginalExtension(), $quality, $maxDimension);

        $prefix = $customPrefix ? ($customPrefix . '_') : '';
        $filename = $prefix . time() . '_' . Str::random(10) . '.' . $compressed['extension'];
        $path = trim($folder, '/') . '/' . $filename;

        try {
            Storage::disk('r2')->put($path, $compressed['content'], [
                'visibility' => 'public',
                'CacheControl' => 'public, max-age=31536000, immutable',
            ]);
            return Storage::disk('r2')->url($path);
        } catch (\Throwable $e) {
            Log::error('Cloudflare R2 Upload Failed, falling back to local: ' . $e->getMessage());
            
            // Fallback to local storage if R2 fails
            $localDest = public_path('uploads/' . trim($folder, '/'));
            if (!file_exists($localDest)) {
                mkdir($localDest, 0755, true);
            }
            file_put_contents($localDest . '/' . $filename, $compressed['content']);
            return url('/uploads/' . trim($folder, '/') . '/' . $filename);
        }
    }

    /**
     * Upload raw contents (e.g. generated images or base64) to R2 after WebP compression.
     *
     * @param string $contents
     * @param string $folder
     * @param string $extension
     * @param string|null $customPrefix
     * @param int $quality
     * @param int|null $maxDimension
     * @return string
     */
    public static function uploadContent(string $contents, string $folder = 'uploads', string $extension = 'jpg', ?string $customPrefix = null, int $quality = 82, ?int $maxDimension = 1200): string
    {
        $compressed = self::convertToWebP($contents, $extension, $quality, $maxDimension);

        $prefix = $customPrefix ? ($customPrefix . '_') : '';
        $filename = $prefix . time() . '_' . Str::random(10) . '.' . $compressed['extension'];
        $path = trim($folder, '/') . '/' . $filename;

        try {
            Storage::disk('r2')->put($path, $compressed['content'], [
                'visibility' => 'public',
                'CacheControl' => 'public, max-age=31536000, immutable',
            ]);
            return Storage::disk('r2')->url($path);
        } catch (\Throwable $e) {
            Log::error('Cloudflare R2 UploadContent Failed: ' . $e->getMessage());
            
            $localDest = public_path('uploads/' . trim($folder, '/'));
            if (!file_exists($localDest)) {
                mkdir($localDest, 0755, true);
            }
            file_put_contents($localDest . '/' . $filename, $compressed['content']);
            return url('/uploads/' . trim($folder, '/') . '/' . $filename);
        }
    }

    /**
     * Compresses, reorients, resizes, and converts image binary data to WebP.
     *
     * @param string $binaryData
     * @param string $fallbackExt
     * @param int $quality
     * @param int|null $maxDimension (Maksimum genişlik/yükseklik sınırı, orantılı küçültülür)
     * @return array{content: string, extension: string}
     */
    public static function convertToWebP(string $binaryData, string $fallbackExt = 'jpg', int $quality = 82, ?int $maxDimension = 1200): array
    {
        if (!function_exists('imagewebp') || !function_exists('imagecreatefromstring')) {
            return ['content' => $binaryData, 'extension' => strtolower($fallbackExt ?: 'jpg')];
        }

        try {
            $image = @imagecreatefromstring($binaryData);
            if (!$image) {
                return ['content' => $binaryData, 'extension' => strtolower($fallbackExt ?: 'jpg')];
            }

            // 1. EXIF Orientation düzeltmesi (cep telefonu dikey çekimlerinin 90 derece dönmesini engeller)
            $image = self::autoOrient($image, $binaryData);

            // 2. Orantılı yeniden boyutlandırma (Aşırı yüksek çözünürlükleri web için 1200px'e sınırlar)
            $image = self::resizeIfNeeded($image, $maxDimension);

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
            Log::warning('WebP conversion failed, using original: ' . $e->getMessage());
        }

        return ['content' => $binaryData, 'extension' => strtolower($fallbackExt ?: 'jpg')];
    }

    /**
     * Görseli en-boy oranını bozmadan maksimum piksel sınırına göre orantılı küçültür.
     *
     * @param \GdImage $image
     * @param int|null $maxDimension
     * @return \GdImage
     */
    public static function resizeIfNeeded(\GdImage $image, ?int $maxDimension = 1200): \GdImage
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
     * Cep telefonu ile çekilen fotoğrafların WebP'ye dönüştürülürken sağa/sola dönmesini engeller.
     *
     * @param \GdImage $image
     * @param string $binaryData
     * @return \GdImage
     */
    public static function autoOrient(\GdImage $image, string $binaryData): \GdImage
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
                    case 6: // 90 derece saat yönünde (en yaygın telefon dikey çekimi)
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
                    case 8: // 90 derece saat yönünün tersine (270 CW)
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

    /**
     * Delete a file from Cloudflare R2 given its URL or relative path.
     *
     * @param string|null $urlOrPath
     * @return bool
     */
    public static function delete(?string $urlOrPath): bool
    {
        if (empty($urlOrPath)) {
            return false;
        }

        try {
            $r2Url = rtrim(config('filesystems.disks.r2.url', ''), '/');
            $path = $urlOrPath;

            if ($r2Url && str_starts_with($urlOrPath, $r2Url)) {
                $path = ltrim(substr($urlOrPath, strlen($r2Url)), '/');
            } elseif (str_starts_with($urlOrPath, 'http://') || str_starts_with($urlOrPath, 'https://')) {
                $parsed = parse_url($urlOrPath, PHP_URL_PATH);
                $path = ltrim($parsed ?: '', '/');
            }

            if (Storage::disk('r2')->exists($path)) {
                return Storage::disk('r2')->delete($path);
            }

            // Also check and delete local fallback if exists
            $localPath = public_path(ltrim($path, '/'));
            if (file_exists($localPath) && is_file($localPath)) {
                @unlink($localPath);
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Cloudflare R2 Delete Failed: ' . $e->getMessage());
            return false;
        }
    }
}
