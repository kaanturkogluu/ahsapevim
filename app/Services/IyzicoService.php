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
        // Clean phone number format for Iyzico (+90XXXXXXXXXX)
        $cleanPhone = preg_replace('/[^0-9+]/', '', $order->phone);
        if (strlen($cleanPhone) == 10 && str_starts_with($cleanPhone, '5')) {
            $cleanPhone = '+90' . $cleanPhone;
        } elseif (strlen($cleanPhone) == 11 && str_starts_with($cleanPhone, '05')) {
            $cleanPhone = '+90' . substr($cleanPhone, 1);
        }
        if (empty($cleanPhone) || strlen($cleanPhone) < 10) {
            $cleanPhone = '+905555555555';
        }

        // Build Basket Items and calculate total sum
        $basketItems = [];
        $calculatedSum = 0;
        foreach ($cartItems as $key => $item) {
            $itemTotal = round($item['price'] * $item['quantity'], 2);
            $calculatedSum += $itemTotal;

            $basketItem = new BasketItem();
            $basketItem->setId((string)$item['product_id']);
            $basketItem->setName(mb_substr($item['name'], 0, 50));
            $basketItem->setCategory1('Cerceve');
            $basketItem->setItemType(BasketItemType::PHYSICAL);
            $basketItem->setPrice(number_format($itemTotal, 2, '.', ''));
            $basketItems[] = $basketItem;
        }

        // Set total price matching basket sum
        $totalFormatted = number_format($calculatedSum > 0 ? $calculatedSum : $order->total_amount, 2, '.', '');
        $request->setPrice($totalFormatted);
        $request->setPaidPrice($totalFormatted);
        $request->setCurrency(Currency::TL);
        $request->setBasketId('B' . $order->id);
        $request->setPaymentGroup(PaymentGroup::PRODUCT);
        $request->setCallbackUrl($callbackUrl);

        // Split name/surname (Iyzico requires both)
        $nameParts = explode(' ', trim($order->name));
        $surname = array_pop($nameParts);
        $name = implode(' ', $nameParts);
        if (empty($name)) {
            $name = $surname;
            $surname = 'Musteri';
        }

        $buyer = new Buyer();
        $buyer->setId((string)($order->user_id ?: 9999));
        $buyer->setName($name);
        $buyer->setSurname($surname);
        $buyer->setEmail($order->email);
        $buyer->setGsmNumber($cleanPhone);
        $buyer->setIdentityNumber($order->identity_number ?: '11111111111');
        $buyer->setRegistrationAddress($order->address ?: 'Manisa');
        $buyer->setCity($order->city ?: 'Manisa');
        $buyer->setCountry('Turkey');
        $buyer->setIp(request()->ip() ?: '127.0.0.1');
        $request->setBuyer($buyer);

        $billingAddress = new Address();
        $billingAddress->setContactName($order->name);
        $billingAddress->setCity($order->city ?: 'Manisa');
        $billingAddress->setCountry('Turkey');
        $billingAddress->setAddress($order->address);
        $request->setBillingAddress($billingAddress);

        $shippingAddress = new Address();
        $shippingAddress->setContactName($order->name);
        $shippingAddress->setCity($order->city ?: 'Manisa');
        $shippingAddress->setCountry('Turkey');
        $shippingAddress->setAddress($order->address);
        $request->setShippingAddress($shippingAddress);

        $request->setBasketItems($basketItems);

        return CheckoutFormInitialize::create($request, $this->options);
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

        return CheckoutForm::retrieve($request, $this->options);
    }
}
