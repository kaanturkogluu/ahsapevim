<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NetgsmService
{
    protected $usercode;
    protected $password;
    protected $header;

    /**
     * Netgsm REST v2 JSON POST endpoint
     * @see https://www.netgsm.com.tr/dokuman/#json-post-rapor
     */
    protected $apiUrl = 'https://api.netgsm.com.tr/sms/rest/v2/send';

    public function __construct()
    {
        $this->usercode = config('services.netgsm.usercode');
        $this->password = config('services.netgsm.password');
        $this->header   = config('services.netgsm.header');
    }

    /**
     * Telefon numarasını Netgsm formatına çevirir (5XXXXXXXXX — 10 hane)
     * Örnek giriş: 05321234567, +905321234567, 90532...
     *
     * @param string $phone
     * @return string|null 10 haneli temiz numara ya da null (geçersizse)
     */
    public function formatPhone(string $phone): ?string
    {
        // Sadece rakamları al
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // +90 veya 90 ile başlıyorsa at (12 hane → 10)
        if (str_starts_with($clean, '90') && strlen($clean) === 12) {
            $clean = substr($clean, 2);
        }

        // Başında 0 varsa at (11 hane → 10)
        if (str_starts_with($clean, '0') && strlen($clean) === 11) {
            $clean = substr($clean, 1);
        }

        // 10 hane ve 5 ile başlamalı
        if (strlen($clean) !== 10 || !str_starts_with($clean, '5')) {
            return null;
        }

        return $clean;
    }

    /**
     * Netgsm REST v2 JSON POST API ile SMS gönderir ve SmsLog kaydeder.
     *
     * @param string      $phone   Alıcı telefon numarası
     * @param string      $message SMS metni
     * @param int|null    $orderId İlişkili sipariş ID
     * @param string      $type    'automated' | 'manual'
     * @return bool
     */
    public function sendSms(string $phone, string $message, ?int $orderId = null, string $type = 'automated'): bool
    {
        // Kimlik bilgisi kontrolü
        if (empty($this->usercode) || empty($this->password)) {
            Log::warning('Netgsm SMS credentials not configured in .env. Skipping SMS.');
            SmsLog::create([
                'order_id'     => $orderId,
                'to_phone'     => $phone,
                'message'      => $message,
                'status'       => 'failed',
                'error_message'=> 'Netgsm kullanıcı adı veya şifresi .env dosyasında yapılandırılmamış.',
                'response_code'=> 'CONFIG_ERROR',
                'type'         => $type,
            ]);
            return false;
        }

        // Telefon formatla
        $cleanPhone = $this->formatPhone($phone);
        if (!$cleanPhone) {
            Log::warning("Netgsm: Geçersiz telefon numarası formatı: {$phone}");
            SmsLog::create([
                'order_id'     => $orderId,
                'to_phone'     => $phone,
                'message'      => $message,
                'status'       => 'failed',
                'error_message'=> "Geçersiz telefon numarası formatı: {$phone}",
                'response_code'=> 'INVALID_PHONE',
                'type'         => $type,
            ]);
            return false;
        }

        try {
            // Netgsm REST v2 — JSON POST, Basic Auth
            $response = Http::withBasicAuth($this->usercode, $this->password)
                ->timeout(15)
                ->post($this->apiUrl, [
                    'msgheader' => $this->header,
                    'encoding'  => 'TR',
                    'iysfilter' => '0',
                    'appname'   => config('app.name', 'AhsapEvim'),
                    'messages'  => [
                        [
                            'msg' => $message,
                            'no'  => $cleanPhone,
                        ],
                    ],
                ]);

            $json         = $response->json();
            $responseCode = $json['code']        ?? 'UNKNOWN';
            $jobId        = $json['jobid']        ?? null;
            $description  = $json['description'] ?? null;

            // Kod "00" → başarılı
            $isSuccess = ($responseCode === '00');

            $errorDetail = null;
            if (!$isSuccess) {
                $errorDetail = match ((string) $responseCode) {
                    '01'  => 'Mesaj planlama başlangıç tarihi hatası (Kod: 01)',
                    '02'  => 'Mesaj bitiş tarihi hatası (Kod: 02)',
                    '20'  => 'Mesaj metni hatası veya karakter limiti aşıldı (Kod: 20)',
                    '30'  => 'Geçersiz kullanıcı adı / şifre veya API izni yok (Kod: 30)',
                    '40'  => 'Mesaj başlığı (msgheader) sistemde tanımlı değil (Kod: 40)',
                    '50'  => 'IYS kontrollü gönderim hesabınız için aktif değil (Kod: 50)',
                    '51'  => 'IYS marka bilgisi bulunamadı (Kod: 51)',
                    '70'  => 'Geçersiz parametre veya eksik zorunlu alan (Kod: 70)',
                    '80'  => 'Gönderim limiti aşıldı (Kod: 80)',
                    '85'  => 'Spam engeli: Aynı numaraya 1 dakikada 20'den fazla görev oluşturulamaz (Kod: 85)',
                    default => 'Netgsm Servis Hatası — Kod: ' . $responseCode . ($description ? " ({$description})" : ''),
                };
            }

            SmsLog::create([
                'order_id'     => $orderId,
                'to_phone'     => $phone,
                'message'      => $message,
                'status'       => $isSuccess ? 'success' : 'failed',
                'error_message'=> $errorDetail,
                'response_code'=> $isSuccess ? ($jobId ?? $responseCode) : $responseCode,
                'type'         => $type,
            ]);

            if ($isSuccess) {
                Log::info("Netgsm SMS gönderildi — Tel: {$cleanPhone}, JobID: {$jobId}");
            } else {
                Log::warning("Netgsm SMS başarısız — Tel: {$cleanPhone}, Kod: {$responseCode}, Açıklama: {$description}");
            }

            return $isSuccess;

        } catch (\Exception $e) {
            Log::error('Netgsm SMS Exception: ' . $e->getMessage());
            SmsLog::create([
                'order_id'     => $orderId,
                'to_phone'     => $phone,
                'message'      => $message,
                'status'       => 'failed',
                'error_message'=> 'Sunucu SMS Bağlantı Hatası: ' . $e->getMessage(),
                'response_code'=> 'EXCEPTION',
                'type'         => $type,
            ]);
            return false;
        }
    }
}
