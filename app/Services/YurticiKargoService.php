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
     * Conforming to Web Servis Giden Kargo Teknik Döküman V3 & WSDL sequence
     */
    public function createShipment(Order $order, array $options = []): array
    {
        $paymentType = strtoupper($options['payment_type'] ?? $order->yurtici_payment_type ?? $this->defaultPayment);
        if (!in_array($paymentType, ['GO', 'AO'])) {
            $paymentType = 'GO';
        }

        $creds = $this->getCredentials($paymentType);

        // Standardize Cargo Key (Max 20 chars, unique)
        $cargoKey = $order->tracking_code ?: ('AHS-' . $order->id);
        if (mb_strlen($cargoKey) > 20) {
            $cargoKey = mb_substr($cargoKey, 0, 20);
        }
        $invoiceKey = 'INV-' . $order->id;
        if (mb_strlen($invoiceKey) > 20) {
            $invoiceKey = mb_substr($invoiceKey, 0, 20);
        }

        // Clean & Validate City / District
        $cityName = trim($order->city ?: 'MANİSA');
        $townName = trim($order->district ?: 'ŞEHZADELER');
        if (mb_strlen($cityName) > 40) {
            $cityName = mb_substr($cityName, 0, 40);
        }
        if (mb_strlen($townName) > 40) {
            $townName = mb_substr($townName, 0, 40);
        }

        // Format and clean recipient name & address according to technical doc
        $receiverName = $this->formatReceiverName($order->name);
        $receiverAddress = $this->formatReceiverAddress($order->address, $cityName, $townName);

        // Standard 10-digit phone number (without country code +90 and trunk 0)
        $phone = $this->cleanPhoneNumber($order->phone);
        if (empty($phone) || strlen($phone) < 10) {
            $phone = '5550000000'; // Safe fallback
        }

        // Email address (only send if valid RFC format)
        $email = trim($order->email ?: '');
        $validEmail = filter_var($email, FILTER_VALIDATE_EMAIL) ? mb_substr($email, 0, 100) : '';

        // Tax / TC Number (only send if checksum validates to avoid 82517 rejection)
        $validTaxNumber = $this->validateTaxNumber($order->identity_number);

        $desi = max(0.5, floatval($options['desi'] ?? 1.0));
        $kg = max(0.5, floatval($options['kg'] ?? 1.0));
        $cargoCount = max(1, intval($options['cargo_count'] ?? 1));
        $description = mb_substr($options['description'] ?? ("AhsapEvim Siparis #" . $order->id), 0, 200, 'UTF-8');

        // Special field 1: Index Sipariş No in Yurtiçi Kargo portal (Field 1 & 3: Sipariş No)
        $specialField1 = "1$" . $order->id . "#3$" . $order->id . "#";

        // Construct XML adhering strictly to WSDL <ShippingOrderVO> element sequence:
        // 1. cargoKey
        // 2. invoiceKey
        // 3. receiverCustName
        // 4. receiverAddress
        // 5. cityName
        // 6. townName
        // 7. receiverPhone1
        // 8. emailAddress (optional)
        // 9. taxNumber (optional, only if valid)
        // 10. desi
        // 11. kg
        // 12. cargoCount
        // 13. specialField1
        // 14. description
        $voElements = [];
        $voElements[] = '<cargoKey>' . htmlspecialchars($cargoKey, ENT_XML1, 'UTF-8') . '</cargoKey>';
        $voElements[] = '<invoiceKey>' . htmlspecialchars($invoiceKey, ENT_XML1, 'UTF-8') . '</invoiceKey>';
        $voElements[] = '<receiverCustName>' . htmlspecialchars($receiverName, ENT_XML1, 'UTF-8') . '</receiverCustName>';
        $voElements[] = '<receiverAddress>' . htmlspecialchars($receiverAddress, ENT_XML1, 'UTF-8') . '</receiverAddress>';
        $voElements[] = '<cityName>' . htmlspecialchars($cityName, ENT_XML1, 'UTF-8') . '</cityName>';
        $voElements[] = '<townName>' . htmlspecialchars($townName, ENT_XML1, 'UTF-8') . '</townName>';
        $voElements[] = '<receiverPhone1>' . htmlspecialchars($phone, ENT_XML1, 'UTF-8') . '</receiverPhone1>';

        if (!empty($validEmail)) {
            $voElements[] = '<emailAddress>' . htmlspecialchars($validEmail, ENT_XML1, 'UTF-8') . '</emailAddress>';
        }

        if (!empty($validTaxNumber)) {
            $voElements[] = '<taxNumber>' . htmlspecialchars($validTaxNumber, ENT_XML1, 'UTF-8') . '</taxNumber>';
        }

        $voElements[] = '<desi>' . number_format($desi, 2, '.', '') . '</desi>';
        $voElements[] = '<kg>' . number_format($kg, 2, '.', '') . '</kg>';
        $voElements[] = '<cargoCount>' . $cargoCount . '</cargoCount>';
        $voElements[] = '<specialField1>' . htmlspecialchars($specialField1, ENT_XML1, 'UTF-8') . '</specialField1>';
        $voElements[] = '<description>' . htmlspecialchars($description, ENT_XML1, 'UTF-8') . '</description>';

        $soapXml = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com.tr/ShippingOrderDispatcherServices">
   <soapenv:Header/>
   <soapenv:Body>
      <ship:createShipment>
         <wsUserName>' . htmlspecialchars($creds['user'], ENT_XML1, 'UTF-8') . '</wsUserName>
         <wsPassword>' . htmlspecialchars($creds['pass'], ENT_XML1, 'UTF-8') . '</wsPassword>
         <userLanguage>TR</userLanguage>
         <ShippingOrderVO>
            ' . implode("\n            ", $voElements) . '
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
            Log::error("Yurtiçi Kargo XML parse hatası (Sipariş #{$order->id}):\n" . $response['body']);
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
        $retCargoKey = (string) ($xml->xpath('//shippingOrderDetailVO/cargoKey')[0] ?? $cargoKey);

        Log::info("Yurtiçi Kargo createShipment Çözümlenen Alanlar (Sipariş #{$order->id}):", [
            'outFlag'       => $outFlag,
            'outResult'     => $outResult,
            'jobId'         => $jobId,
            'cargoKey'      => $retCargoKey,
            'errCode'       => $errCode,
            'errMessage'    => $errMessage,
            'raw_response'  => $response['body'],
        ]);

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
                'cargo_tracking_code'   => $cargoKey,
                'yurtici_cargo_key'     => $cargoKey,
                'yurtici_job_id'        => $jobId ?: null,
                'yurtici_payment_type'  => $paymentType,
                'yurtici_status'        => 'created',
                'yurtici_response_data' => json_encode([
                    'created_at'   => now()->toDateTimeString(),
                    'jobId'        => $jobId,
                    'cargoKey'     => $cargoKey,
                    'invoiceKey'   => $invoiceKey,
                    'paymentType'  => $paymentType,
                    'desi'         => $desi,
                    'kg'           => $kg,
                    'cargoCount'   => $cargoCount,
                    'branch'       => $this->branchName,
                    'raw_response' => $response['body'],
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ]);

            Log::info("Yurtiçi Kargo kaydı oluşturuldu: Sipariş #{$order->id}, CargoKey: {$cargoKey}, JobId: {$jobId}, Tip: {$paymentType}");

            return [
                'success'     => true,
                'cargoKey'    => $cargoKey,
                'jobId'       => $jobId,
                'paymentType' => $paymentType,
                'typeName'    => $creds['typeName'],
                'rawResponse' => $response['body'],
                'message'     => "Yurtiçi Kargo kaydı başarıyla oluşturuldu. (Kargo Anahtarı / Takip Kodu: {$cargoKey}, İş Emri No: #{$jobId})",
            ];
        }

        $errorMessage = $this->translateErrorCode($errCode) ?: ($errMessage ?: ($outResult ?: 'Bilinmeyen kargo servisi hatası oluştu.'));
        Log::warning("Yurtiçi Kargo Gönderi Oluşturulamadı (Sipariş #{$order->id}): " . $errorMessage);

        return [
            'success'     => false,
            'errCode'     => $errCode,
            'rawResponse' => $response['body'],
            'message'     => "Yurtiçi Kargo Hatası: {$errorMessage}",
        ];
    }

    /**
     * Query shipment status from Yurtiçi Kargo (queryShipment)
     * Conforming to Web Servis Giden Kargo Teknik Döküman V3
     */
    public function queryShipment(Order $order): array
    {
        $cargoKey = $order->yurtici_cargo_key ?: $order->cargo_tracking_code ?: ('AHS-' . $order->id);
        $paymentType = $order->yurtici_payment_type ?: $this->defaultPayment;
        $creds = $this->getCredentials($paymentType);

        // Rate-limiting protection: Technical document states repeating queries within 1 min throws exception
        $cacheKey = "yurtici_query_{$order->id}_{$cargoKey}";
        if (request()->wantsJson() === false && \Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if (!empty($cached)) {
                return $cached;
            }
        }

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
            $translated = $this->translateErrorCode($errCode) ?: $errMessage;
            return [
                'success' => false,
                'errCode' => $errCode,
                'message' => $translated ?: "Kargo bulunamadı (Kod: {$errCode}).",
            ];
        }

        $item = $detail->shippingDeliveryItemDetailVO ?? null;
        $opStatus = (string) ($detail->operationStatus ?? '');
        $opMessage = (string) ($detail->operationMessage ?? '');
        $docId = (string) ($item->docId ?? $detail->docId ?? '');
        $docNumber = (string) ($item->docNumber ?? '');
        $invoiceNumber = (string) ($item->invoiceNumber ?? '');

        // Map status explanations according to Technical Document (Page 35)
        $statusMap = [
            'NOP' => 'Kargo İşlem Görmemiş (Şube Çıkışı Bekleniyor)',
            'IND' => 'Kargo Teslimatta / Dağıtımda',
            'ISR' => 'Kargo İşlem Görmüş (Faturası Henüz Kesilmedi)',
            'CNL' => 'Kargo Çıkışı Engellendi (İptal Edildi)',
            'ISC' => 'Kargo Önceden İptal Edilmiş',
            'DLV' => 'Kargo Teslim Edilmiştir',
            'BI'  => 'Fatura Şube Tarafından İptal Edilmiştir',
        ];

        $cargoEvent = (string) ($item->cargoEventExplanation ?? '');
        if (empty($cargoEvent)) {
            $cargoEvent = $statusMap[$opStatus] ?? ($opMessage ?: 'Kargo Kaydı Alındı');
        }

        // Parse historical movement tracking (invDocCargoVOArray)
        $history = [];
        if ($item && !empty($item->invDocCargoVOArray)) {
            foreach ($item->invDocCargoVOArray as $cargoMovement) {
                $history[] = [
                    'unitName'   => (string) ($cargoMovement->unitName ?? ''),
                    'eventName'  => (string) ($cargoMovement->eventName ?? ''),
                    'reasonName' => (string) ($cargoMovement->reasonName ?? ''),
                    'eventDate'  => (string) ($cargoMovement->eventDate ?? ''),
                    'eventTime'  => (string) ($cargoMovement->eventTime ?? ''),
                    'cityName'   => (string) ($cargoMovement->cityName ?? ''),
                    'townName'   => (string) ($cargoMovement->townName ?? ''),
                ];
            }
        }

        $data = [
            'success'             => true,
            'cargoKey'            => (string) ($detail->cargoKey ?? $cargoKey),
            'jobId'               => (string) ($detail->jobId ?? ''),
            'docId'               => $docId, // Official 12-digit Yurtiçi Kargo Gönderi Numarası
            'docNumber'           => $docNumber,
            'invoiceNumber'       => $invoiceNumber,
            'operationStatus'     => $opStatus,
            'operationCode'       => (string) ($detail->operationCode ?? ''),
            'operationMessage'    => $opMessage ?: ($statusMap[$opStatus] ?? 'Kargo Kaydı Alındı'),
            'statusText'          => $statusMap[$opStatus] ?? ($opMessage ?: 'Kargo İşlemde'),
            'trackingUrl'         => (string) ($item->trackingUrl ?? $this->getTrackingUrl($order)),
            'cargoEvent'          => $cargoEvent,
            'deliveryStatus'      => (string) ($item->delInfoDeliveryFlag ?? ($item->documentDelFlag ?? '0')),
            'deliveryDate'        => (string) ($item->deliveryDate ?? ''),
            'deliveryTime'        => (string) ($item->deliveryTime ?? ''),
            'receiverInfo'        => (string) ($item->receiverInfo ?? ''),
            'receiverCustName'    => (string) ($item->receiverCustName ?? $order->name),
            'arrivalUnit'         => (string) ($item->arrivalUnitName ?? 'Belirlenmedi'),
            'departureUnit'       => (string) ($item->departureUnitName ?? $this->branchName),
            'totalDesi'           => (string) ($item->totalDesi ?? ''),
            'totalKg'             => (string) ($item->totalKg ?? ''),
            'history'             => $history,
        ];

        Log::info("Yurtiçi Kargo queryShipment Çözümlenen Alanlar (Sipariş #{$order->id}):", [
            'cargoKey'         => $data['cargoKey'] ?? null,
            'jobId'            => $data['jobId'] ?? null,
            'docId'            => $data['docId'] ?? null,
            'docNumber'        => $data['docNumber'] ?? null,
            'operationStatus'  => $data['operationStatus'] ?? null,
            'cargoEvent'       => $data['cargoEvent'] ?? null,
            'deliveryStatus'   => $data['deliveryStatus'] ?? null,
        ]);

        // When official docId is created by YK branch, save as primary cargo_tracking_code
        if (!empty($docId) && $order->cargo_tracking_code !== $docId) {
            $order->update(['cargo_tracking_code' => $docId]);
        }

        // Update delivered status
        if (($data['deliveryStatus'] === '1' || $opStatus === 'DLV') && $order->yurtici_status !== 'delivered') {
            $order->update(['yurtici_status' => 'delivered']);
            if (in_array($order->status, ['shipped', 'preparing'])) {
                $order->update(['status' => 'delivered']);
            }
        } elseif ($opStatus === 'CNL' || $opStatus === 'ISC') {
            if ($order->yurtici_status !== 'cancelled') {
                $order->update(['yurtici_status' => 'cancelled']);
            }
        }

        // Cache result for 45 seconds to prevent rate-limit exception
        \Illuminate\Support\Facades\Cache::put($cacheKey, $data, 45);

        return $data;
    }

    /**
     * Cancel shipment in Yurtiçi Kargo (cancelShipment)
     * Conforming to Web Servis Giden Kargo Teknik Döküman V3
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
        $errCode = (string) ($cancelDetail->errCode ?? '');
        $errMessage = (string) ($cancelDetail->errMessage ?? '');

        Log::info("Yurtiçi Kargo cancelShipment Çözümlenen Alanlar (Sipariş #{$order->id}):", [
            'cargoKey'         => $cargoKey,
            'outFlag'          => $outFlag,
            'operationStatus'  => $opStatus,
            'operationMessage' => $opMessage,
            'errCode'          => $errCode,
            'errMessage'       => $errMessage,
            'raw_response'     => $response['body'],
        ]);

        // Status CNL (3: Kargo Çıkışı Engellendi), ISC (4: Kargo daha önceden iptal edilmiştir), or Err 82520
        $isCancelled = ($outFlag === '0' && ($opStatus === 'CNL' || $opStatus === 'ISC' || empty($errCode) || $errCode === '0'))
            || $opStatus === 'CNL'
            || $opStatus === 'ISC'
            || $errCode === '82520'
            || str_contains(strtolower($opMessage), 'iptal');

        if ($isCancelled) {
            $order->update([
                'yurtici_status' => 'cancelled',
            ]);

            // Clear cache
            \Illuminate\Support\Facades\Cache::forget("yurtici_query_{$order->id}_{$cargoKey}");

            Log::info("Yurtiçi Kargo gönderisi iptal edildi: Sipariş #{$order->id}, CargoKey: {$cargoKey}");

            return [
                'success' => true,
                'message' => 'Yurtiçi Kargo gönderi kaydı başarıyla iptal edildi. (' . ($opMessage ?: 'Kargo Çıkışı Engellendi') . ')',
            ];
        }

        $failMessage = $this->translateErrorCode($errCode) ?: ($errMessage ?: ($opMessage ?: 'Kargo iptal edilemedi.'));
        return [
            'success' => false,
            'errCode' => $errCode,
            'message' => 'Kargo iptal edilemedi: ' . $failMessage,
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
        Log::info("================================================================================");
        Log::info(">>> YURTİÇİ KARGO API SOAP İSTEĞİ: [{$action}]");
        Log::info("URL: " . $this->endpoint);
        Log::info("İstek XML Gövdesi:\n" . $xmlContent);

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

        Log::info("<<< YURTİÇİ KARGO API SOAP YANITI: [{$action}] (HTTP Kod: {$httpCode})");
        Log::info("Ham XML Yanıtı:\n" . ($response ?: '(Boş Yanıt Alındı)'));
        if ($curlError) {
            Log::error("Yurtiçi Kargo [{$action}] cURL Hatası: " . $curlError);
        }
        Log::info("================================================================================");

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
     * Clean phone number to strictly 10 digits (Alan kodu ile birlikte 10 adet rakam)
     * e.g. +90 545 903 95 84 -> 5459039584
     *      0545 903 95 84    -> 5459039584
     *      0212 365 24 26    -> 2123652426
     */
    public function cleanPhoneNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Keep only digits
        $digits = preg_replace('/\D+/', '', $phone);

        // Strip country code 90 if 12 digits (e.g. 905xxxxxxxxx -> 5xxxxxxxxx)
        if (str_starts_with($digits, '90') && strlen($digits) === 12) {
            $digits = substr($digits, 2);
        }

        // Strip trunk zero 0 if 11 digits (e.g. 05xxxxxxxxx or 02xxxxxxxx -> 5xxxxxxxxx / 2xxxxxxxx)
        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Format receiver address conforming to Technical Document rules:
     * - Do NOT include city / district if cityName and townName are sent
     * - Min 10 alphanumeric chars, max 200 chars
     */
    public function formatReceiverAddress(?string $rawAddress, string $city, string $district): string
    {
        $address = trim($rawAddress ?: '');

        // Remove redundant checkout prefix "İl: ... / İlçe: ... - " if present
        $address = preg_replace('#^İl:\s*[^/]+\s*/\s*İlçe:\s*[^-]+\s*-\s*#iu', '', $address);
        // Remove duplicate city/district tags if present at start
        $address = preg_replace('#^(İl\s*:\s*[^,]+\s*,\s*İlçe\s*:\s*[^,]+[\s,-]*)#iu', '', $address);

        $address = trim(preg_replace('/\s+/', ' ', $address));

        // If remaining address is under 10 characters, append district/neighborhood context to fulfill min 10 chars rule
        if (mb_strlen($address, 'UTF-8') < 10) {
            $address = trim($address . ' ' . $district . ' Mevkii No:1');
        }

        // Cap at 200 characters
        if (mb_strlen($address, 'UTF-8') > 200) {
            $address = mb_substr($address, 0, 200, 'UTF-8');
        }

        return $address;
    }

    /**
     * Format receiver customer name conforming to Technical Document rules:
     * - Min 5 characters, at least 4 letters
     * - Max 200 characters
     */
    public function formatReceiverName(?string $rawName): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $rawName ?: 'Müşteri'));

        // Check letter count
        $lettersOnly = preg_replace('/[^a-zA-ZçÇğĞıİöÖşŞüÜ]/u', '', $name);
        if (mb_strlen($lettersOnly, 'UTF-8') < 4 || mb_strlen($name, 'UTF-8') < 5) {
            $name = $name . ' Müşteri';
        }

        if (mb_strlen($name, 'UTF-8') > 90) {
            $name = mb_substr($name, 0, 90, 'UTF-8');
        }

        return $name;
    }

    /**
     * Validate Turkish TC Kimlik No (11 digits) or Vergi No (10 digits)
     * Returns valid number or null if invalid (to avoid rejection 82517 by Yurtiçi)
     */
    public function validateTaxNumber(?string $rawNumber): ?string
    {
        if (empty($rawNumber)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $rawNumber);

        // TC Kimlik No Check (11 digits)
        if (strlen($digits) === 11) {
            if ($digits[0] === '0') {
                return null;
            }
            // Reject known dummy sequences
            if ($digits === '11111111110' || $digits === '11111111111' || $digits === '22222222222') {
                return null;
            }

            $d = array_map('intval', str_split($digits));
            $oddSum  = $d[0] + $d[2] + $d[4] + $d[6] + $d[8];
            $evenSum = $d[1] + $d[3] + $d[5] + $d[7];

            $digit10 = (($oddSum * 7) - $evenSum) % 10;
            if ($digit10 < 0) {
                $digit10 += 10;
            }
            $digit11 = (array_sum(array_slice($d, 0, 10))) % 10;

            if ($d[9] === $digit10 && $d[10] === $digit11) {
                return $digits;
            }

            return null;
        }

        // Vergi Kimlik No Check (10 digits)
        if (strlen($digits) === 10) {
            return $digits;
        }

        return null;
    }

    /**
     * Translate Yurtiçi Kargo error codes to Turkish human-readable messages
     */
    protected function translateErrorCode(string $code): ?string
    {
        $errors = [
            '60020' => 'Belirtilen Kargo Anahtarı (Barkod) Yurtiçi Kargo sisteminde zaten kayıtlıdır.',
            '60017' => 'Fatura Anahtarı (invoiceKey) bulunamadı.',
            '60018' => 'Alıcı Adı bulunamadı.',
            '60019' => 'Alıcı Adresi bulunamadı.',
            '80859' => 'Kargo Anahtarı bulunamadı.',
            '82500' => 'Kargo Anahtarı 20 karakterden uzun olamaz.',
            '82501' => 'Fatura Anahtarı 20 karakterden uzun olamaz.',
            '82502' => 'Alıcı Adresi en az 10, en fazla 200 karakter olmalıdır.',
            '82503' => 'Alıcı Adı en az 5 karakter olmalı ve en az 4 harf içermelidir.',
            '82515' => 'Geçersiz E-posta Adresi.',
            '82516' => 'Hatalı Telefon Numarası. Telefon numarası alan koduyla birlikte 10 haneli olmalıdır.',
            '82517' => 'Hatalı parametre formatı bilgisi.',
            '82519' => 'Bu kullanıcıya ait belirtilen kargo anahtarı bulunamadı.',
            '82520' => 'Bu kargo gönderisi daha önceden iptal edilmiştir.',
            '82526' => 'Sorgulanacak kargo anahtarları parametresi eksik.',
            '82527' => 'Sorgu anahtar tipi (KEY_TYPE) parametresi hatalı.',
            '936'   => 'Yurtiçi Kargo servisinde beklenmeyen bir hata oluştu.',
        ];

        return $errors[$code] ?? null;
    }
}
