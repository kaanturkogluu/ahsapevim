<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentLogService
{
    private const LINE_WIDTH = 95;

    /**
     * Iyzico Checkout Formu başlatıldığında loglar.
     */
    public static function logCheckoutInitialize(Order $order, bool $isSuccess, ?string $token = null, ?string $errorMessage = null, ?string $errorCode = null): void
    {
        $statusText = $isSuccess ? '✅ BAŞARILI (Token Alındı)' : '❌ BAŞARISIZ (Form Oluşturulamadı)';
        $items = self::formatOrderItems($order);

        $lines = [
            self::header("💳 IYZICO CHECKOUT FORMU BAŞLATILDI"),
            self::row("Sipariş No", "#{$order->id} (Takip Kodu: {$order->tracking_code})"),
            self::row("İşlem Sonucu", $statusText),
            self::row("Toplam Tutar", number_format($order->total_amount, 2, ',', '.') . ' TL'),
            self::row("Müşteri", "{$order->name} ({$order->email} | {$order->phone})"),
            self::row("Teslimat Adresi", ($order->city ? "{$order->city} / {$order->district} - " : '') . $order->address),
            self::row("Müşteri IP", request()->ip() ?: 'Bilinmiyor'),
        ];

        if ($token) {
            $lines[] = self::row("Iyzico Token", $token);
        }

        if (!$isSuccess) {
            if ($errorCode) {
                $lines[] = self::row("Hata Kodu", $errorCode);
            }
            $lines[] = self::row("Hata Açıklaması", $errorMessage ?: 'Bilinmeyen hata');
        }

        $lines[] = self::divider();
        $lines[] = "  Sepet Kalemleri:";
        foreach ($items as $itemStr) {
            $lines[] = "    • " . $itemStr;
        }

        $lines[] = self::footer();

        Log::channel('payment')->info(implode("\n", $lines));
    }

    /**
     * Iyzico callback ile başarılı ödeme tamamlandığında loglar.
     */
    public static function logPaymentSuccess(Order $order, array $paymentData, array $notifications = []): void
    {
        $paidPrice     = number_format($paymentData['paidPrice'] ?? $order->total_amount, 2, ',', '.') . ' TL';
        $payout        = number_format($paymentData['merchantPayoutAmount'] ?? 0, 2, ',', '.') . ' TL';
        $installment   = ($paymentData['installment'] ?? 1) > 1 
            ? "{$paymentData['installment']} Taksit" 
            : "Tek Çekim";
        $cardFamily    = $paymentData['cardFamily'] ?: 'Bilinmiyor';
        $cardLastFour  = $paymentData['cardLastFour'] ? "**** **** **** " . $paymentData['cardLastFour'] : 'Belirtilmedi';

        $lines = [
            self::header("✅ IYZICO ÖDEME BAŞARILI (3D SECURE ONAYLANDI)"),
            self::row("Sipariş No", "#{$order->id} (Takip Kodu: {$order->tracking_code})"),
            self::row("Iyzico Ödeme ID", (string) ($paymentData['paymentId'] ?? '-')),
            self::row("Ödenen Tutar", $paidPrice),
            self::row("Net Hakediş", $payout),
            self::row("Kart / Taksit", "{$cardFamily} | {$cardLastFour} | {$installment}"),
            self::row("Müşteri", "{$order->name} ({$order->email} | {$order->phone})"),
            self::row("Sipariş Durumu", "ÖDENDİ (paid)"),
        ];

        if (!empty($notifications)) {
            $lines[] = self::divider();
            $lines[] = "  Gönderilen Bildirimler:";
            foreach ($notifications as $key => $status) {
                $lines[] = "    • {$key}: {$status}";
            }
        }

        $lines[] = self::footer();

        Log::channel('payment')->info(implode("\n", $lines));
    }

    /**
     * Iyzico callback veya ödeme onayında hata alındığında loglar.
     */
    public static function logPaymentFailure(?Order $order, string $token, string $errorMessage, ?string $errorCode = null, ?string $errorGroup = null): void
    {
        $lines = [
            self::header("❌ IYZICO ÖDEME BAŞARISIZ / REDDEDİLDİ"),
        ];

        if ($order) {
            $lines[] = self::row("Sipariş No", "#{$order->id} (Takip Kodu: {$order->tracking_code})");
            $lines[] = self::row("Müşteri", "{$order->name} ({$order->email} | {$order->phone})");
            $lines[] = self::row("Sipariş Tutarı", number_format($order->total_amount, 2, ',', '.') . ' TL');
        } else {
            $lines[] = self::row("Sipariş", "Bulunamadı veya Eşleşmedi");
        }

        $lines[] = self::row("Iyzico Token", $token ?: '-');
        if ($errorCode) {
            $lines[] = self::row("Hata Kodu", $errorCode);
        }
        if ($errorGroup) {
            $lines[] = self::row("Hata Grubu", $errorGroup);
        }
        $lines[] = self::row("Hata Açıklaması", $errorMessage ?: 'Banka tarafından işlem reddedildi.');
        $lines[] = self::row("Sipariş Durumu", "BAŞARISIZ (failed)");
        $lines[] = self::footer();

        Log::channel('payment')->warning(implode("\n", $lines));
    }

    /**
     * Havale / EFT ile sipariş verildiğinde loglar.
     */
    public static function logEftOrder(Order $order): void
    {
        $items = self::formatOrderItems($order);

        $lines = [
            self::header("🏦 HAVALE / EFT SİPARİŞİ OLUŞTURULDU"),
            self::row("Sipariş No", "#{$order->id} (Takip Kodu: {$order->tracking_code})"),
            self::row("Toplam Tutar", number_format($order->total_amount, 2, ',', '.') . ' TL'),
            self::row("Ödeme Yöntemi", "Banka Havalesi / EFT (Halkbank - Mete Almaz)"),
            self::row("Müşteri", "{$order->name} ({$order->email} | {$order->phone})"),
            self::row("Teslimat Adresi", ($order->city ? "{$order->city} / {$order->district} - " : '') . $order->address),
            self::row("Sipariş Durumu", "BEKLEMEDE (pending)"),
            self::row("Müşteri IP", request()->ip() ?: 'Bilinmiyor'),
            self::divider(),
            "  Sepet Kalemleri:",
        ];

        foreach ($items as $itemStr) {
            $lines[] = "    • " . $itemStr;
        }

        $lines[] = self::footer();

        Log::channel('payment')->info(implode("\n", $lines));
    }

    private static function header(string $title): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $topBorder = str_repeat('=', self::LINE_WIDTH);
        $midBorder = str_repeat('-', self::LINE_WIDTH);
        return "\n{$topBorder}\n[{$timestamp}] {$title}\n{$midBorder}";
    }

    private static function divider(): string
    {
        return str_repeat('-', self::LINE_WIDTH);
    }

    private static function footer(): string
    {
        return str_repeat('=', self::LINE_WIDTH) . "\n";
    }

    private static function row(string $label, string $value): string
    {
        $prefix = "  " . $label;
        $width = 24;
        $currentLen = mb_strwidth($prefix, 'UTF-8');
        $padding = max(1, $width - $currentLen);
        $padded = $prefix . str_repeat(' ', $padding);
        return "{$padded}: {$value}";
    }

    private static function formatOrderItems(Order $order): array
    {
        $items = [];
        if (!$order->relationLoaded('items')) {
            $order->load('items.product');
        }

        foreach ($order->items as $item) {
            $pName = $item->product ? $item->product->name : 'Ahşap Ürün';
            $qty   = $item->quantity;
            $price = number_format($item->price * $qty, 2, ',', '.') . ' TL';
            $items[] = "[{$qty}x] {$pName} ({$price})";
        }

        return $items;
    }
}
