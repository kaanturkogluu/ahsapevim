<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\IyzicoService;
use App\Services\NetgsmService;

class CheckoutController extends Controller
{
    protected $iyzico;

    public function __construct(IyzicoService $iyzico)
    {
        $this->iyzico = $iyzico;
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Sepetiniz boş olduğu için ödeme sayfasına gidemezsiniz.');
        }

        return view('checkout.index', compact('cart'));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Sepetiniz boş.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
        ]);

        $tc = $request->input('identity_number');
        if (!empty($tc) && !$this->isValidTcNo($tc)) {
            return redirect()->back()->with('error', 'Girdiğiniz T.C. Kimlik Numarası matematiksel olarak geçersizdir. Lütfen kontrol ediniz.')->withInput();
        }

        // Calculate total amount
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        // Create temporary order with unique tracking code
        $order = Order::create([
            'tracking_code' => Order::generateTrackingCode(),
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city ?: 'Manisa',
            'district' => $request->district ?: 'Merkez',
            'identity_number' => $request->identity_number ?: '11111111111',
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);

        // Save order items with front and back customization images
        foreach ($cart as $key => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'features' => [
                    'front_image' => $item['custom_image_front'] ?? ($item['custom_image'] ?? null),
                    'back_image' => $item['custom_image_back'] ?? null,
                    'custom_image' => $item['custom_image'] ?? null,
                    'custom_preview' => $item['custom_preview'] ?? null,
                ]
            ]);
        }

        // Save pending order ID in session as fallback
        session()->put('pending_order_id', $order->id);

        $paymentMethod = $request->input('payment_method', 'card');

        // If customer selected Havale / EFT
        if (in_array($paymentMethod, ['eft', 'cod'])) {
            $order->update([
                'status' => 'pending',
                'payment_id' => 'EFT_' . time(),
            ]);

            session()->forget('cart');
            session()->forget('pending_order_id');

            // Decrement stock for each item (EFT)
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            $orderData = [
                'user_name' => $order->name,
                'order_id' => $order->id,
                'tracking_code' => $order->tracking_code ?: 'AHS-' . $order->id,
                'total_amount' => number_format($order->total_amount, 2, ',', '.'),
                'delivery_address' => $order->address . ' (' . ($order->city ?: 'Manisa') . ')',
            ];

            try {
                \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_success', $orderData));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Mail Kuyruğa Gönderim Hatası: ' . $e->getMessage());
            }

            return redirect()->route('checkout.result')->with([
                'status' => 'success',
                'order_id' => $order->id,
                'is_eft' => true,
            ]);
        }

        // Standard: Kredi / Banka Kartı Ödemesi (Iyzico Güvenli Formu Göster)
        try {
            $callbackUrl = route('checkout.callback');
            $iyzicoForm = $this->iyzico->initializeCheckoutForm($order, $cart, $callbackUrl);

            if ($iyzicoForm && $iyzicoForm->getStatus() == 'success') {
                return view('checkout.payment', [
                    'formContent' => $iyzicoForm->getCheckoutFormContent(),
                    'order' => $order
                ]);
            } else {
                $errorMsg = $iyzicoForm ? $iyzicoForm->getErrorMessage() : 'Ödeme kapısına erişilemedi.';
                return redirect()->back()->with('error', 'Kredi Kartı ödeme formu yüklenemedi: ' . $errorMsg)->withInput();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Iyzico Init Exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ödeme sistemi başlatılırken bir hata oluştu: ' . $e->getMessage())->withInput();
        }
    }

    public function callback(Request $request)
    {
        $token = $request->input('token');
        if (empty($token)) {
            return redirect()->route('checkout.result')->with([
                'status' => 'error',
                'message' => 'Geçersiz ödeme isteği (Token bulunamadı).'
            ]);
        }

        try {
            $payment = $this->iyzico->retrieveCheckoutForm($token);

            // Log detailed payment callback info for debugging
            \Illuminate\Support\Facades\Log::info('Iyzico Callback Response:', [
                'status' => $payment ? $payment->getStatus() : null,
                'paymentStatus' => $payment ? $payment->getPaymentStatus() : null,
                'errorCode' => $payment ? $payment->getErrorCode() : null,
                'errorMessage' => $payment ? $payment->getErrorMessage() : null,
                'conversationId' => $payment ? $payment->getConversationId() : null,
            ]);

            // 4-Tier Robust Order Lookup Strategy
            $conversationId = $payment ? $payment->getConversationId() : null;
            $sessionOrderId = session()->get('pending_order_id');
            $targetOrderId = $conversationId ?: $sessionOrderId;

            $order = null;
            if ($targetOrderId) {
                $order = Order::find($targetOrderId);
            }

            if (!$order && auth()->check()) {
                $order = Order::where('user_id', auth()->id())->whereIn('status', ['pending', 'failed'])->latest()->first();
            }

            if (!$order) {
                $order = Order::whereIn('status', ['pending', 'failed'])->latest()->first();
            }

            if (!$order) {
                return redirect()->route('checkout.result')->with([
                    'status' => 'error',
                    'message' => 'Sipariş kaydı bulunamadı (Sipariş No: #' . ($targetOrderId ?: 'Sistem') . ').'
                ]);
            }

            // Restore user login session if cross-site Iyzico POST stripped session cookie
            if ($order->user_id && !auth()->check()) {
                \Illuminate\Support\Facades\Auth::loginUsingId($order->user_id);
            }

            $isSuccess = $payment && (
                ($payment->getStatus() === 'success' && $payment->getPaymentStatus() === 'SUCCESS') ||
                ($payment->getStatus() === 'success' && empty($payment->getErrorCode()))
            );

            if ($isSuccess) {
                // Calculate merchant payout total from items if available
                $merchantPayout = 0;
                if ($payment->getPaymentItems()) {
                    foreach ($payment->getPaymentItems() as $pItem) {
                        $merchantPayout += (float)($pItem->getMerchantPayoutAmount() ?? 0);
                    }
                }
                $paidPrice = (float)($payment->getPaidPrice() ?: $order->total_amount);
                if ($merchantPayout <= 0) {
                    $merchantPayout = $paidPrice;
                }

                // Update order status to paid with financial details
                $order->update([
                    'status' => 'paid',
                    'payment_id' => $payment->getPaymentId() ?: ('IYZ_' . time()),
                    'paid_price' => $paidPrice,
                    'installment' => (int)($payment->getInstallment() ?: 1),
                    'merchant_payout_amount' => round($merchantPayout, 2),
                    'card_family' => $payment->getCardFamily(),
                    'card_last_four' => $payment->getLastFourDigits(),
                    'payment_error_reason' => null,
                ]);

                // Decrement stock for each item
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->decrement('stock', $item->quantity);
                    }
                }

                // Clear Cart Session
                session()->forget('cart');

                // Queue Order Confirmation Email to Customer using Dynamic Mail Template
                $orderData = [
                    'user_name' => $order->name,
                    'order_id' => $order->id,
                    'tracking_code' => $order->tracking_code ?: 'AHS-' . $order->id,
                    'total_amount' => number_format($order->total_amount, 2, ',', '.'),
                    'delivery_address' => $order->address . ' (' . ($order->city ?: 'Manisa') . ')',
                ];

                try {
                    \Illuminate\Support\Facades\Mail::to($order->email)->queue(new \App\Mail\DynamicMail('order_success', $orderData));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Mail Kuyruğa Gönderim Hatası: ' . $e->getMessage());
                }

                // Send SMS to customer and shop owner via Netgsm
                try {
                    $netgsm = app(NetgsmService::class);
                    $customerMsg = "Degerli musterimiz, #" . $order->id . " nolu siparisiniz basariyla alinmistir. Siparisiniz en kisa surede kargolanacaktır. Bizi tercih ettiginiz icin tesekkur ederiz.";
                    $netgsm->sendSms($order->phone, $customerMsg);

                    $adminPhone = config('services.netgsm.admin_phone');
                    if ($adminPhone) {
                        $adminMsg = "Yeni siparis alindi! Siparis No: #" . $order->id . ", Tutar: " . number_format($order->total_amount, 2, ',', '.') . " TL. Musteri: " . $order->name;
                        $netgsm->sendSms($adminPhone, $adminMsg);
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('SMS Gönderim Hatası: ' . $e->getMessage());
                }

                return redirect()->route('checkout.result')->with([
                    'status' => 'success',
                    'order_id' => $order->id
                ]);
            } else {
                $errorMessage = $payment ? $payment->getErrorMessage() : null;
                if (empty($errorMessage)) {
                    $errorMessage = 'Iyzico ödeme doğrulaması başarısız oldu (Hata Kodu: ' . ($payment ? $payment->getErrorCode() : 'Bilinmiyor') . '). Sandbox test kartı SMS şifresi ekranında 123456 veya 111111 giriniz.';
                }

                // Update order status to failed with failure reason
                $order->update([
                    'status' => 'failed',
                    'payment_error_reason' => $errorMessage,
                ]);

                return redirect()->route('checkout.result')->with([
                    'status' => 'error',
                    'message' => $errorMessage
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Ödeme Callback Hatası: ' . $e->getMessage());
            return redirect()->route('checkout.result')->with([
                'status' => 'error',
                'message' => 'Ödeme doğrulama sırasında bir sistem hatası oluştu: ' . $e->getMessage()
            ]);
        }
    }

    public function result()
    {
        $status = session('status');
        if (empty($status)) {
            return redirect()->route('cart.index');
        }

        return view('checkout.result');
    }

    private function isValidTcNo($tc)
    {
        $tc = preg_replace('/[^0-9]/', '', $tc);
        if (strlen($tc) !== 11 || $tc[0] === '0') {
            return false;
        }

        $digits = array_map('intval', str_split($tc));

        $oddSum = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8];
        $evenSum = $digits[1] + $digits[3] + $digits[5] + $digits[7];

        $d10 = (($oddSum * 7) - $evenSum) % 10;
        if ($d10 < 0) $d10 += 10;
        if ($d10 !== $digits[9]) {
            return false;
        }

        $totalSum = array_sum(array_slice($digits, 0, 10));
        if (($totalSum % 10) !== $digits[10]) {
            return false;
        }

        return true;
    }
}
