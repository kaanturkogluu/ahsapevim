<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Services\MailService;
use App\Services\NetgsmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::getAllGrouped();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'admin_email'            => 'nullable|email|max:255',
            'admin_phone'            => 'nullable|string|max:30',
            'admin_sms_template'     => 'nullable|string|max:500',
            'admin_email_subject'    => 'nullable|string|max:255',
            'site_title'             => 'nullable|string|max:255',
            'contact_phone'          => 'nullable|string|max:30',
            'contact_whatsapp'       => 'nullable|string|max:30',
            'contact_email'          => 'nullable|email|max:255',
            'contact_address'        => 'nullable|string|max:500',
            'netgsm_usercode'        => 'nullable|string|max:100',
            'netgsm_password'        => 'nullable|string|max:100',
            'netgsm_header'          => 'nullable|string|max:50',
        ]);

        // Sipariş & Bildirim Ayarları
        Setting::set('admin_email', $request->input('admin_email', ''), 'notifications');
        Setting::set('admin_phone', $request->input('admin_phone', ''), 'notifications');
        Setting::set('notify_admin_email', $request->has('notify_admin_email') ? '1' : '0', 'notifications');
        Setting::set('notify_admin_sms', $request->has('notify_admin_sms') ? '1' : '0', 'notifications');
        Setting::set('notify_customer_email', $request->has('notify_customer_email') ? '1' : '0', 'notifications');
        Setting::set('notify_customer_sms', $request->has('notify_customer_sms') ? '1' : '0', 'notifications');
        Setting::set('admin_sms_template', $request->input('admin_sms_template', ''), 'notifications');
        Setting::set('admin_email_subject', $request->input('admin_email_subject', ''), 'notifications');

        // Netgsm SMS Ayarları
        Setting::set('netgsm_usercode', $request->input('netgsm_usercode', ''), 'sms');
        Setting::set('netgsm_password', $request->input('netgsm_password', ''), 'sms');
        Setting::set('netgsm_header', $request->input('netgsm_header', ''), 'sms');

        // Genel & İletişim Bilgileri
        Setting::set('site_title', $request->input('site_title', ''), 'general');
        Setting::set('contact_phone', $request->input('contact_phone', ''), 'general');
        Setting::set('contact_whatsapp', $request->input('contact_whatsapp', ''), 'general');
        Setting::set('contact_email', $request->input('contact_email', ''), 'general');
        Setting::set('contact_address', $request->input('contact_address', ''), 'general');

        return redirect()->route('admin.settings.index')->with('success', 'Sistem ayarları ve bildirim yapılandırması başarıyla kaydedildi.');
    }

    public function testSms(Request $request, NetgsmService $netgsm)
    {
        $phone = $request->input('test_phone') ?: Setting::get('admin_phone', config('services.netgsm.admin_phone'));

        if (empty($phone)) {
            return redirect()->back()->with('error', 'Test SMS gönderilemedi: Telefon numarası girilmedi veya ayarlanmadı.');
        }

        $message = "AhsapEvim Test SMS: Bildirim sisteminiz basariyla calismaktadir. Tarih: " . now()->format('d.m.Y H:i:s');
        $result = $netgsm->sendSms($phone, $message, null, 'manual');

        if ($result) {
            return redirect()->back()->with('success', "Test SMS mesajı {$phone} numarasına başarıyla iletildi.");
        } else {
            return redirect()->back()->with('error', "Test SMS gönderimi başarısız oldu. Lütfen Netgsm kullanıcı adı, şifre ve başlık (header) bilgilerinizi kontrol ediniz.");
        }
    }

    public function testEmail(Request $request, MailService $mailService)
    {
        $email = $request->input('test_email') ?: Setting::get('admin_email', config('mail.from.address'));

        if (empty($email)) {
            return redirect()->back()->with('error', 'Test E-Postası gönderilemedi: E-posta adresi girilmedi veya ayarlanmadı.');
        }

        $subject = "AhşapEvim — Test E-Posta Bildirimi";
        $body = "<p>Merhaba,</p><p>Bu bir test e-postasıdır. AhşapEvim sipariş bildirim sistemi ve SMTP e-posta sunucunuz başarıyla çalışmaktadır.</p><p><strong>Gönderim Zamanı:</strong> " . now()->format('d.m.Y H:i:s') . "</p>";

        $result = $mailService->sendMail($email, $subject, $body, null, 'manual');

        if ($result) {
            return redirect()->back()->with('success', "Test e-postası {$email} adresine başarıyla gönderildi.");
        } else {
            return redirect()->back()->with('error', "Test e-postası gönderilirken bir hata oluştu. Lütfen SMTP yapılandırmanızı kontrol ediniz.");
        }
    }

    /**
     * Canlı admin bildirim zili için son siparişler JSON API endpoint'i
     */
    public function recentOrdersApi(Request $request)
    {
        try {
            $lastOrderId = (int)$request->input('last_order_id', 0);
            
            $recentOrders = Order::latest()
                ->take(6)
                ->get()
                ->map(function ($order) {
                    return [
                        'id'            => $order->id,
                        'name'          => $order->name,
                        'total_amount'  => number_format($order->total_amount, 2, ',', '.') . ' ₺',
                        'status'        => $order->status,
                        'time_ago'      => $order->created_at ? $order->created_at->diffForHumans() : 'Az önce',
                        'url'           => route('admin.orders.show', $order->id),
                        'is_new'        => $order->created_at ? $order->created_at->greaterThan(now()->subHours(24)) : false,
                    ];
                });

            $newCount = Order::where('created_at', '>=', now()->subHours(48))
                ->whereIn('status', ['pending', 'paid', 'preparing'])
                ->count();

            $hasNewer = false;
            if ($lastOrderId > 0) {
                $hasNewer = Order::where('id', '>', $lastOrderId)->exists();
            }

            return response()->json([
                'status'        => 'success',
                'orders'        => $recentOrders,
                'count'         => $newCount,
                'has_newer'     => $hasNewer,
                'latest_id'     => $recentOrders->first()['id'] ?? 0,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
