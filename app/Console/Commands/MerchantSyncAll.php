<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\GoogleMerchantService;
use Illuminate\Console\Command;

class MerchantSyncAll extends Command
{
    protected $signature   = 'merchant:sync-all {--dry-run : Gerçek gönderim yapmadan çalıştır}';
    protected $description = 'Tüm aktif ürünleri Google Merchant Center\'a senkronize eder';

    public function handle(GoogleMerchantService $merchantService): int
    {
        $this->info('🔄 Google Merchant Center — Senkronizasyon Başlıyor...');

        // Bağlantı testi
        $this->line('  ↳ Bağlantı test ediliyor...');
        $status = $merchantService->testConnection();

        if (!$status['success']) {
            $this->error('  ✗ Bağlantı başarısız: ' . $status['message']);
            $this->line('');
            $this->line('  .env dosyanızda şu değişkenlerin doğru ayarlandığından emin olun:');
            $this->line('    GOOGLE_MERCHANT_ID=...');
            $this->line('    GOOGLE_SERVICE_ACCOUNT_JSON=...');
            return Command::FAILURE;
        }

        $this->info('  ✔ Bağlantı başarılı: ' . ($status['name'] ?? 'Merchant Center'));

        $products = Product::where('is_active', true)->with('category')->get();
        $this->line("  ↳ {$products->count()} aktif ürün bulundu.");

        if ($this->option('dry-run')) {
            $this->warn('  ⚠ Dry-run modu: Gerçek gönderim yapılmayacak.');
            foreach ($products as $p) {
                $hasImage = !empty($p->attributes['image']);
                $icon     = $hasImage ? '✔' : '✗';
                $this->line("    {$icon} [{$p->id}] {$p->name}" . ($hasImage ? '' : ' (görsel eksik)'));
            }
            return Command::SUCCESS;
        }

        $this->line('  ↳ Senkronizasyon başlatılıyor...');
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        $results = $merchantService->syncAllProducts();
        $bar->finish();

        $this->line('');
        $this->info("  ✔ Başarılı: {$results['success']}");

        if ($results['failed'] > 0) {
            $this->warn("  ✗ Başarısız: {$results['failed']}");
            foreach ($results['errors'] as $err) {
                $this->error("    - {$err}");
            }
        }

        $this->line('');
        $this->info('🎉 Senkronizasyon tamamlandı!');

        return $results['failed'] === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
