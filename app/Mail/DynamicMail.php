<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynamicMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $slug;
    public array $data;

    public function __construct(string $slug, array $data = [])
    {
        $this->slug = $slug;
        $this->data = $data;
    }

    public function build()
    {
        $template = EmailTemplate::where('slug', $this->slug)->where('is_active', true)->first();

        if (!$template) {
            return null;
        }

        $subject = $template->subject;
        $body = $template->content;

        // Add default site_name
        if (!isset($this->data['site_name'])) {
            $this->data['site_name'] = 'AhşapEvim';
        }

        // Replace placeholders with real values
        foreach ($this->data as $key => $value) {
            $placeholder = '{' . $key . '}';
            $subject = str_replace($placeholder, (string)$value, $subject);
            $body = str_replace($placeholder, (string)$value, $body);
        }

        // Wrap in clean, warm wood-themed master HTML email layout
        $htmlContent = $this->wrapInMasterLayout($subject, $body);

        $fromAddress = config('mail.from.address') ?: 'info@ahsapevim.com';
        $fromName = config('mail.from.name') ?: 'AhşapEvim';

        return $this->from($fromAddress, $fromName)
                    ->subject($subject)
                    ->html($htmlContent);
    }

    protected function wrapInMasterLayout(string $subject, string $body): string
    {
        $siteUrl = url('/');
        return <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>{$subject}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F7F5F0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2E251E;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F7F5F0; padding: 30px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #EFEAE0; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #FAF3EE; padding: 25px; border-bottom: 2px solid #E6DFD5;">
                            <a href="{$siteUrl}" style="text-decoration: none;">
                                <h1 style="margin: 0; color: #C87A53; font-size: 26px; font-weight: 800; letter-spacing: 0.5px;">AhşapEvim</h1>
                            </a>
                            <p style="margin: 5px 0 0 0; color: #8C6239; font-size: 13px;">Masif Ahşap El İşçiliği ve Kişiye Özel Tasarımlar</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 35px; line-height: 1.6; color: #333333; font-size: 14px;">
                            {$body}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #2E251E; color: #E6DFD5; padding: 20px; font-size: 12px; line-height: 1.6;">
                            <p style="margin: 0;"><strong>AhşapEvim Manisa Atölyesi</strong></p>
                            <p style="margin: 4px 0 0 0; opacity: 0.8;">Bizi tercih ettiğiniz için teşekkür ederiz!</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}
