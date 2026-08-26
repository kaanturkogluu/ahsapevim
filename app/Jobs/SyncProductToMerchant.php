<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\GoogleMerchantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncProductToMerchant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string 'insert' | 'delete'
     */
    public string $action;

    public Product $product;

    /**
     * Kaç kez yeniden denensin (API geçici hataları için)
     */
    public int $tries = 3;

    /**
     * Deneme aralığı (saniye)
     */
    public int $backoff = 30;

    /**
     * @param Product $product
     * @param string  $action  'insert' veya 'delete'
     */
    public function __construct(Product $product, string $action = 'insert')
    {
        $this->product = $product;
        $this->action  = $action;
        $this->onQueue('merchant'); // Ayrı bir queue kanalına gönder
    }

    /**
     * Job'u çalıştır.
     */
    public function handle(GoogleMerchantService $merchantService): void
    {
        try {
            if ($this->action === 'delete') {
                $result = $merchantService->deleteProduct($this->product);
            } else {
                $result = $merchantService->insertProduct($this->product);
            }

            if (!$result['success']) {
                Log::warning('Merchant Job başarısız', [
                    'action'     => $this->action,
                    'product_id' => $this->product->id,
                    'message'    => $result['message'],
                ]);
                $this->fail(new \RuntimeException($result['message']));
            }
        } catch (\Exception $e) {
            Log::error('Merchant Job hatası', [
                'action'     => $this->action,
                'product_id' => $this->product->id,
                'error'      => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Tüm denemeler başarısız olunca çağrılır.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Merchant Job kalıcı olarak başarısız', [
            'action'     => $this->action,
            'product_id' => $this->product->id,
            'error'      => $exception->getMessage(),
        ]);
    }
}
