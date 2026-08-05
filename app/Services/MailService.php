<?php

namespace App\Services;

use App\Models\MailLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailService
{
    /**
     * Send a raw or HTML mail and log result to mail_logs table
     *
     * @param string $toEmail
     * @param string $subject
     * @param string $body
     * @param int|null $orderId
     * @param string $type ('manual'|'automated')
     * @return bool
     */
    public function sendMail($toEmail, $subject, $body, $orderId = null, $type = 'manual')
    {
        $fromAddress = config('mail.from.address', 'raquun@raquun.net');
        $fromName = config('mail.from.name', 'AhşapEvim Manisa');

        $htmlContent = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 24px; background-color: #f9f8f6; border-radius: 16px; border: 1px solid #e5dfd5;">
            <div style="text-align: center; padding-bottom: 16px; border-bottom: 2px solid #C87A53;">
                <h2 style="color: #C87A53; margin: 0; font-size: 22px;">' . e($fromName) . '</h2>
                <span style="color: #777; font-size: 12px;">Müşteri Bilgilendirme Mesajı</span>
            </div>
            <div style="padding: 24px 12px; color: #333333; font-size: 14px; line-height: 1.6;">
                ' . nl2br(e($body)) . '
            </div>
            <div style="text-align: center; padding-top: 16px; border-top: 1px solid #e5dfd5; color: #888888; font-size: 11px;">
                ' . e($fromName) . ' &copy; ' . date('Y') . ' - Tüm Hakları Saklıdır.
            </div>
        </div>';

        try {
            Mail::html($htmlContent, function ($message) use ($toEmail, $subject, $fromAddress, $fromName) {
                $message->from($fromAddress, $fromName)
                        ->to($toEmail)
                        ->subject($subject);
            });

            MailLog::create([
                'order_id' => $orderId,
                'to_email' => $toEmail,
                'subject' => $subject,
                'body' => $body,
                'status' => 'success',
                'error_message' => null,
                'type' => $type,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('MailService sendMail Exception: ' . $e->getMessage());

            MailLog::create([
                'order_id' => $orderId,
                'to_email' => $toEmail,
                'subject' => $subject,
                'body' => $body,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'type' => $type,
            ]);

            return false;
        }
    }

    /**
     * Log an automated dynamic mailable send attempt
     */
    public function logMailable($toEmail, $subject, $body, $status = 'success', $errorMsg = null, $orderId = null)
    {
        MailLog::create([
            'order_id' => $orderId,
            'to_email' => $toEmail,
            'subject' => $subject,
            'body' => $body,
            'status' => $status,
            'error_message' => $errorMsg,
            'type' => 'automated',
        ]);
    }
}
