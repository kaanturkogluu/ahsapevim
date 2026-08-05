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
    protected $apiUrl = 'https://api.netgsm.com.tr/sms/send/get/';

    public function __construct()
    {
        $this->usercode = config('services.netgsm.usercode');
        $this->password = config('services.netgsm.password');
        $this->header   = config('services.netgsm.header');
    }

    /**
     * Send an SMS via Netgsm GET API and record SmsLog
     * 
     * @param string $phone
     * @param string $message
     * @param int|null $orderId
     * @param string $type ('automated'|'manual')
     * @return bool
     */
    public function sendSms($phone, $message, $orderId = null, $type = 'automated')
    {
        if (empty($this->usercode) || empty($this->password)) {
            Log::warning('Netgsm SMS credentials not configured in .env. Skipping SMS.');
            SmsLog::create([
                'order_id' => $orderId,
                'to_phone' => $phone,
                'message' => $message,
                'status' => 'failed',
                'error_message' => 'Netgsm kullanıcı adı veya şifresi .env dosyasında yapılandırılmamış.',
                'response_code' => 'CONFIG_ERROR',
                'type' => $type,
            ]);
            return false;
        }

        // Clean phone number (remove spaces, leading zero, or +90)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '90') && strlen($cleanPhone) === 12) {
            $cleanPhone = substr($cleanPhone, 2);
        } elseif (str_starts_with($cleanPhone, '0') && strlen($cleanPhone) === 11) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        try {
            $response = Http::get($this->apiUrl, [
                'usercode' => $this->usercode,
                'password' => $this->password,
                'gsm' => $cleanPhone,
                'msg' => $message,
                'msgheader' => $this->header,
                'filter' => '0',
            ]);

            $resBody = trim($response->body());
            Log::info('Netgsm SMS response: ' . $resBody);

            $isSuccess = str_starts_with($resBody, '00');
            $responseCode = strtok($resBody, ' ');

            $errorDetail = null;
            if (!$isSuccess) {
                $errorDetail = match($responseCode) {
                    '20' => 'Mesaj metninde veya karakter kütüğünde hata var (Kod: 20)',
                    '30' => 'Geçersiz kullanıcı adı veya şifre (Kod: 30)',
                    '40' => 'Mesaj başlığı (Gönderici Adı) sistemde tanımlı değil (Kod: 40)',
                    '70' => 'Hatalı parametre veya GSM numarası geçersiz (Kod: 70)',
                    default => 'Netgsm Servis Hatası: ' . $resBody,
                };
            }

            SmsLog::create([
                'order_id' => $orderId,
                'to_phone' => $phone,
                'message' => $message,
                'status' => $isSuccess ? 'success' : 'failed',
                'error_message' => $errorDetail,
                'response_code' => $responseCode,
                'type' => $type,
            ]);

            return $isSuccess;
        } catch (\Exception $e) {
            Log::error('Netgsm SMS Exception: ' . $e->getMessage());
            SmsLog::create([
                'order_id' => $orderId,
                'to_phone' => $phone,
                'message' => $message,
                'status' => 'failed',
                'error_message' => 'Sunucu SMS Bağlantı Hatası: ' . $e->getMessage(),
                'response_code' => 'EXCEPTION',
                'type' => $type,
            ]);
            return false;
        }
    }
}
