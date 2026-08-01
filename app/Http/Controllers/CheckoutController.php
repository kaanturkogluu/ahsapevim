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
        ]);

        // Calculate total amount
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        // Create temporary order
        $order = Order::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);
        $order->identity_number = $request->identity_number ?: '11111111111';

        // Save order items
        foreach ($cart as $key => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'features' => [
                    'custom_image' => $item['custom_image'] ?? null,
                ]
            ]);
        }

        // Initialize Iyzico Checkout Form
        $callbackUrl = route('checkout.callback');
        $iyzicoForm = $this->iyzico->initializeCheckoutForm($order, $cart, $callbackUrl);

        if ($iyzicoForm->getStatus() == 'success') {
            return view('checkout.payment', [
                'formContent' => $iyzicoForm->getCheckoutFormContent(),
                'order' => $order
            ]);
        } else {
            // Delete pending order if payment initialization failed
            $order->delete();
            return redirect()->back()->with('error', 'Ödeme sistemi başlatılamadı: ' . $iyzicoForm->getErrorMessage())->withInput();
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

        $payment = $this->iyzico->retrieveCheckoutForm($token);
        $orderId = $payment->getConversationId();
        $order = Order::findOrFail($orderId);

        if ($payment->getPaymentStatus() == 'SUCCESS') {
            // Update order status to paid
            $order->update([
                'status' => 'paid',
                'payment_id' => $payment->getPaymentId(),
            ]);

            // Clear Cart Session
            session()->forget('cart');

            // Send SMS to customer and shop owner via Netgsm
            $netgsm = app(NetgsmService::class);
            
            // Clean Turkish characters for SMS compliance
            $customerMsg = "Degerli musterimiz, #" . $order->id . " nolu siparisiniz basariyla alinmistir. Siparisiniz en kisa surede kargolanacaktir. Bizi tercih ettiginiz icin tesekkur ederiz.";
            $netgsm->sendSms($order->phone, $customerMsg);

            $adminPhone = config('services.netgsm.admin_phone');
            if ($adminPhone) {
                $adminMsg = "Yeni siparis alindi! Siparis No: #" . $order->id . ", Mutfak Tutar: " . number_format($order->total_amount, 2, ',', '.') . " TL. Musteri: " . $order->name;
                $netgsm->sendSms($adminPhone, $adminMsg);
            }

            return redirect()->route('checkout.result')->with([
                'status' => 'success',
                'order_id' => $order->id
            ]);
        } else {
            // Update order status to failed
            $order->update([
                'status' => 'failed',
            ]);

            return redirect()->route('checkout.result')->with([
                'status' => 'error',
                'message' => $payment->getErrorMessage() ?: 'Ödeme işlemi başarısız oldu.'
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
}
