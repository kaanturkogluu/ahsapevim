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
        try {
            Mail::send([], [], function ($message) use ($toEmail, $subject, $body) {
                $message->to($toEmail)
                        ->subject($subject)
                        ->html(nl2br(e($body)));
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
