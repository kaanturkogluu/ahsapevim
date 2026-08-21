<?php

namespace App\Services;

use Iyzipay\Options;
use Iyzipay\Model\Locale;
use Iyzipay\Model\Currency;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\CheckoutFormInitialize;
use Iyzipay\Request\CreateCheckoutFormInitializeRequest;
use Iyzipay\Model\CheckoutForm;
use Iyzipay\Request\RetrieveCheckoutFormRequest;

class IyzicoService
{
    protected $options;

    public function __construct()
    {
        $this->options = new Options();
        $this->options->setApiKey(config('services.iyzico.api_key'));
        $this->options->setSecretKey(config('services.iyzico.secret_key'));
        $this->options->setBaseUrl(config('services.iyzico.base_url', 'https://sandbox-api.iyzipay.com'));
    }

    /**
     * Initialize Iyzico Checkout Form
     * 
     * @param \App\Models\Order $order
     * @param array $cartItems
     * @param string $callbackUrl
     * @return \Iyzipay\Model\CheckoutFormInitialize
     */
    public function initializeCheckoutForm($order, $cartItems, $callbackUrl)
    {
        $request = new CreateCheckoutFormInitializeRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId((string)$order->id);

        // 1. Phone number format (+905XXXXXXXXX - 13 chars)
        $cleanPhone = preg_replace('/[^0-9]/', '', $order->phone);
        if (str_starts_with($cleanPhone, '90') && strlen($cleanPhone) == 12) {
            $cleanPhone = '+' . $cleanPhone;
        } elseif (str_starts_with($cleanPhone, '0') && strlen($cleanPhone) == 11) {
            $cleanPhone = '+90' . substr($cleanPhone, 1);
        } elseif (strlen($cleanPhone) == 10 && str_starts_with($cleanPhone, '5')) {
            $cleanPhone = '+90' . $cleanPhone;
        }
        if (empty($cleanPhone) || strlen($cleanPhone) !== 13) {
            $cleanPhone = '+905555555555';
        }

        // 2. T.C. Identity Number (Strict 11 digits for live iyzico)
        $tcNo = preg_replace('/[^0-9]/', '', $order->identity_number);
        if (strlen($tcNo) !== 11) {
            $tcNo = '11111111111';
        }

        // 3. Basket Items & Total Amount Sum Matching
        $basketItems = [];
        $calculatedSum = 0;
        foreach ($cartItems as $key => $item) {
            $itemTotal = round((float)$item['price'] * (int)$item['quantity'], 2);
            $calculatedSum += $itemTotal;

            $itemName = trim(strip_tags($item['name'] ?? 'Ahsap Urun'));
            if (empty($itemName)) $itemName = 'Ahsap Urun';
            $itemName = mb_substr($itemName, 0, 50);

            $basketItem = new BasketItem();
            $basketItem->setId((string)($item['product_id'] ?? ($key + 1)));
            $basketItem->setName($itemName);
            $basketItem->setCategory1('Ahsap Cerceve');
            $basketItem->setItemType(BasketItemType::PHYSICAL);
            $basketItem->setPrice(number_format($itemTotal, 2, '.', ''));
            $basketItems[] = $basketItem;
        }

        $orderTotal = round((float)$order->total_amount, 2);
        if ($orderTotal <= 0) {
            $orderTotal = $calculatedSum;
        }

        // Adjust Basket Items Sum to match $orderTotal down to exact cent
        $diff = round($orderTotal - $calculatedSum, 2);
        if (abs($diff) >= 0.01) {
            if ($diff > 0) {
                // Add Shipping/Service item for positive difference (e.g. shipping fee)
                $shippingItem = new BasketItem();
                $shippingItem->setId('SHIPPING');
                $shippingItem->setName('Kargo ve Hizmet Bedeli');
                $shippingItem->setCategory1('Kargo');
                $shippingItem->setItemType(BasketItemType::PHYSICAL);
                $shippingItem->setPrice(number_format($diff, 2, '.', ''));
                $basketItems[] = $shippingItem;
            } elseif ($diff < 0 && count($basketItems) > 0) {
                // Adjust last item price for negative difference (e.g. discount)
                $lastIdx = count($basketItems) - 1;
                $currentPrice = (float)$basketItems[$lastIdx]->getPrice();
                $adjustedPrice = round($currentPrice + $diff, 2);
                if ($adjustedPrice > 0) {
                    $basketItems[$lastIdx]->setPrice(number_format($adjustedPrice, 2, '.', ''));
                }
            }
        }

        $totalFormatted = number_format($orderTotal, 2, '.', '');
        $request->setPrice($totalFormatted);
        $request->setPaidPrice($totalFormatted);
        $request->setCurrency(Currency::TL);
        $request->setBasketId('B' . $order->id);
        $request->setPaymentGroup(PaymentGroup::PRODUCT);
        $request->setCallbackUrl($callbackUrl);

        // 4. Split Name & Surname
        $cleanFullName = trim(preg_replace('/\s+/', ' ', $order->name));
        $nameParts = explode(' ', $cleanFullName);
        $surname = array_pop($nameParts);
        $name = implode(' ', $nameParts);
        if (empty($name)) {
            $name = $surname ?: 'Musteri';
            $surname = 'Ahsapevim';
        }

        // 5. Buyer Info
        $buyer = new Buyer();
        $buyer->setId((string)($order->user_id ?: ($order->id ?: 9999)));
        $buyer->setName(mb_substr($name, 0, 45));
        $buyer->setSurname(mb_substr($surname, 0, 45));
        $buyer->setEmail($order->email ?: 'info@ahsapevimmanisa.com');
        $buyer->setGsmNumber($cleanPhone);
        $buyer->setIdentityNumber($tcNo);
        $buyer->setRegistrationAddress(mb_substr($order->address ?: 'Manisa Merkez', 0, 200));
        $buyer->setCity($order->city ?: 'Manisa');
        $buyer->setCountry('Turkey');
        $buyer->setIp(request()->ip() ?: '127.0.0.1');
        $request->setBuyer($buyer);

        // 6. Billing & Shipping Address
        $addressStr = mb_substr($order->address ?: 'Manisa Merkez', 0, 200);

        $billingAddress = new Address();
        $billingAddress->setContactName(mb_substr($cleanFullName ?: 'Musteri', 0, 50));
        $billingAddress->setCity($order->city ?: 'Manisa');
        $billingAddress->setCountry('Turkey');
        $billingAddress->setAddress($addressStr);
        $request->setBillingAddress($billingAddress);

        $shippingAddress = new Address();
        $shippingAddress->setContactName(mb_substr($cleanFullName ?: 'Musteri', 0, 50));
        $shippingAddress->setCity($order->city ?: 'Manisa');
        $shippingAddress->setCountry('Turkey');
        $shippingAddress->setAddress($addressStr);
        $request->setShippingAddress($shippingAddress);

        $request->setBasketItems($basketItems);

        \Illuminate\Support\Facades\Log::info("=== IYZICO INIT CHECKOUT FORM LOG [Order #{$order->id}] ===", [
            'config' => [
                'base_url'   => config('services.iyzico.base_url'),
                'api_key'    => substr(config('services.iyzico.api_key'), 0, 15) . '...',
                'secret_key' => substr(config('services.iyzico.secret_key'), 0, 10) . '...',
            ],
            'request_summary' => [
                'order_id'       => $order->id,
                'price'          => $totalFormatted,
                'paid_price'     => $totalFormatted,
                'currency'       => Currency::TL,
                'callback_url'   => $callbackUrl,
                'basket_total'   => $calculatedSum,
                'basket_count'   => count($basketItems),
                'buyer_name'     => $name . ' ' . $surname,
                'buyer_email'    => $order->email,
                'buyer_phone'    => $cleanPhone,
                'buyer_tc'       => $tcNo,
                'buyer_city'     => $order->city,
                'buyer_address'  => $addressStr,
                'buyer_ip'       => request()->ip(),
            ],
            'basket_items' => array_map(function($bItem) {
                return [
                    'id'       => $bItem->getId(),
                    'name'     => $bItem->getName(),
                    'price'    => $bItem->getPrice(),
                    'category' => $bItem->getCategory1(),
                    'type'     => $bItem->getItemType(),
                ];
            }, $basketItems),
        ]);

        $res = CheckoutFormInitialize::create($request, $this->options);

        \Illuminate\Support\Facades\Log::info("=== IYZICO INIT CHECKOUT FORM RESPONSE [Order #{$order->id}] ===", [
            'status'         => $res ? $res->getStatus() : null,
            'error_code'     => $res ? $res->getErrorCode() : null,
            'error_message'  => $res ? $res->getErrorMessage() : null,
            'error_group'    => $res ? $res->getErrorGroup() : null,
            'token'          => $res ? $res->getToken() : null,
            'raw_result'     => $res ? $res->getRawResult() : null,
        ]);

        return $res;
    }

    /**
     * Retrieve Iyzico Checkout Form Result using Token
     * 
     * @param string $token
     * @return \Iyzipay\Model\CheckoutForm
     */
    public function retrieveCheckoutForm($token)
    {
        $request = new RetrieveCheckoutFormRequest();
        $request->setLocale(Locale::TR);
        $request->setToken($token);

        $res = CheckoutForm::retrieve($request, $this->options);

        \Illuminate\Support\Facades\Log::info("=== IYZICO RETRIEVE CHECKOUT FORM RESPONSE [Token: {$token}] ===", [
            'config' => [
                'base_url' => config('services.iyzico.base_url'),
            ],
            'response' => [
                'status'          => $res ? $res->getStatus() : null,
                'payment_status'  => $res ? $res->getPaymentStatus() : null,
                'error_code'      => $res ? $res->getErrorCode() : null,
                'error_message'   => $res ? $res->getErrorMessage() : null,
                'payment_id'      => $res ? $res->getPaymentId() : null,
                'price'           => $res ? $res->getPrice() : null,
                'paid_price'      => $res ? $res->getPaidPrice() : null,
                'raw_result'      => $res ? $res->getRawResult() : null,
            ]
        ]);

        return $res;
    }
}
