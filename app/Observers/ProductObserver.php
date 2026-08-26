<?php

namespace App\Observers;

use App\Jobs\SyncProductToMerchant;
use App\Models\Product;

class ProductObserver
{
    /**
     * Ürün oluşturulduğunda Merchant Center'a gönder.
     * (sadece aktif ürünler gönderilir)
     */
    public function created(Product $product): void
    {
        if ($product->is_active && $product->image) {
            SyncProductToMerchant::dispatch($product, 'insert')
                ->delay(now()->addSeconds(5)); // Kayıt tamamlansın diye kısa gecikme
        }
    }

    /**
     * Ürün güncellendiğinde Merchant Center'ı güncelle.
     */
    public function updated(Product $product): void
    {
        if ($product->is_active && $product->image) {
            // Aktif & görseli var → güncelle/ekle
            SyncProductToMerchant::dispatch($product, 'insert');
        } elseif (!$product->is_active) {
            // Pasif yapılmışsa Merchant Center'dan sil
            SyncProductToMerchant::dispatch($product, 'delete');
        }
    }

    /**
     * Ürün silindiğinde Merchant Center'dan da sil.
     */
    public function deleted(Product $product): void
    {
        SyncProductToMerchant::dispatch($product, 'delete');
    }
}
