<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailLog;
use App\Models\SmsLog;
use App\Services\MailService;
use App\Services\NetgsmService;
use Illuminate\Http\Request;

class MessageLogController extends Controller
{
    public function mailLogs(Request $request)
    {
        $query = MailLog::with('order')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('to_email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.logs.mail', compact('logs'));
    }

    public function smsLogs(Request $request)
    {
        $query = SmsLog::with('order')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('to_phone', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('response_code', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.logs.sms', compact('logs'));
    }

    public function sendManualMail(Request $request)
    {
        $request->validate([
            'to_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $mailService = app(MailService::class);
        $success = $mailService->sendMail(
            $request->to_email,
            $request->subject,
            $request->body,
            $request->order_id,
            'manual'
        );

        if ($success) {
            return redirect()->back()->with('success', "'{$request->to_email}' adresine manuel e-posta gönderimi başarıyla kuyruğa (sıraya) eklendi ve arka planda gönderiliyor.");
        }

        return redirect()->back()->with('error', "'{$request->to_email}' adresine e-posta gönderimi başarısız oldu! Log detaylarını inceleyebilirsiniz.");
    }

    public function sendManualSms(Request $request)
    {
        // Validasyon öncesi telefonu temizle: boşluk, tire, parantez, + vb. kaldır
        // "0532 123 45 67" → "05321234567"
        $rawPhone   = preg_replace('/[^0-9+]/', '', $request->input('to_phone', ''));
        $request->merge(['to_phone' => $rawPhone]);

        $request->validate([
            // Türkiye GSM: 05XXXXXXXXX (11 hane), 905XXXXXXXXX (12 hane), +905XXXXXXXXX
            'to_phone' => ['required', 'string', 'regex:/^(\+?90|0)5[0-9]{9}$/'],
            'message'  => 'required|string|max:918',
            'order_id' => 'nullable|exists:orders,id',
        ], [
            'to_phone.regex' => 'Geçerli bir Türk GSM numarası girin. Örn: 05321234567 veya +905321234567',
            'message.max'    => 'Mesaj 918 karakteri aşamaz (6 SMS).',
        ]);

        $netgsm     = app(NetgsmService::class);
        $cleanPhone = $netgsm->formatPhone($request->to_phone);

        // Spam koruması: Aynı numaraya son 1 dakikada 3'ten fazla manuel SMS engellensin
        $recentCount = SmsLog::where('to_phone', $cleanPhone ?? $request->to_phone)
            ->where('type', 'manual')
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        if ($recentCount >= 3) {
            return redirect()->back()->with(
                'error',
                "'{$request->to_phone}' numarasına son 1 dakika içinde çok fazla SMS gönderildi. Lütfen bekleyin."
            );
        }

        $success = $netgsm->sendSms(
            $request->to_phone,
            $request->message,
            $request->order_id,
            'manual'
        );

        if ($success) {
            return redirect()->back()->with('success', "'{$request->to_phone}' numarasına SMS başarıyla iletildi ve loglandı.");
        }

        return redirect()->back()->with('error', "'{$request->to_phone}' numarasına SMS gönderimi başarısız oldu! Hata detayı için SMS Loglarını inceleyebilirsiniz.");
    }

}
