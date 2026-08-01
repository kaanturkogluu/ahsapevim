<?php

namespace App\Services;

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
     * Send an SMS via Netgsm GET API
     * 
     * @param string $phone
     * @param string $message
     * @return bool
     */
    public function sendSms($phone, $message)
    {
        if (empty($this->usercode) || empty($this->password)) {
            Log::warning('Netgsm SMS credentials not configured in .env. Skipping SMS.');
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

            Log::info('Netgsm SMS response: ' . $response->body());
            
            // Netgsm returns status code starting with "00" on success
            return str_starts_with($response->body(), '00');
        } catch (\Exception $e) {
            Log::error('Netgsm SMS Exception: ' . $e->getMessage());
            return false;
        }
    }
}
