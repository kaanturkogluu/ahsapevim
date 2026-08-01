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
        $request->setConversationId($order->id);
        $request->setPrice(number_format($order->total_amount, 2, '.', ''));
        $request->setPaidPrice(number_format($order->total_amount, 2, '.', ''));
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
            $surname = 'Soyadi';
        }

        $buyer = new Buyer();
        $buyer->setId($order->user_id ?: 9999);
        $buyer->setName($name);
        $buyer->setSurname($surname);
        $buyer->setEmail($order->email);
        $buyer->setGsmNumber($order->phone);
        $buyer->setIdentityNumber($order->identity_number ?? '11111111111');
        $buyer->setRegistrationAddress($order->address);
        $buyer->setCity('Manisa');
        $buyer->setCountry('Turkey');
        $buyer->setIp(request()->ip() ?: '127.0.0.1');
        $request->setBuyer($buyer);

        $billingAddress = new Address();
        $billingAddress->setContactName($order->name);
        $billingAddress->setCity('Manisa');
        $billingAddress->setCountry('Turkey');
        $billingAddress->setAddress($order->address);
        $request->setBillingAddress($billingAddress);

        $shippingAddress = new Address();
        $shippingAddress->setContactName($order->name);
        $shippingAddress->setCity('Manisa');
        $shippingAddress->setCountry('Turkey');
        $shippingAddress->setAddress($order->address);
        $request->setShippingAddress($shippingAddress);

        $basketItems = [];
        foreach ($cartItems as $key => $item) {
            $basketItem = new BasketItem();
            $basketItem->setId($item['product_id']);
            $basketItem->setName($item['name']);
            $basketItem->setCategory1('Cerceve');
            $basketItem->setItemType(BasketItemType::PHYSICAL);
            $basketItem->setPrice(number_format($item['price'] * $item['quantity'], 2, '.', ''));
            $basketItems[] = $basketItem;
        }
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
