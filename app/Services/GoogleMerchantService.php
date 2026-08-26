<?php

namespace App\Services;

use App\Models\Product;
use Google\Client;
use Google\Service\ShoppingContent;
use Google\Service\ShoppingContent\Product as GoogleProduct;
use Google\Service\ShoppingContent\Price;
use Google\Service\ShoppingContent\ProductShipping;
use Google\Service\ShoppingContent\ProductsCustomBatchRequest;
use Google\Service\ShoppingContent\ProductsCustomBatchRequestEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GoogleMerchantService
{
    protected Client $client;
    protected ShoppingContent $service;
    protected string $merchantId;
    protected string $country;
    protected string $currency;
    protected string $language;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->merchantId = (string) config('merchant.merchant_id');
        $this->country    = config('merchant.country', 'TR');
        $this->currency   = config('merchant.currency', 'TRY');
        $this->language   = config('merchant.language', 'tr');

        $this->client = new Client();
        $this->client->setApplicationName('Ahsap Evim Merchant');
        $this->client->setScopes(config('merchant.scopes'));

        $jsonValue = config('merchant.service_account_json');

        if (empty($jsonValue)) {
            throw new \RuntimeException(
                'GOOGLE_SERVICE_ACCOUNT_JSON .env değişkeni ayarlanmamış.'
            );
        }

        // Eğer tam yol verilmişse direkt kullan, sadece dosya adı ise storage/app/ altında ara
        if (file_exists($jsonValue)) {
            $jsonPath = $jsonValue;
        } else {
            $jsonPath = storage_path('app/' . ltrim($jsonValue, '/\\'));
        }

        if (!file_exists($jsonPath)) {
            throw new \RuntimeException(
                "Service Account JSON dosyası bulunamadı: {$jsonPath}\n" .
                "Dosyayı storage/app/ klasörüne yükleyin ve .env'de dosya adını yazın:\n" .
                "GOOGLE_SERVICE_ACCOUNT_JSON=google-service-account.json"
            );
        }

        $this->client->setAuthConfig($jsonPath);
        $this->service = new ShoppingContent($this->client);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Tekil Ürün İşlemleri
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Merchant Center'a yeni ürün ekle / güncelle (upsert).
     */
    public function insertProduct(Product $product): array
    {
        try {
            $gProduct = $this->buildMerchantProduct($product);
            $result   = $this->service->products->insert($this->merchantId, $gProduct);

            Log::info('Merchant: ürün eklendi', [
                'product_id'     => $product->id,
                'merchant_product_id' => $result->getId(),
            ]);

            return [
                'success'    => true,
                'product_id' => $result->getId(),
                'message'    => 'Ürün başarıyla Merchant Center\'a gönderildi.',
            ];
        } catch (\Exception $e) {
            Log::error('Merchant: ürün eklenemedi', [
                'product_id' => $product->id,
                'error'      => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Merchant Center'dan ürün sil.
     */
    public function deleteProduct(Product $product): array
    {
        try {
            $productId = $this->buildMerchantProductId($product->id);
            $this->service->products->delete($this->merchantId, $productId);

            Log::info('Merchant: ürün silindi', ['product_id' => $product->id]);

            return ['success' => true, 'message' => 'Ürün Merchant Center\'dan silindi.'];
        } catch (\Exception $e) {
            Log::error('Merchant: ürün silinemedi', [
                'product_id' => $product->id,
                'error'      => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Merchant Center'daki ürün durumunu getir.
     */
    public function getProductStatus(Product $product): ?array
    {
        try {
            $productId = $this->buildMerchantProductId($product->id);
            $status    = $this->service->productstatuses->get($this->merchantId, $productId);

            return [
                'product_id'         => $productId,
                'title'              => $status->getTitle(),
                'creation_date'      => $status->getCreationDate(),
                'last_update_date'   => $status->getLastUpdateDate(),
                'google_expiration_date' => $status->getGoogleExpirationDate(),
                'destinations'       => $status->getDestinationStatuses(),
                'item_level_issues'  => $status->getItemLevelIssues() ?? [],
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Toplu Senkronizasyon
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Tüm aktif ürünleri Merchant Center'a senkronize et.
     */
    public function syncAllProducts(): array
    {
        $products = Product::where('is_active', true)->with('category')->get();
        return $this->batchSync($products);
    }

    /**
     * Bir Collection ürünü batch olarak Merchant Center'a gönder.
     * Batch API: tek seferde 1000 ürüne kadar destekler.
     */
    public function batchSync(Collection $products): array
    {
        $results  = ['success' => 0, 'failed' => 0, 'errors' => []];
        $chunks   = $products->chunk(100); // 100'erli gruplar halinde gönder

        foreach ($chunks as $chunk) {
            $batchRequest = new ProductsCustomBatchRequest();
            $entries      = [];
            $batchIndex   = 1;

            foreach ($chunk as $product) {
                if (!$product->image) {
                    $results['failed']++;
                    $results['errors'][] = "Ürün #{$product->id} ({$product->name}): Görsel eksik, Merchant Center gerektiriyor.";
                    continue;
                }

                $entry = new ProductsCustomBatchRequestEntry();
                $entry->setBatchId($batchIndex++);
                $entry->setMerchantId($this->merchantId);
                $entry->setMethod('insert');
                $entry->setProduct($this->buildMerchantProduct($product));

                $entries[] = $entry;
            }

            if (empty($entries)) {
                continue;
            }

            $batchRequest->setEntries($entries);

            try {
                $response = $this->service->products->custombatch($batchRequest);

                foreach ($response->getEntries() as $entry) {
                    if ($entry->getErrors()) {
                        $results['failed']++;
                        foreach ($entry->getErrors()->getErrors() as $err) {
                            $results['errors'][] = "Batch #{$entry->getBatchId()}: {$err->getMessage()}";
                        }
                    } else {
                        $results['success']++;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Merchant: batch sync hatası', ['error' => $e->getMessage()]);
                $results['failed'] += count($entries);
                $results['errors'][] = 'Batch isteği başarısız: ' . $e->getMessage();
            }
        }

        Log::info('Merchant: batch sync tamamlandı', $results);
        return $results;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API Bağlantı Testi
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Merchant Center bağlantısını test et.
     */
    public function testConnection(): array
    {
        try {
            $account = $this->service->accounts->get($this->merchantId, $this->merchantId);
            return [
                'success'  => true,
                'name'     => $account->getName(),
                'website'  => $account->getWebsiteUrl(),
                'message'  => 'Bağlantı başarılı!',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Yardımcı Metodlar
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Laravel Product modelini Google Merchant Product nesnesine dönüştür.
     */
    public function buildMerchantProduct(Product $product): GoogleProduct
    {
        $gProduct = new GoogleProduct();

        // Zorunlu alanlar
        $gProduct->setOfferId((string) $product->id);
        $gProduct->setTitle($product->name);
        $gProduct->setLink($product->url);
        $gProduct->setDescription($this->buildDescription($product));
        $gProduct->setCondition(config('merchant.condition', 'new'));
        $gProduct->setAvailability($product->stock > 0 ? 'in stock' : 'out of stock');
        $gProduct->setContentLanguage($this->language);
        $gProduct->setTargetCountry($this->country);
        $gProduct->setChannel('online');

        // Görsel
        // getRawImageAttribute → işlenmemiş (url() eklenmemiş) yol
        $imageRaw = $product->attributes['image'] ?? null;
        if ($imageRaw) {
            $imageUrl = str_starts_with($imageRaw, 'http') ? $imageRaw : url($imageRaw);
            $gProduct->setImageLink($imageUrl);
        }

        // Ek görseller (gallery)
        $galleryUrls = $product->gallery_urls ?? [];
        if (!empty($galleryUrls)) {
            $gProduct->setAdditionalImageLinks(array_slice($galleryUrls, 0, 10));
        }

        // Fiyat
        $price = new Price();
        $price->setValue(number_format((float) $product->price, 2, '.', ''));
        $price->setCurrency($this->currency);
        $gProduct->setPrice($price);

        // İndirimli fiyat
        if ($product->original_price && $product->original_price > $product->price) {
            $salePrice = new Price();
            $salePrice->setValue(number_format((float) $product->price, 2, '.', ''));
            $salePrice->setCurrency($this->currency);

            $originalPrice = new Price();
            $originalPrice->setValue(number_format((float) $product->original_price, 2, '.', ''));
            $originalPrice->setCurrency($this->currency);

            $gProduct->setPrice($originalPrice);
            $gProduct->setSalePrice($salePrice);
        }

        // Marka
        $gProduct->setBrand(config('merchant.brand', 'Ahsap Evim Manisa'));

        // Google Ürün Kategorisi
        $categorySlug = optional($product->category)->slug ?? 'default';
        $categoryMap  = config('merchant.category_map', []);
        $googleCat    = $categoryMap[$categorySlug] ?? $categoryMap['default'] ?? '500044';
        $gProduct->setGoogleProductCategory($googleCat);

        // Ürün tipi (kendi kategori adınız)
        if ($product->category) {
            $gProduct->setProductTypes([$product->category->name]);
        }

        // Özel ürün — barkod/GTIN yok
        $gProduct->setIdentifierExists(false);

        // Kargo bilgisi
        $shipping = new ProductShipping();
        $shipping->setCountry($this->country);

        $shippingPrice = new Price();
        $shippingPrice->setValue((string) config('merchant.shipping_price', '0'));
        $shippingPrice->setCurrency($this->currency);
        $shipping->setPrice($shippingPrice);
        $shipping->setMinHandlingTime(config('merchant.shipping_min_days', 3));
        $shipping->setMaxHandlingTime(config('merchant.shipping_max_days', 7));

        $gProduct->setShipping([$shipping]);

        // Renk / Beden (varsa)
        $features = $product->features ?? [];
        if (!empty($features['color'])) {
            $gProduct->setColor($features['color']);
        }
        if (!empty($features['size'])) {
            $gProduct->setSizes([$features['size']]);
        }

        return $gProduct;
    }

    /**
     * Merchant Center'da kullanılan ürün ID formatını oluştur.
     * Format: online:tr:TR:{offerId}
     */
    public function buildMerchantProductId(int $productId): string
    {
        return "online:{$this->language}:{$this->country}:{$productId}";
    }

    /**
     * Merchant Center için açıklama metni hazırla.
     * HTML tagları temizlenir, karakter limiti uygulanır.
     */
    private function buildDescription(Product $product): string
    {
        $desc = $product->description ?? $product->name;
        $desc = strip_tags($desc);
        $desc = preg_replace('/\s+/', ' ', $desc);
        $desc = trim($desc);

        // GMC: max 5000 karakter
        if (mb_strlen($desc) > 5000) {
            $desc = mb_substr($desc, 0, 4997) . '...';
        }

        // Minimum açıklama uzunluğu yok ama çok kısa olursa ürün adını ekle
        if (mb_strlen($desc) < 20) {
            $desc = $product->name . ' — ' . $desc;
        }

        return $desc;
    }
}
