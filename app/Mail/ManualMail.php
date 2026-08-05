<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManualMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $customSubject;
    public string $bodyText;

    public function __construct(string $subject, string $bodyText)
    {
        $this->customSubject = $subject;
        $this->bodyText = $bodyText;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address') ?: 'raquun@raquun.net';
        $fromName = config('mail.from.name') ?: config('app.name');

        if (empty($fromName) || in_array($fromName, ['Laravel', '{APP_NAME}', 'null'])) {
            $fromName = 'AhşapEvim';
        }

        $bodyFormatted = nl2br(e($this->bodyText));
        $siteUrl = url('/');

        $htmlContent = <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>{$this->customSubject}</title>
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
                                <h1 style="margin: 0; color: #C87A53; font-size: 26px; font-weight: 800; letter-spacing: 0.5px;">{$fromName}</h1>
                            </a>
                            <p style="margin: 5px 0 0 0; color: #8C6239; font-size: 13px;">Müşteri Bilgilendirme Mesajı</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 35px; line-height: 1.7; color: #333333; font-size: 14px;">
                            {$bodyFormatted}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #2E251E; color: #E6DFD5; padding: 20px; font-size: 12px; line-height: 1.6;">
                            <p style="margin: 0;"><strong>{$fromName} Atölyesi</strong></p>
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

        return $this->from($fromAddress, $fromName)
                    ->subject($this->customSubject)
                    ->html($htmlContent);
    }
}
