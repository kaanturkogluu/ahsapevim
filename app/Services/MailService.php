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
        $sendSuccess = false;
        $errorMessage = null;

        try {
            // Attempt queued send first
            Mail::to($toEmail)->queue(new \App\Mail\ManualMail($subject, $body));
            $sendSuccess = true;
        } catch (\Throwable $e) {
            Log::error('MailService queue error, trying direct send: ' . $e->getMessage());
            try {
                // Direct send fallback if queue driver fails
                Mail::to($toEmail)->send(new \App\Mail\ManualMail($subject, $body));
                $sendSuccess = true;
            } catch (\Throwable $ex) {
                Log::error('MailService sendMail Exception: ' . $ex->getMessage());
                $errorMessage = $ex->getMessage();
            }
        }

        try {
            MailLog::create([
                'order_id' => $orderId,
                'to_email' => $toEmail,
                'subject' => $subject,
                'body' => $body,
                'status' => $sendSuccess ? 'success' : 'failed',
                'error_message' => $errorMessage,
                'type' => $type,
            ]);
        } catch (\Throwable $dbEx) {
            Log::error('MailLog database write error: ' . $dbEx->getMessage());
        }

        return $sendSuccess;
    }

    /**
     * Log an automated dynamic mailable send attempt
     */
    public function logMailable($toEmail, $subject, $body, $status = 'success', $errorMsg = null, $orderId = null)
    {
        try {
            MailLog::create([
                'order_id' => $orderId,
                'to_email' => $toEmail,
                'subject' => $subject,
                'body' => $body,
                'status' => $status,
                'error_message' => $errorMsg,
                'type' => 'automated',
            ]);
        } catch (\Throwable $dbEx) {
            Log::error('MailLog logMailable error: ' . $dbEx->getMessage());
        }
    }
}
