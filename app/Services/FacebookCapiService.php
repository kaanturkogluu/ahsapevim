<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookCapiService
{
    protected string $pixelId;
    protected string $accessToken;
    protected string $apiVersion = 'v19.0';

    public function __construct()
    {
        $this->pixelId = (string) Setting::get('facebook_pixel_id', config('services.facebook.pixel_id', '1151884751162206'));
        $this->accessToken = (string) Setting::get('facebook_access_token', config('services.facebook.access_token', 'EAAaur7B13B4BSb1P8ZAdbIdT0uNY26NzpwVVtMsoKv3qvUD9kaVJo6nIT9O1XdGPMnbh1B4xT6lg2KItz4F65nfOmGIKPwkNG3vFHluziYhS7UlobwEQedeQZCW1CM5bEt1xXofLJAoKLqqQ5ucXpvjcMmZA7ZA7yuyXs8SA2BNqWi5ERCQdVJ713XJ7lAZDZD'));
    }

    /**
     * Get configured Pixel ID
     */
    public function getPixelId(): string
    {
        return $this->pixelId;
    }

    /**
     * Get configured Access Token
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Check if credentials are set
     */
    public function isConfigured(): bool
    {
        return !empty($this->pixelId) && !empty($this->accessToken);
    }

    /**
     * Send standard or custom event to Facebook Conversions API
     */
    public function sendEvent(string $eventName, array $userData = [], array $customData = [], ?string $eventSourceUrl = null, ?string $eventId = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Facebook Pixel ID veya Erişim Jetonu yapılandırılmamış.',
            ];
        }

        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->pixelId}/events";

        // Prepare User Data with SHA-256 Hashes as required by Meta Conversions API
        $formattedUserData = [];

        if (!empty($userData['email'])) {
            $formattedUserData['em'] = [hash('sha256', strtolower(trim($userData['email'])))];
        }

        if (!empty($userData['phone'])) {
            $cleanedPhone = preg_replace('/[^0-9]/', '', $userData['phone']);
            if (str_starts_with($cleanedPhone, '0')) {
                $cleanedPhone = '9' . $cleanedPhone;
            } elseif (!str_starts_with($cleanedPhone, '90') && strlen($cleanedPhone) === 10) {
                $cleanedPhone = '90' . $cleanedPhone;
            }
            $formattedUserData['ph'] = [hash('sha256', $cleanedPhone)];
        }

        if (!empty($userData['first_name'])) {
            $formattedUserData['fn'] = [hash('sha256', strtolower(trim($userData['first_name'])))];
        }

        if (!empty($userData['last_name'])) {
            $formattedUserData['ln'] = [hash('sha256', strtolower(trim($userData['last_name'])))];
        }

        if (!empty($userData['city'])) {
            $formattedUserData['ct'] = [hash('sha256', strtolower(trim($userData['city'])))];
        }

        if (!empty($userData['country'])) {
            $formattedUserData['country'] = [hash('sha256', strtolower(trim($userData['country'])))];
        } else {
            $formattedUserData['country'] = [hash('sha256', 'tr')];
        }

        if (!empty($userData['client_ip_address'])) {
            $formattedUserData['client_ip_address'] = $userData['client_ip_address'];
        } elseif (request()) {
            $formattedUserData['client_ip_address'] = request()->ip();
        }

        if (!empty($userData['client_user_agent'])) {
            $formattedUserData['client_user_agent'] = $userData['client_user_agent'];
        } elseif (request()) {
            $formattedUserData['client_user_agent'] = request()->userAgent();
        }

        // Event Payload
        $eventData = [
            'event_name'       => $eventName,
            'event_time'       => time(),
            'action_source'    => 'website',
            'event_source_url' => $eventSourceUrl ?: (request() ? request()->fullUrl() : 'https://ahsapevimmanisa.com'),
            'user_data'        => $formattedUserData,
            'custom_data'      => $customData,
        ];

        if ($eventId) {
            $eventData['event_id'] = $eventId;
        }

        try {
            $response = Http::timeout(10)->post($url, [
                'data'         => [$eventData],
                'access_token' => $this->accessToken,
            ]);

            $json = $response->json();

            if ($response->successful() && isset($json['events_received']) && $json['events_received'] > 0) {
                Log::info("Facebook CAPI: Event '{$eventName}' successfully dispatched to Meta.", [
                    'pixel_id'        => $this->pixelId,
                    'events_received' => $json['events_received'],
                    'fbtrace_id'      => $json['fbtrace_id'] ?? null,
                ]);

                return [
                    'success'    => true,
                    'message'    => "Meta CAPI: '{$eventName}' olayı başarıyla iletildi.",
                    'response'   => $json,
                ];
            } else {
                $errorMsg = $json['error']['message'] ?? 'Bilinmeyen Meta API hatası';
                Log::warning("Facebook CAPI Error for '{$eventName}': " . $errorMsg, $json);

                return [
                    'success'    => false,
                    'message'    => "Meta CAPI Hatası: " . $errorMsg,
                    'response'   => $json,
                ];
            }
        } catch (\Throwable $e) {
            Log::error("Facebook CAPI Exception for '{$eventName}': " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Meta API sunucu bağlantı hatası: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send Purchase event for an Order
     */
    public function sendPurchaseEvent(Order $order, ?Request $request = null): array
    {
        $nameParts = explode(' ', trim($order->name));
        $firstName = $nameParts[0] ?? '';
        $lastName  = count($nameParts) > 1 ? end($nameParts) : '';

        $userData = [
            'email'             => $order->email,
            'phone'             => $order->phone,
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            'city'              => $order->city ?: 'Manisa',
            'client_ip_address' => $request ? $request->ip() : null,
            'client_user_agent' => $request ? $request->userAgent() : null,
        ];

        $contents = [];
        $contentIds = [];

        foreach ($order->items as $item) {
            $contentIds[] = (string) $item->product_id;
            $contents[] = [
                'id'         => (string) $item->product_id,
                'quantity'   => (int) $item->quantity,
                'item_price' => (float) $item->price,
            ];
        }

        $customData = [
            'currency'     => 'TRY',
            'value'        => (float) $order->total_amount,
            'content_type' => 'product',
            'content_ids'  => $contentIds,
            'contents'     => $contents,
            'order_id'     => (string) $order->id,
        ];

        return $this->sendEvent(
            'Purchase',
            $userData,
            $customData,
            url('/siparis-sonuc'),
            'order_' . $order->id
        );
    }

    /**
     * Test connection to Facebook Conversions API
     */
    public function testConnection(): array
    {
        return $this->sendEvent('PageView', [
            'email' => 'info@ahsapevimmanisa.com',
            'city'  => 'Manisa',
        ], [
            'status' => 'test_connection',
        ], 'https://ahsapevimmanisa.com', 'test_' . time());
    }
}
