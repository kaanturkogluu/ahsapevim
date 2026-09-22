<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Jobs\SendNewOrderNotificationJob;
use App\Services\IyzicoService;
use App\Services\PaymentLogService;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        // If cart is empty, try restoring items from failed/pending order in session
        if (empty($cart)) {
            $pendingId = session()->get('pending_order_id') ?: session()->get('order_id');
            if ($pendingId) {
                $failedOrder = Order::with('items.product')->find($pendingId);
                if ($failedOrder && in_array($failedOrder->status, ['pending', 'failed'])) {
                    $restoredCart = [];
                    foreach ($failedOrder->items as $item) {
                        $features = is_array($item->features) ? $item->features : (json_decode($item->features, true) ?: []);
                        $fImg = $features['front_image'] ?? ($features['custom_image'] ?? null);
                        $bImg = $features['back_image'] ?? null;
                        $preview = $features['custom_preview'] ?? null;
                        
                        $uniqueSeed = ($fImg ?: '') . ($bImg ?: '') . ($preview ?: '');
                        $cartKey = $item->product_id . ($uniqueSeed ? '_' . md5($uniqueSeed) : '');
                        
                        $restoredCart[$cartKey] = [
                            'product_id' => $item->product_id,
                            'name' => $item->product ? $item->product->name : 'Ahşap Ürün',
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                            'image' => $preview ? url($preview) : ($fImg ? url($fImg) : ($item->product ? $item->product->image : null)),
                            'custom_image_front' => $fImg ? url($fImg) : null,
                            'custom_image_back' => $bImg ? url($bImg) : null,
                            'custom_image' => $fImg ? url($fImg) : null,
                            'custom_preview' => $preview ? url($preview) : null,
                        ];
                    }
                    if (!empty($restoredCart)) {
                        session()->put('cart', $restoredCart);
                        $cart = $restoredCart;
                    }
                }
            }
        }

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
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:1000',
            'city'           => 'nullable|string|max:100',
            'district'       => 'nullable|string|max:100',
            'payment_method' => 'required|in:eft,credit_card',
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

        $paymentMethod = $request->input('payment_method', 'eft');

        // ─── Kredi Kartı ile Ödeme (İyzico Checkout Form) ─────────────────────
        if ($paymentMethod === 'credit_card') {
            // Siparişi önce 'pending' olarak oluştur
            $order = Order::create([
                'tracking_code'   => Order::generateTrackingCode(),
                'user_id'         => auth()->id(),
                'name'            => $request->name,
                'email'           => $request->email,
                'phone'           => $request->phone,
                'address'         => $request->address,
                'city'            => $request->city ?: 'Manisa',
                'district'        => $request->district ?: 'Merkez',
                'identity_number' => $request->identity_number ?: '11111111111',
                'note'            => $request->note,
                'total_amount'    => $totalAmount,
                'status'          => 'pending',
                'payment_id'      => 'CC_INIT_' . time(),
                'installment'     => 1,
            ]);

            // Sipariş kalemlerini kaydet
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'features'   => [
                        'front_image'    => $item['custom_image_front'] ?? ($item['custom_image'] ?? null),
                        'back_image'     => $item['custom_image_back'] ?? null,
                        'custom_image'   => $item['custom_image'] ?? null,
                        'custom_preview' => $item['custom_preview'] ?? null,
                        'is_gift'        => $item['is_gift'] ?? false,
                        'gift_note'      => $item['gift_note'] ?? null,
                    ]
                ]);
            }

            // İyzico Resmi Checkout Formu Başlat
            $iyzicoService = app(IyzicoService::class);
            $result = $iyzicoService->initiateCheckoutForm($order);

            if (!$result['success']) {
                // Form başlatılamadı — geçici siparişi sil ve geri dön
                $order->items()->delete();
                $order->delete();
                return redirect()->back()
                    ->with('error', '💳 ' . ($result['errorMessage'] ?? 'Ödeme formu oluşturulamadı.'))
                    ->withInput();
            }

            // Siparişe token'ı kaydet
            $order->update(['payment_id' => $result['token']]);

            // Sepet oturumuna pending_order_id kaydet (callback'te doğrulamak için)
            session()->put('pending_order_id', $order->id);
            session()->put('iyzico_token', $result['token']);

            // İyzico ödeme ekranını göster
            return view('checkout.iyzico_form', [
                'order'               => $order,
                'checkoutFormContent' => $result['checkoutFormContent'],
                'paymentPageUrl'      => $result['paymentPageUrl'] ?? null,
            ]);
        }

        // ─── EFT / Havale ile Ödeme ──────────────────────────────────────────
        $order = Order::create([
            'tracking_code'   => Order::generateTrackingCode(),
            'user_id'         => auth()->id(),
            'name'            => $request->name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'address'         => $request->address,
            'city'            => $request->city ?: 'Manisa',
            'district'        => $request->district ?: 'Merkez',
            'identity_number' => $request->identity_number ?: '11111111111',
            'note'            => $request->note,
            'total_amount'    => $totalAmount,
            'status'          => 'pending',
            'payment_id'      => 'EFT_' . time(),
        ]);

        // Save order items with customization images
        foreach ($cart as $key => $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'features'   => [
                    'front_image'    => $item['custom_image_front'] ?? ($item['custom_image'] ?? null),
                    'back_image'     => $item['custom_image_back'] ?? null,
                    'custom_image'   => $item['custom_image'] ?? null,
                    'custom_preview' => $item['custom_preview'] ?? null,
                    'is_gift'        => $item['is_gift'] ?? false,
                    'gift_note'      => $item['gift_note'] ?? null,
                ]
            ]);
        }

        // Decrement stock for each item
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->decrement('stock', $item->quantity);
            }
        }

        // Clear cart session
        session()->forget('cart');
        session()->forget('pending_order_id');

        // Dispatch queued notification job for admin (email & SMS) and customer
        try {
            SendNewOrderNotificationJob::dispatch($order->id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('SendNewOrderNotificationJob dispatch error: ' . $e->getMessage());
        }

        // Okunaklı Loglama
        PaymentLogService::logEftOrder($order);

        return redirect()->route('checkout.result')->with([
            'status'   => 'success',
            'order_id' => $order->id,
            'is_eft'   => true,
        ]);
    }

    /**
     * Iyzico Callback — Ödeme formu tamamlandığında İyzico bu endpoint'i POST eder.
     * CSRF muafiyeti: routes/web.php ve bootstrap/app.php'de tanımlı.
     */
    public function callback3DS(Request $request)
    {
        $token = $request->input('token');

        if (empty($token)) {
            PaymentLogService::logPaymentFailure(null, '', 'İyzico callback endpointine geçersiz veya boş token ile ulaşıldı.');
            return redirect()->route('checkout.result')->with([
                'status'        => 'failed',
                'error_message' => 'Ödeme oturum bilgisi bulunamadı. Lütfen tekrar deneyiniz.',
            ]);
        }

        $iyzicoService = app(IyzicoService::class);
        $result = $iyzicoService->retrieveCheckoutForm($token);

        // Siparişi belirle: Önce İyzico conversationId (Sipariş ID), sonra token veya session
        $orderId = $result['conversationId'] ?? session('pending_order_id');
        $order = null;
        if ($orderId) {
            $order = Order::with('items.product')->find($orderId);
        }
        if (!$order) {
            $order = Order::with('items.product')->where('payment_id', $token)->first();
        }

        // Ödeme başarısız
        if (!$result['success']) {
            $errorMessage = $result['errorMessage'] ?? 'Ödeme bankanız veya İyzico tarafından onaylanmadı.';

            if ($order && $order->status === 'pending') {
                $order->update([
                    'status'               => 'failed',
                    'payment_error_reason' => $errorMessage,
                ]);
            }

            PaymentLogService::logPaymentFailure(
                $order,
                $token,
                $errorMessage,
                $result['errorCode'] ?? null,
                $result['errorGroup'] ?? null
            );

            session()->forget('pending_order_id');
            session()->forget('iyzico_token');

            return redirect()->route('checkout.result')->with([
                'status'        => 'failed',
                'error_message' => $errorMessage,
            ]);
        }

        // Sipariş bulunamadı koruması
        if (!$order) {
            PaymentLogService::logPaymentFailure(null, $token, 'İyzico ödemesi başarılı fakat sistemde eşleşen sipariş bulunamadı.');
            return redirect()->route('checkout.result')->with([
                'status'        => 'failed',
                'error_message' => 'Sipariş kaydı bulunamadı. Lütfen müşteri hizmetleri ile iletişime geçiniz.',
            ]);
        }

        // ─── ÖDEME BAŞARILI ─────────────────────────────────────────────────
        $order->update([
            'status'                 => 'paid',
            'payment_id'             => $result['paymentId'],
            'paid_price'             => $result['paidPrice'],
            'installment'            => $result['installment'],
            'card_family'            => $result['cardFamily'],
            'card_last_four'         => $result['cardLastFour'],
            'merchant_payout_amount' => $result['merchantPayoutAmount'],
        ]);

        // Stok düş
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->decrement('stock', $item->quantity);
            }
        }

        // Sepet oturumunu temizle
        session()->forget(['cart', 'pending_order_id', 'iyzico_token']);

        // ─── BİLDİRİMLER ────────────────────────────────────────────────────
        $notifications = [];

        // 1. Admin Bildirimi (e-posta + SMS)
        try {
            SendNewOrderNotificationJob::dispatch($order->id);
            $notifications['Yönetici Bildirimi (Kuyruk)'] = 'Tetiklendi';
        } catch (\Throwable $e) {
            $notifications['Yönetici Bildirimi'] = 'Hata: ' . $e->getMessage();
            \Illuminate\Support\Facades\Log::error('SendNewOrderNotificationJob dispatch error (iyzico): ' . $e->getMessage());
        }

        // 2. Müşteriye Ödeme Onayı E-postası
        try {
            $data = [
                'user_name'        => $order->name,
                'user_email'       => $order->email,
                'order_id'         => $order->id,
                'tracking_code'    => $order->tracking_code ?: 'AHS-' . $order->id,
                'total_amount'     => number_format($order->total_amount, 2, ',', '.'),
                'card_family'      => $order->card_family ?: '',
                'card_last_four'   => $order->card_last_four ?: '',
                'installment'      => $order->installment > 1 ? $order->installment . ' Taksit' : 'Tek Çekim',
                'delivery_address' => $order->address . ' (' . ($order->city ?: 'Manisa') . ')',
                'product_details'  => $this->formatOrderItemsHtmlSimple($order),
                'site_name'        => 'AhşapEvim',
            ];

            \Illuminate\Support\Facades\Mail::to($order->email)
                ->queue(new \App\Mail\DynamicMail('order_paid', $data));

            app(\App\Services\MailService::class)->logMailable(
                $order->email,
                "Ödemeniz Onaylandı (#{$order->id})",
                "Kredi kartı ödemesi onaylandı.",
                'success',
                null,
                $order->id
            );
            $notifications['Müşteri E-Posta'] = "Kuyruğa Eklendi ({$order->email})";
        } catch (\Throwable $e) {
            $notifications['Müşteri E-Posta'] = 'Hata: ' . $e->getMessage();
            \Illuminate\Support\Facades\Log::error('Iyzico payment confirmation email error: ' . $e->getMessage());
        }

        // 3. Müşteriye SMS
        try {
            $installmentText = $order->installment > 1 ? " ({$order->installment} taksit)" : '';
            $smsMessage = "Sayın {$order->name}, #{$order->id} numaralı siparişinizin kredi kartı ödemesi{$installmentText} onaylandı. Siparişiniz hazırlanmaya başlandı. AhşapEvim";
            app(\App\Services\NetgsmService::class)->sendSms($order->phone, $smsMessage, $order->id, 'automated');
            $notifications['Müşteri SMS'] = "Gönderildi ({$order->phone})";
        } catch (\Throwable $e) {
            $notifications['Müşteri SMS'] = 'Hata: ' . $e->getMessage();
            \Illuminate\Support\Facades\Log::error('Iyzico payment confirmation SMS error: ' . $e->getMessage());
        }

        // Okunaklı Başarılı Ödeme Logu
        PaymentLogService::logPaymentSuccess($order, $result, $notifications);

        return redirect()->route('checkout.result')->with([
            'status'       => 'success',
            'order_id'     => $order->id,
            'is_eft'       => false,
            'card_family'  => $result['cardFamily'],
            'installment'  => $result['installment'],
        ]);

        return redirect()->route('checkout.result')->with([
            'status'       => 'success',
            'order_id'     => $order->id,
            'is_eft'       => false,
            'card_family'  => $result['cardFamily'],
            'installment'  => $result['installment'],
        ]);
    }

    /**
     * Iyzico taksit seçeneklerini AJAX ile döner.
     */
    public function installmentInfo(Request $request)
    {
        $request->validate([
            'bin_number' => 'required|string|min:6',
            'price'      => 'required|numeric|min:1',
        ]);

        try {
            $iyzicoService = app(IyzicoService::class);
            $installments  = $iyzicoService->getInstallmentOptions(
                (float) $request->price,
                $request->bin_number
            );
            return response()->json(['success' => true, 'installments' => $installments]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Installment info error: ' . $e->getMessage());
            return response()->json(['success' => false, 'installments' => [
                ['count' => 1, 'price' => $request->price, 'totalPrice' => $request->price, 'label' => 'Tek Çekim'],
            ]]);
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

    private function formatOrderItemsHtmlSimple($order): string
    {
        $html = '<table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px;">';
        $html .= '<thead><tr style="background-color: #F5F2EB; text-align: left; color: #666;"><th style="padding: 8px; border-bottom: 1px solid #EFEAE0;">Ürün</th><th style="padding: 8px; text-align: center; border-bottom: 1px solid #EFEAE0;">Adet</th><th style="padding: 8px; text-align: right; border-bottom: 1px solid #EFEAE0;">Fiyat</th></tr></thead>';
        $html .= '<tbody>';
        foreach ($order->items as $item) {
            $pName = e($item->product ? $item->product->name : 'Ahşap Ürün');
            $qty   = intval($item->quantity);
            $price = number_format($item->price * $qty, 2, ',', '.');
            $html .= "<tr><td style=\"padding: 8px; border-bottom: 1px solid #EFEAE0;\">{$pName}</td><td style=\"padding: 8px; text-align: center; border-bottom: 1px solid #EFEAE0;\">{$qty}</td><td style=\"padding: 8px; text-align: right; border-bottom: 1px solid #EFEAE0;\">₺{$price}</td></tr>";
        }
        $html .= '</tbody></table>';
        return $html;
    }

    private function isValidTcNo($tc)
    {
        $tc = preg_replace('/[^0-9]/', '', $tc);
        if (strlen($tc) !== 11 || $tc[0] === '0') {
            return false;
        }

        $digits = array_map('intval', str_split($tc));

        $oddSum  = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8];
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
