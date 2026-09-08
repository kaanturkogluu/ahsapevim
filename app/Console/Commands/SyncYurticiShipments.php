<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\YurticiKargoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncYurticiShipments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yurtici:sync {--force-all : Teslim edilenler dahil tüm siparişleri tara}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Yurtiçi Kargo gönderilerini sorgula, resmi takip kodlarını sisteme çek ve müşteriye SMS ilet.';

    /**
     * Execute the console command.
     */
    public function handle(YurticiKargoService $yurticiService): int
    {
        $this->info('--- Yurtiçi Kargo Otomatik Durum ve Takip Kodu Senkronizasyonu Başlatıldı ---');

        $query = Order::query()
            ->whereNotNull('yurtici_cargo_key');

        if (!$this->option('force-all')) {
            $query->where(function ($q) {
                $q->whereNull('yurtici_status')
                  ->orWhereNotIn('yurtici_status', ['delivered', 'cancelled']);
            });
        }

        $orders = $query->orderBy('id', 'desc')->get();

        if ($orders->isEmpty()) {
            $this->info('Sorgulanacak aktif Yurtiçi Kargo gönderisi bulunamadı.');
            return Command::SUCCESS;
        }

        $this->info("Toplam {$orders->count()} adet aktif kargo siparişi sorgulanıyor...");

        $successCount = 0;
        $smsSentCount = 0;
        $errorCount   = 0;

        foreach ($orders as $order) {
            $this->line("-> Sipariş #{$order->id} (CargoKey: {$order->yurtici_cargo_key}) sorgulanıyor...");

            try {
                $result = $yurticiService->queryShipment($order);

                if (!$result['success']) {
                    $this->warn("   Hata: " . ($result['message'] ?? 'Bilinmeyen yanıt'));
                    $errorCount++;
                    // Yurtiçi rate-limit'e takılmamak için kısa bekleme
                    usleep(500000); // 0.5 saniye
                    continue;
                }

                $docId = $result['docId'] ?? '';
                $opStatus = $result['operationStatus'] ?? '';
                $statusText = $result['statusText'] ?? $result['cargoEvent'] ?? 'İşlemde';

                $this->info("   Durum: [{$opStatus}] {$statusText}");

                // Eğer resmi 12 haneli Yurtiçi Takip Numarası (docId) oluşmuşsa
                if (!empty($docId)) {
                    $this->info("   Resmi Takip No: {$docId}");

                    // Müşteriye SMS henüz gönderilmediyse gönder
                    if (empty($order->cargo_sms_sent_at)) {
                        $sent = $yurticiService->sendOfficialTrackingSms($order, $docId);
                        if ($sent) {
                            $smsSentCount++;
                            $this->info("   ✓ Müşteriye resmi takip no ile SMS bildirimi gönderildi.");
                        }
                    }
                }

                $successCount++;
            } catch (\Throwable $e) {
                $this->error("   İstisna Hatası: " . $e->getMessage());
                Log::error("Yurtiçi cron sync hatası (Sipariş #{$order->id}): " . $e->getMessage());
                $errorCount++;
            }

            // Rate-limiting koruması: her sorgu arasında 1 saniye bekle
            sleep(1);
        }

        $this->info("--- Senkronizasyon Tamamlandı ---");
        $this->info("Başarılı: {$successCount} | Yeni SMS Gönderilen: {$smsSentCount} | Hatalı: {$errorCount}");

        return Command::SUCCESS;
    }
}
