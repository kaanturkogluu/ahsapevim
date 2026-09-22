<?php

namespace App\Services;

use App\Models\Order;
use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\CheckoutForm;
use Iyzipay\Model\CheckoutFormInitialize;
use Iyzipay\Model\Currency;
use Iyzipay\Model\InstallmentInfo;
use Iyzipay\Model\Locale;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Options;
use Iyzipay\Request\CreateCheckoutFormInitializeRequest;
use Iyzipay\Request\RetrieveCheckoutFormRequest;
use Iyzipay\Request\RetrieveInstallmentInfoRequest;

class IyzicoService
{
    private function getOptions(): Options
    {
        $options = new Options();
        $options->setApiKey(config('services.iyzico.api_key'));
        $options->setSecretKey(config('services.iyzico.secret_key'));
        $options->setBaseUrl(config('services.iyzico.base_url'));
        return $options;
    }

    /**
     * İyzico Resmi Checkout Formu Başlatma (Hosted / Responsive Form).
     * Müşterinin kredi kartı bilgileri sitemiz yerine İyzico'nun güvenli formunda girilir.
     * Taksit seçenekleri ve 3D Secure akışı İyzico tarafından otomatik yönetilir.
     *
     * @param Order $order
     * @return array
     */
    public function initiateCheckoutForm(Order $order): array
    {
        $options = $this->getOptions();
        $order->loadMissing('items.product');

        $request = new CreateCheckoutFormInitializeRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId((string) $order->id);
        
        $totalPrice = number_format($order->total_amount, 2, '.', '');
        $request->setPrice($totalPrice);
        $request->setPaidPrice($totalPrice);
        $request->setCurrency(Currency::TL);
        $request->setBasketId('ORDER_' . $order->id);
        $request->setPaymentGroup(PaymentGroup::PRODUCT);
        $request->setCallbackUrl(route('checkout.iyzico.callback'));
        $request->setEnabledInstallments([1, 2, 3, 6, 9, 12]);

        // Alıcı (Buyer)
        $nameParts = explode(' ', trim($order->name), 2);
        $firstName  = $nameParts[0] ?: 'Müşteri';
        $lastName   = $nameParts[1] ?? $firstName;

        $buyer = new Buyer();
        $buyer->setId('USER_' . ($order->user_id ?? $order->id));
        $buyer->setName($firstName);
        $buyer->setSurname($lastName);
        $buyer->setGsmNumber($this->formatPhone($order->phone));
        $buyer->setEmail($order->email);
        $buyer->setIdentityNumber($order->identity_number ?: '11111111111');
        $buyer->setLastLoginDate(now()->format('Y-m-d H:i:s'));
        $buyer->setRegistrationDate(now()->format('Y-m-d H:i:s'));
        $buyer->setRegistrationAddress($order->address ?: 'Türkiye');
        $buyer->setIp(request()->ip() ?: '127.0.0.1');
        $buyer->setCity($order->city ?: 'Manisa');
        $buyer->setCountry('Turkey');
        $buyer->setZipCode('45000');
        $request->setBuyer($buyer);

        // Teslimat Adresi (Shipping Address)
        $shippingAddress = new Address();
        $shippingAddress->setContactName($order->name);
        $shippingAddress->setCity($order->city ?: 'Manisa');
        $shippingAddress->setCountry('Turkey');
        $shippingAddress->setAddress($order->address ?: 'Türkiye');
        $shippingAddress->setZipCode('45000');
        $request->setShippingAddress($shippingAddress);

        // Fatura Adresi (Billing Address)
        $billingAddress = new Address();
        $billingAddress->setContactName($order->name);
        $billingAddress->setCity($order->city ?: 'Manisa');
        $billingAddress->setCountry('Turkey');
        $billingAddress->setAddress($order->address ?: 'Türkiye');
        $billingAddress->setZipCode('45000');
        $request->setBillingAddress($billingAddress);

        // Sepet Kalemleri (Basket Items)
        $basketItems = [];
        $itemsSum = 0.0;
        foreach ($order->items as $item) {
            $itemTotalPrice = round($item->price * $item->quantity, 2);
            $basketItem = new BasketItem();
            $basketItem->setId('ITEM_' . $item->id);
            $basketItem->setName(mb_substr($item->product ? $item->product->name : 'Ahşap Ürün', 0, 100));
            $basketItem->setCategory1('Ahşap Ürünler');
            $basketItem->setItemType(BasketItemType::PHYSICAL);
            $basketItem->setPrice(number_format($itemTotalPrice, 2, '.', ''));
            $basketItems[] = $basketItem;
            $itemsSum += $itemTotalPrice;
        }

        // Kuruş yuvarlama farkı kontrolü: basketItems toplamı mutlaka order->total_amount ile eşit olmalıdır
        $orderTotal = round((float) $order->total_amount, 2);
        $difference = round($orderTotal - $itemsSum, 2);
        if ($difference != 0 && count($basketItems) > 0) {
            $lastIndex = count($basketItems) - 1;
            $adjustedPrice = round((float) $basketItems[$lastIndex]->getPrice() + $difference, 2);
            $basketItems[$lastIndex]->setPrice(number_format($adjustedPrice, 2, '.', ''));
        }

        $request->setBasketItems($basketItems);

        // İyzico'ya istek gönder
        $initialize = CheckoutFormInitialize::create($request, $options);

        $isSuccess = ($initialize->getStatus() === 'success');
        $token     = $initialize->getToken();
        $errorMsg  = $initialize->getErrorMessage();
        $errorCode = $initialize->getErrorCode();

        // Okunaklı Loglama
        PaymentLogService::logCheckoutInitialize($order, $isSuccess, $token, $errorMsg, $errorCode);

        if (!$isSuccess) {
            return [
                'success'      => false,
                'errorMessage' => $errorMsg ?: 'İyzico ödeme formu başlatılamadı. Lütfen bilgilerinizi kontrol ediniz.',
                'errorCode'    => $errorCode,
            ];
        }

        return [
            'success'             => true,
            'token'               => $token,
            'checkoutFormContent' => $initialize->getCheckoutFormContent(),
            'paymentPageUrl'      => $initialize->getPaymentPageUrl(),
        ];
    }

    /**
     * İyzico Callback sonrasında ödeme sonucunu token ile sorgular.
     * SDK: CheckoutForm::retrieve(RetrieveCheckoutFormRequest $request, Options $options)
     *
     * @param string $token
     * @return array
     */
    public function retrieveCheckoutForm(string $token): array
    {
        $options = $this->getOptions();

        $request = new RetrieveCheckoutFormRequest();
        $request->setLocale(Locale::TR);
        $request->setToken($token);

        $checkoutForm = CheckoutForm::retrieve($request, $options);

        $status        = $checkoutForm->getStatus();        // 'success' | 'failure'
        $paymentStatus = $checkoutForm->getPaymentStatus(); // 'SUCCESS' | 'FAILURE'

        $isSuccess = ($status === 'success' && $paymentStatus === 'SUCCESS');

        if (!$isSuccess) {
            $errorMsg   = $checkoutForm->getErrorMessage() ?: 'Ödeme bankanız veya İyzico tarafından onaylanmadı.';
            $errorCode  = $checkoutForm->getErrorCode();
            $errorGroup = $checkoutForm->getErrorGroup();

            return [
                'success'        => false,
                'conversationId' => $checkoutForm->getConversationId(),
                'status'         => $status,
                'paymentStatus'  => $paymentStatus,
                'errorMessage'   => $errorMsg,
                'errorCode'      => $errorCode,
                'errorGroup'     => $errorGroup,
                'token'          => $token,
            ];
        }

        $paidPrice   = (float) $checkoutForm->getPaidPrice();
        $iyziComm    = (float) ($checkoutForm->getIyziCommissionRateAmount() ?? 0);
        $merchantPay = $iyziComm > 0 ? round($paidPrice - $iyziComm, 2) : $paidPrice;

        return [
            'success'              => true,
            'conversationId'       => $checkoutForm->getConversationId(),
            'paymentId'            => $checkoutForm->getPaymentId(),
            'paidPrice'            => $paidPrice,
            'installment'          => (int) ($checkoutForm->getInstallment() ?: 1),
            'cardFamily'           => $checkoutForm->getCardFamily() ?? '',
            'cardLastFour'         => $checkoutForm->getLastFourDigits() ?? '',
            'cardType'             => $checkoutForm->getCardType() ?? '',
            'cardAssociation'      => $checkoutForm->getCardAssociation() ?? '',
            'merchantPayoutAmount' => $merchantPay,
            'token'                => $token,
        ];
    }

    /**
     * BIN numarasına göre taksit sorgulama (Opsiyonel / Bilgilendirme amaçlı).
     */
    public function getInstallmentOptions(float $price, string $binNumber): array
    {
        $options = $this->getOptions();

        $request = new RetrieveInstallmentInfoRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId('INST_' . time());
        $request->setBinNumber(preg_replace('/\s+/', '', substr($binNumber, 0, 6)));
        $request->setPrice(number_format($price, 2, '.', ''));

        $installmentInfo = InstallmentInfo::retrieve($request, $options);

        if ($installmentInfo->getStatus() !== 'success') {
            return $this->defaultInstallments($price);
        }

        $details = $installmentInfo->getInstallmentDetails();
        if (empty($details)) {
            return $this->defaultInstallments($price);
        }

        $result = [];
        foreach ($details[0]->getInstallmentPrices() as $installmentPrice) {
            $count = (int) $installmentPrice->getInstallmentNumber();
            $result[] = [
                'count'      => $count,
                'price'      => (float) $installmentPrice->getInstallmentPrice(),
                'totalPrice' => (float) $installmentPrice->getTotalPrice(),
                'label'      => $count === 1 ? 'Tek Çekim' : "{$count} Taksit",
            ];
        }

        return !empty($result) ? $result : $this->defaultInstallments($price);
    }

    private function defaultInstallments(float $price): array
    {
        return [
            ['count' => 1, 'price' => $price, 'totalPrice' => $price, 'label' => 'Tek Çekim'],
        ];
    }

    /**
     * Telefon numarasını İyzico formatına getirir: +905XXXXXXXXX
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 10) {
            return '+90' . $phone;
        }
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            return '+9' . $phone;
        }
        if (strlen($phone) === 12 && str_starts_with($phone, '90')) {
            return '+' . $phone;
        }
        return '+90' . $phone;
    }
}
