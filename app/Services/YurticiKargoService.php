<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use App\Models\ShippingCompany;
use Illuminate\Support\Facades\Log;

class YurticiKargoService
{
    protected string $endpoint;
    protected string $branchCode;
    protected string $branchName;
    protected string $customerCode;
    protected string $customerName;
    protected string $goUser;
    protected string $goPass;
    protected string $aoUser;
    protected string $aoPass;
    protected string $defaultPayment;
    protected bool $isActive;

    public function __construct()
    {
        $this->endpoint       = Setting::get('yurtici_endpoint', 'https://ws.yurticikargo.com/KOPSWebServices/ShippingOrderDispatcherServices');
        $this->branchCode     = Setting::get('yurtici_branch_code', '3150');
        $this->branchName     = Setting::get('yurtici_branch_name', 'SPİL');
        $this->customerCode   = Setting::get('yurtici_customer_code', '178821492');
        $this->customerName   = Setting::get('yurtici_customer_name', 'METE ALMAZ');
        $this->goUser         = Setting::get('yurtici_go_user', '3150N178821492G');
        $this->goPass         = Setting::get('yurtici_go_pass', 'ziXVB0D16K11vfa5');
        $this->aoUser         = Setting::get('yurtici_ao_user', '3150N178821492A');
        $this->aoPass         = Setting::get('yurtici_ao_pass', 'y36kE8e7HH2NE5B6');
        $this->defaultPayment = Setting::get('yurtici_default_payment', 'GO');
        $this->isActive       = (bool) Setting::get('yurtici_active', '1');
    }

    /**
     * Get credentials for specified payment type (GO: Sender Pays, AO: Receiver Pays)
     */
    public function getCredentials(string $paymentType = 'GO'): array
    {
        $paymentType = strtoupper($paymentType);
        if ($paymentType === 'AO') {
            return [
                'type'     => 'AO',
                'typeName' => 'Alıcı Ödemeli (AÖ)',
                'user'     => $this->aoUser,
                'pass'     => $this->aoPass,
            ];
        }

        return [
            'type'     => 'GO',
            'typeName' => 'Gönderici Ödemeli (GÖ)',
            'user'     => $this->goUser,
            'pass'     => $this->goPass,
        ];
    }

    /**
     * Test Yurtiçi Kargo API connection for GO and AO accounts
     */
    public function testConnection(?string $type = null): array
    {
        $results = [];

        if (!$type || strtoupper($type) === 'GO') {
            $goCreds = $this->getCredentials('GO');
            $results['go'] = $this->pingAccount($goCreds['user'], $goCreds['pass'], 'GÖ (Gönderici Ödemeli)');
        }

        if (!$type || strtoupper($type) === 'AO') {
            $aoCreds = $this->getCredentials('AO');
            $results['ao'] = $this->pingAccount($aoCreds['user'], $aoCreds['pass'], 'AÖ (Alıcı Ödemeli)');
        }

        $allOk = true;
        foreach ($results as $res) {
            if (!$res['success']) {
                $allOk = false;
                break;
            }
        }

        return [
            'success' => $allOk,
            'results' => $results,
            'message' => $allOk
                ? 'Yurtiçi Kargo API bağlantısı başarılı! (GÖ ve AÖ hesapları doğrulandı)'
                : 'Yurtiçi Kargo bağlantı testinde hata oluştu. Lütfen bilgileri kontrol ediniz.'
        ];
    }

    /**
     * Ping Yurtiçi Kargo by sending a dummy queryShipment request
     */
    protected function pingAccount(string $user, string $pass, string $label): array
    {
        $soapXml = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com.tr/ShippingOrderDispatcherServices">
   <soapenv:Header/>
   <soapenv:Body>
      <ship:queryShipment>
         <wsUserName>' . htmlspecialchars($user, ENT_XML1, 'UTF-8') . '</wsUserName>
         <wsPassword>' . htmlspecialchars($pass, ENT_XML1, 'UTF-8') . '</wsPassword>
         <wsLanguage>TR</wsLanguage>
         <keys>PING_CHECK</keys>
         <keyType>0</keyType>
         <addHistoricalData>false</addHistoricalData>
         <onlyTracking>true</onlyTracking>
      </ship:queryShipment>
   </soapenv:Body>
</soapenv:Envelope>';

        $response = $this->sendSoapRequest($soapXml, 'queryShipment');

        if (!$response['success']) {
            return [
                'success' => false,
                'label'   => $label,
                'user'    => $user,
                'message' => 'Bağlantı hatası: ' . $response['error'],
            ];
        }

        $xml = $this->parseXml($response['body']);
        if ($xml === null) {
            return [
                'success' => false,
                'label'   => $label,
                'user'    => $user,
                'message' => 'Geçersiz XML yanıtı alındı.',
            ];
        }

        // Check if outFlag is 0 (even if key not found, auth succeeded)
        $outFlag = (string) ($xml->xpath('//outFlag')[0] ?? '');
        $outResult = (string) ($xml->xpath('//outResult')[0] ?? '');

        if ($outFlag === '0' || str_contains(strtolower($outResult), 'başarılı') || str_contains(strtolower($outResult), 'basarili')) {
            return [
                'success' => true,
                'label'   => $label,
                'user'    => $user,
                'message' => "Bağlantı ve kimlik doğrulama başarılı ({$label}).",
            ];
        }

        return [
            'success' => false,
            'label'   => $label,
            'user'    => $user,
            'message' => 'Yetkilendirme hatası: ' . ($outResult ?: 'Bilinmeyen yanıt'),
        ];
    }

    /**
     * Create shipment in Yurtiçi Kargo (createShipment)
     */
    public function createShipment(Order $order, array $options = []): array
    {
        $paymentType = strtoupper($options['payment_type'] ?? $order->yurtici_payment_type ?? $this->defaultPayment);
        if (!in_array($paymentType, ['GO', 'AO'])) {
            $paymentType = 'GO';
        }

        $creds = $this->getCredentials($paymentType);

        // Standardize Cargo Key
        $cargoKey = $order->tracking_code ?: ('AHS-' . $order->id);
        $invoiceKey = 'INV-' . $order->id;

        // Clean & Format Customer Data
        $receiverName = mb_substr(trim($order->name), 0, 90, 'UTF-8');
        $receiverAddress = mb_substr(trim($order->address), 0, 240, 'UTF-8');
        $cityName = trim($order->city ?: 'Manisa');
        $townName = trim($order->district ?: 'Merkez');
        $phone = $this->cleanPhoneNumber($order->phone);
        $email = trim($order->email ?: '');
        $identityNumber = trim($order->identity_number ?: '');

        $desi = max(0.5, floatval($options['desi'] ?? 1.0));
        $kg = max(0.5, floatval($options['kg'] ?? 1.0));
        $cargoCount = max(1, intval($options['cargo_count'] ?? 1));
        $description = $options['description'] ?? ("AhsapEvim Siparis #" . $order->id);

        $soapXml = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com.tr/ShippingOrderDispatcherServices">
   <soapenv:Header/>
   <soapenv:Body>
      <ship:createShipment>
         <wsUserName>' . htmlspecialchars($creds['user'], ENT_XML1, 'UTF-8') . '</wsUserName>
         <wsPassword>' . htmlspecialchars($creds['pass'], ENT_XML1, 'UTF-8') . '</wsPassword>
         <userLanguage>TR</userLanguage>
         <ShippingOrderVO>
            <cargoKey>' . htmlspecialchars($cargoKey, ENT_XML1, 'UTF-8') . '</cargoKey>
            <invoiceKey>' . htmlspecialchars($invoiceKey, ENT_XML1, 'UTF-8') . '</invoiceKey>
            <receiverCustName>' . htmlspecialchars($receiverName, ENT_XML1, 'UTF-8') . '</receiverCustName>
            <receiverAddress>' . htmlspecialchars($receiverAddress, ENT_XML1, 'UTF-8') . '</receiverAddress>
            <cityName>' . htmlspecialchars($cityName, ENT_XML1, 'UTF-8') . '</cityName>
            <townName>' . htmlspecialchars($townName, ENT_XML1, 'UTF-8') . '</townName>
            <receiverPhone1>' . htmlspecialchars($phone, ENT_XML1, 'UTF-8') . '</receiverPhone1>
            <emailAddress>' . htmlspecialchars($email, ENT_XML1, 'UTF-8') . '</emailAddress>
            <taxOfficeId>0</taxOfficeId>
            <taxNumber>' . htmlspecialchars($identityNumber, ENT_XML1, 'UTF-8') . '</taxNumber>
            <desi>' . $desi . '</desi>
            <kg>' . $kg . '</kg>
            <cargoCount>' . $cargoCount . '</cargoCount>
            <ttInvoiceAmount>0</ttInvoiceAmount>
            <ttDocumentId>0</ttDocumentId>
            <dcSelectedCredit>0</dcSelectedCredit>
            <dcCreditRule>0</dcCreditRule>
            <description>' . htmlspecialchars($description, ENT_XML1, 'UTF-8') . '</description>
         </ShippingOrderVO>
      </ship:createShipment>
   </soapenv:Body>
</soapenv:Envelope>';

        $response = $this->sendSoapRequest($soapXml, 'createShipment');

        if (!$response['success']) {
            Log::error("Yurtiçi Kargo createShipment cURL Hatası (Sipariş #{$order->id}): " . $response['error']);
            return [
                'success' => false,
                'message' => 'Yurtiçi Kargo web servisine bağlanılamadı: ' . $response['error'],
            ];
        }

        $xml = $this->parseXml($response['body']);
        if ($xml === null) {
            Log::error("Yurtiçi Kargo XML parse hatası (Sipariş #{$order->id}): " . $response['body']);
            return [
                'success' => false,
                'message' => 'Yurtiçi Kargo yanıtı çözümlenemedi.',
            ];
        }

        // Inspect response
        $outFlag = (string) ($xml->xpath('//outFlag')[0] ?? '');
        $outResult = (string) ($xml->xpath('//outResult')[0] ?? '');
        $jobId = (string) ($xml->xpath('//jobId')[0] ?? '');
        $errCode = (string) ($xml->xpath('//shippingOrderDetailVO/errCode')[0] ?? '');
        $errMessage = (string) ($xml->xpath('//shippingOrderDetailVO/errMessage')[0] ?? '');

        if ($outFlag === '0' && (empty($errCode) || $errCode === '0')) {
            // Find or create Yurtiçi Kargo shipping company
            $yurticiCompany = ShippingCompany::firstOrCreate(
                ['name' => 'Yurtiçi Kargo'],
                [
                    'website_url' => 'https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula',
                    'is_active'   => true,
                ]
            );

            // Update order with Yurtiçi details
            $order->update([
                'shipping_company_id'   => $yurticiCompany->id,
                'cargo_tracking_code'   => $order->cargo_tracking_code ?: $cargoKey,
                'yurtici_cargo_key'     => $cargoKey,
                'yurtici_job_id'        => $jobId ?: null,
                'yurtici_payment_type'  => $paymentType,
                'yurtici_status'        => 'created',
                'yurtici_response_data' => json_encode([
                    'created_at'   => now()->toDateTimeString(),
                    'jobId'        => $jobId,
                    'cargoKey'     => $cargoKey,
                    'paymentType'  => $paymentType,
                    'desi'         => $desi,
                    'kg'           => $kg,
                    'cargoCount'   => $cargoCount,
                    'branch'       => $this->branchName,
                ]),
            ]);

            Log::info("Yurtiçi Kargo kaydı oluşturuldu: Sipariş #{$order->id}, CargoKey: {$cargoKey}, JobId: {$jobId}, Tip: {$paymentType}");

            return [
                'success'     => true,
                'cargoKey'    => $cargoKey,
                'jobId'       => $jobId,
                'paymentType' => $paymentType,
                'typeName'    => $creds['typeName'],
                'message'     => "Yurtiçi Kargo kaydı başarıyla oluşturuldu. (Takip / Kargo Anahtarı: {$cargoKey})",
            ];
        }

        $errorMessage = $errMessage ?: $outResult ?: 'Bilinmeyen kargo servisi hatası oluştu.';
        Log::warning("Yurtiçi Kargo Gönderi Oluşturulamadı (Sipariş #{$order->id}): " . $errorMessage);

        return [
            'success' => false,
            'errCode' => $errCode,
            'message' => "Yurtiçi Kargo Hatası: {$errorMessage}",
        ];
    }

    /**
     * Query shipment status from Yurtiçi Kargo (queryShipment)
     */
    public function queryShipment(Order $order): array
    {
        $cargoKey = $order->yurtici_cargo_key ?: $order->cargo_tracking_code ?: ('AHS-' . $order->id);
        $paymentType = $order->yurtici_payment_type ?: $this->defaultPayment;
        $creds = $this->getCredentials($paymentType);

        $soapXml = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com.tr/ShippingOrderDispatcherServices">
   <soapenv:Header/>
   <soapenv:Body>
      <ship:queryShipment>
         <wsUserName>' . htmlspecialchars($creds['user'], ENT_XML1, 'UTF-8') . '</wsUserName>
         <wsPassword>' . htmlspecialchars($creds['pass'], ENT_XML1, 'UTF-8') . '</wsPassword>
         <wsLanguage>TR</wsLanguage>
         <keys>' . htmlspecialchars($cargoKey, ENT_XML1, 'UTF-8') . '</keys>
         <keyType>0</keyType>
         <addHistoricalData>true</addHistoricalData>
         <onlyTracking>false</onlyTracking>
      </ship:queryShipment>
   </soapenv:Body>
</soapenv:Envelope>';

        $response = $this->sendSoapRequest($soapXml, 'queryShipment');

        if (!$response['success']) {
            return [
                'success' => false,
                'message' => 'Yurtiçi Kargo sorgulama hatası: ' . $response['error'],
            ];
        }

        $xml = $this->parseXml($response['body']);
        if ($xml === null) {
            return [
                'success' => false,
                'message' => 'Yurtiçi Kargo yanıtı çözümlenemedi.',
            ];
        }

        $outFlag = (string) ($xml->xpath('//outFlag')[0] ?? '');
        $outResult = (string) ($xml->xpath('//outResult')[0] ?? '');
        $detail = $xml->xpath('//shippingDeliveryDetailVO')[0] ?? null;

        if (!$detail) {
            return [
                'success' => false,
                'message' => 'Kargo detay bilgisi bulunamadı. Kargo şube tarafından henüz teslim alınmamış olabilir.',
            ];
        }

        $errCode = (string) ($detail->errCode ?? '');
        $errMessage = (string) ($detail->errMessage ?? '');

        if (!empty($errCode) && $errCode !== '0' && empty($detail->shippingDeliveryItemDetailVO)) {
            return [
                'success' => false,
                'errCode' => $errCode,
                'message' => $errMessage ?: "Kargo bulunamadı (Kod: {$errCode}).",
            ];
        }

        $item = $detail->shippingDeliveryItemDetailVO ?? null;

        $data = [
            'success'             => true,
            'cargoKey'            => (string) ($detail->cargoKey ?? $cargoKey),
            'jobId'               => (string) ($detail->jobId ?? ''),
            'docNumber'           => (string) ($item->docNumber ?? ''),
            'invoiceNumber'       => (string) ($item->invoiceNumber ?? ''),
            'trackingUrl'         => (string) ($item->trackingUrl ?? $this->getTrackingUrl($order)),
            'cargoEvent'          => (string) ($item->cargoEventExplanation ?? 'Kargo Kaydı Alındı'),
            'deliveryStatus'      => (string) ($item->delInfoDeliveryFlag ?? '0'),
            'deliveryDate'        => (string) ($item->deliveryDate ?? ''),
            'deliveryTime'        => (string) ($item->deliveryTime ?? ''),
            'receiverCustName'    => (string) ($item->receiverCustName ?? $order->name),
            'arrivalUnit'         => (string) ($item->arrivalUnitName ?? ''),
            'departureUnit'       => (string) ($item->departureUnitName ?? $this->branchName),
            'totalDesi'           => (string) ($item->totalDesi ?? ''),
            'totalKg'             => (string) ($item->totalKg ?? ''),
        ];

        // If Yurtiçi issued a official docNumber, update cargo_tracking_code if empty or placeholder
        if (!empty($data['docNumber']) && $order->cargo_tracking_code !== $data['docNumber']) {
            $order->update(['cargo_tracking_code' => $data['docNumber']]);
        }

        // If delivered
        if ($data['deliveryStatus'] === '1' && $order->yurtici_status !== 'delivered') {
            $order->update(['yurtici_status' => 'delivered']);
        }

        return $data;
    }

    /**
     * Cancel shipment in Yurtiçi Kargo (cancelShipment)
     */
    public function cancelShipment(Order $order): array
    {
        $cargoKey = $order->yurtici_cargo_key ?: $order->cargo_tracking_code;

        if (empty($cargoKey)) {
            return [
                'success' => false,
                'message' => 'İptal edilecek Yurtiçi Kargo anahtarı bulunamadı.',
            ];
        }

        $paymentType = $order->yurtici_payment_type ?: $this->defaultPayment;
        $creds = $this->getCredentials($paymentType);

        $soapXml = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com.tr/ShippingOrderDispatcherServices">
   <soapenv:Header/>
   <soapenv:Body>
      <ship:cancelShipment>
         <wsUserName>' . htmlspecialchars($creds['user'], ENT_XML1, 'UTF-8') . '</wsUserName>
         <wsPassword>' . htmlspecialchars($creds['pass'], ENT_XML1, 'UTF-8') . '</wsPassword>
         <userLanguage>TR</userLanguage>
         <cargoKeys>' . htmlspecialchars($cargoKey, ENT_XML1, 'UTF-8') . '</cargoKeys>
      </ship:cancelShipment>
   </soapenv:Body>
</soapenv:Envelope>';

        $response = $this->sendSoapRequest($soapXml, 'cancelShipment');

        if (!$response['success']) {
            return [
                'success' => false,
                'message' => 'Yurtiçi Kargo iptal isteği başarısız: ' . $response['error'],
            ];
        }

        $xml = $this->parseXml($response['body']);
        if ($xml === null) {
            return [
                'success' => false,
                'message' => 'Yurtiçi Kargo yanıtı çözümlenemedi.',
            ];
        }

        $outFlag = (string) ($xml->xpath('//outFlag')[0] ?? '');
        $cancelDetail = $xml->xpath('//shippingCancelDetailVO')[0] ?? null;
        $opStatus = (string) ($cancelDetail->operationStatus ?? '');
        $opMessage = (string) ($cancelDetail->operationMessage ?? '');

        if ($outFlag === '0' || $opStatus === 'CNL' || str_contains(strtolower($opMessage), 'iptal')) {
            $order->update([
                'yurtici_status' => 'cancelled',
            ]);

            Log::info("Yurtiçi Kargo gönderisi iptal edildi: Sipariş #{$order->id}, CargoKey: {$cargoKey}");

            return [
                'success' => true,
                'message' => 'Yurtiçi Kargo gönderi kaydı başarıyla iptal edildi. (' . ($opMessage ?: 'Kargo Çıkışı Engellendi') . ')',
            ];
        }

        return [
            'success' => false,
            'message' => 'Kargo iptal edilemedi: ' . ($opMessage ?: 'Bilinmeyen durum'),
        ];
    }

    /**
     * Generate customer-facing or admin Yurtiçi tracking URL
     */
    public function getTrackingUrl(Order $order): string
    {
        $code = $order->cargo_tracking_code ?: $order->yurtici_cargo_key ?: $order->tracking_code;
        return 'https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code=' . urlencode($code);
    }

    /**
     * Send SOAP Request using resilient cURL
     */
    protected function sendSoapRequest(string $xmlContent, string $action): array
    {
        $ch = curl_init($this->endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlContent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: ""',
            'Content-Length: ' . strlen($xmlContent)
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'error'   => $curlError,
                'code'    => $httpCode,
            ];
        }

        if ($httpCode >= 400 && empty($response)) {
            return [
                'success' => false,
                'error'   => "HTTP {$httpCode} hatası alındı.",
                'code'    => $httpCode,
            ];
        }

        return [
            'success' => true,
            'body'    => $response,
            'code'    => $httpCode,
        ];
    }

    /**
     * Safely parse XML response handling namespaces
     */
    protected function parseXml(string $xmlString): ?\SimpleXMLElement
    {
        try {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlString);
            libxml_clear_errors();

            if ($xml === false) {
                return null;
            }

            // Register standard SOAP envelope namespaces
            $xml->registerXPathNamespace('env', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xml->registerXPathNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');

            return $xml;
        } catch (\Throwable $e) {
            Log::error('Yurtiçi XML parse exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Clean phone number to format 05XXXXXXXXX
     */
    protected function cleanPhoneNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Keep only digits
        $digits = preg_replace('/\D+/', '', $phone);

        // If starts with 90 and length is 12 (905xxxxxxxxx), strip 90
        if (str_starts_with($digits, '90') && strlen($digits) === 12) {
            $digits = substr($digits, 2);
        }

        // Ensure leading zero for 10-digit mobile (5xxxxxxxxx -> 05xxxxxxxxx)
        if (strlen($digits) === 10 && str_starts_with($digits, '5')) {
            $digits = '0' . $digits;
        }

        return $digits;
    }
}
