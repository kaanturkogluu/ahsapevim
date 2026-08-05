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
        $request->validate([
            'to_phone' => 'required|string|max:25',
            'message' => 'required|string|max:1000',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $netgsm = app(NetgsmService::class);
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
