<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Mail\DynamicMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::latest()->get();
        return view('admin.email_templates.index', compact('templates'));
    }

    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return view('admin.email_templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $template->update([
            'subject' => $request->subject,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.email_templates.index')->with('success', "'{$template->name}' şablonu başarıyla güncellendi.");
    }

    public function preview($id)
    {
        $template = EmailTemplate::findOrFail($id);

        $dummyData = [
            'user_name' => 'Ahmet Yılmaz',
            'user_email' => 'ahmet@example.com',
            'order_id' => '1042',
            'tracking_code' => 'AHS-849201',
            'total_amount' => '1,250.00',
            'delivery_address' => 'Atatürk Mah. Cumhuriyet Cad. No:15 Manisa/Merkez',
            'site_name' => 'AhşapEvim',
        ];

        $mail = new DynamicMail($template->slug, $dummyData);

        return $mail->render();
    }

    public function sendTest(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);
        $targetEmail = $request->input('email', auth()->user()->email);

        $dummyData = [
            'user_name' => auth()->user()->name ?: 'Test Kullanıcı',
            'user_email' => $targetEmail,
            'order_id' => '1042',
            'tracking_code' => 'AHS-849201',
            'total_amount' => '1,250.00',
            'delivery_address' => 'Atatürk Mah. Cumhuriyet Cad. No:15 Manisa/Merkez',
            'site_name' => 'AhşapEvim',
        ];

        try {
            Mail::to($targetEmail)->send(new DynamicMail($template->slug, $dummyData));
            return redirect()->back()->with('success', "'{$template->name}' için test e-postası {$targetEmail} adresine başarıyla gönderildi.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Test e-postası gönderilirken hata oluştu: ' . $e->getMessage());
        }
    }
}
