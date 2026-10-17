<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Use relative paths and include them once
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

class Mailer
{
    /**
     * Create a configured PHPMailer instance, ready to send.
     */
    private static function createMailer(): ?PHPMailer
    {
        $config = require BASE_PATH . '/config/mail.php';
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->SMTPAuth   = $config['auth'];
            $mail->Username   = $config['username'];
            $mail->Password   = str_replace(' ', '', $config['password']);
            $mail->CharSet    = 'UTF-8';

            $secure = strtolower($config['secure'] ?? 'tls');
            if ($secure === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($secure === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = false;
            }

            $mail->Port = $config['port'];

            // Disable SSL peer verification for local XAMPP/OpenSSL environment compatibility
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            // Default sender & reply-to
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addReplyTo($config['from_email'], $config['from_name']);

            return $mail;
        } catch (\Throwable $e) {
            error_log("Mailer Init Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send OTP Verification Email
     */
    public static function sendOTP($toEmail, $otp): bool
    {
        $toEmail = trim($toEmail);
        $otp = trim($otp);

        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            error_log("Mailer Error: Invalid recipient email address '$toEmail'");
            return false;
        }

        $mail = self::createMailer();
        if (!$mail) return false;

        try {
            $mail->addAddress($toEmail);
            $mail->isHTML(true);
            $mail->Subject = 'Your ISCAG Verification Code: ' . $otp;
            
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px 0; max-width: 580px; margin: 0 auto; background: #ffffff;'>
                    <h3 style='color: #1c6b3a; margin-top: 0; margin-bottom: 18px; font-size: 20px; font-weight: bold;'>ISCAG Pilipinas</h3>
                    <p style='color: #222222; font-size: 14px; margin-bottom: 12px;'>Assalamu Alaikum,</p>
                    <p style='color: #222222; font-size: 14px; margin-bottom: 20px;'>Ang iyong verification code para sa paggawa ng account ay:</p>
                    <div style='background: #f5f5f5; padding: 14px; text-align: center; letter-spacing: 6px; color: #1c6b3a; font-size: 28px; font-weight: bold; border-radius: 4px; margin-bottom: 20px;'>
                        $otp
                    </div>
                    <p style='color: #222222; font-size: 13px; margin-bottom: 10px; line-height: 1.5;'>Mag-e-expire ang code na ito sa loob ng 10 minuto.</p>
                    <p style='color: #222222; font-size: 13px; margin: 0; line-height: 1.5;'>Kung hindi mo ito hiniling, mangyaring balewalain ang email na ito.</p>
                </div>
            ";

            $mail->AltBody = "Assalamu Alaikum,\n\nAng iyong verification code para sa paggawa ng account ay:\n$otp\n\nMag-e-expire ang code na ito sa loob ng 10 minuto.\nKung hindi mo ito hiniling, mangyaring balewalain ang email na ito.";

            $mail->send();
            return true;
        } catch (\Throwable $e) {
            error_log("Mailer Error (OTP): " . $e->getMessage() . ($mail->ErrorInfo ? " | PHPMailer Info: " . $mail->ErrorInfo : ""));
            return false;
        }
    }

    /**
     * Send billing reminder email with itemized breakdown.
     *
     * @param array $data [
     *   'to_email'       => string (required),
     *   'to_name'        => string,
     *   'due_date'       => string,
     *   'days_until_due' => int,
     *   'month_name'     => string,
     *   'items'          => array of ['name' => ..., 'amount' => ...],
     *   'total_due'      => float,
     *   'unit'           => string,
     *   'is_overdue'     => bool,
     * ]
     * @return bool
     */
    public static function sendBillingReminder(array $data): bool
    {
        $toEmail = trim($data['to_email'] ?? '');
        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            error_log("Mailer Error: Invalid recipient email for billing reminder '$toEmail'");
            return false;
        }

        $mail = self::createMailer();
        if (!$mail) return false;

        $toName     = trim($data['to_name'] ?? 'Valued Tenant');
        $dueDate    = htmlspecialchars($data['due_date'] ?? 'N/A');
        $daysLeft   = (int)($data['days_until_due'] ?? 0);
        $monthName  = htmlspecialchars($data['month_name'] ?? date('F Y'));
        $items      = $data['items'] ?? [];
        $totalDue   = (float)($data['total_due'] ?? 0);
        $unit       = htmlspecialchars($data['unit'] ?? 'ISCAG Apartment');
        $isOverdue  = (bool)($data['is_overdue'] ?? false);

        $refNo      = !empty($data['reference_number']) ? htmlspecialchars($data['reference_number']) : '';
        $subjectRef = $refNo ? " [Ref: {$refNo}]" : "";

        $formattedTotal = number_format($totalDue, 2);
        $themeColor  = $isOverdue ? '#dc2626' : '#0f5c3a';
        $themeBadge  = $isOverdue ? 'OVERDUE BILLING NOTICE' : 'UPCOMING BILLING REMINDER';

        // Due date note
        $dueNote = $isOverdue
            ? "Your payment is <strong style='color:#dc2626;'>overdue</strong>. Please settle immediately to avoid penalties."
            : "Due on <strong>{$dueDate}</strong> ({$daysLeft} day(s) remaining).";

        // Build itemized table rows
        $itemsHtml = '';
        $itemsText = '';
        foreach ($items as $item) {
            $iName = htmlspecialchars($item['name'] ?? 'Charge Item');
            $iAmt  = number_format((float)($item['amount'] ?? 0), 2);
            $itemsHtml .= "
                <tr>
                    <td style='padding: 10px 12px; border-bottom: 1px dashed #e2e8f0; font-size: 13px; color: #334155;'>{$iName}</td>
                    <td style='padding: 10px 12px; border-bottom: 1px dashed #e2e8f0; font-size: 13px; font-weight: 600; color: {$themeColor}; text-align: right;'>₱{$iAmt}</td>
                </tr>
            ";
            $itemsText .= "- {$iName}: PHP {$iAmt}\n";
        }

        $portalUrl = function_exists('full_url') ? full_url('/user/apartment/payment') : (defined('BASE_URL') ? BASE_URL . '/user/apartment/payment' : '');

        try {
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = "ISCAG Billing Reminder: Payment for {$monthName} Due on {$dueDate}{$subjectRef}";

            $refDisplayHtml = $refNo ? "<div style='font-size: 12px; color: #64748b; margin-bottom: 4px;'>Reference: <strong style='font-family: monospace; color: #334155; font-size: 13px;'>{$refNo}</strong></div>" : "";

            $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head><meta charset='UTF-8'></head>
            <body style='margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background: #f1f5f9;'>
                <table width='100%' cellspacing='0' cellpadding='0' style='background: #f1f5f9; padding: 30px 10px;'>
                    <tr>
                        <td align='center'>
                            <table width='100%' style='max-width: 600px; background: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 18px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;' cellspacing='0' cellpadding='0'>
                                <!-- Header Banner -->
                                <tr>
                                    <td style='background: linear-gradient(135deg, {$themeColor} 0%, #155736 100%); padding: 32px 30px; text-align: center; color: #ffffff;'>
                                        <div style='font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #c9a84c; font-weight: 800; margin-bottom: 6px;'>Islamic Studies, Call and Guidance of the Philippines</div>
                                        <h1 style='margin: 0; font-size: 22px; font-weight: 700; letter-spacing: -0.5px;'>ISCAG PILIPINAS</h1>
                                        <div style='display: inline-block; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); border-radius: 20px; padding: 4px 14px; margin-top: 14px; font-size: 12px; font-weight: 600; letter-spacing: 0.5px;'>
                                            {$themeBadge}
                                        </div>
                                    </td>
                                </tr>

                                <!-- Body -->
                                <tr>
                                    <td style='padding: 32px 32px 24px;'>
                                        <p style='margin: 0 0 16px; font-size: 15px; color: #334155;'>
                                            Assalamu Alaikum <strong>{$toName}</strong>,
                                        </p>
                                        <p style='margin: 0 0 24px; font-size: 14px; color: #475569; line-height: 1.6;'>
                                            This is a billing notice regarding your apartment statement for <strong>{$monthName}</strong> ({$unit}).<br>
                                            {$dueNote}
                                        </p>

                                        <!-- Dues Box -->
                                        <div style='background: " . ($isOverdue ? '#fef2f2' : '#f8fafc') . "; border: 1.5px solid " . ($isOverdue ? '#fecaca' : '#e2e8f0') . "; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 24px;'>
                                            {$refDisplayHtml}
                                            <div style='font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 600;'>Total Outstanding Dues</div>
                                            <div style='font-size: 32px; font-weight: 800; color: {$themeColor}; margin: 4px 0 6px;'>₱{$formattedTotal}</div>
                                            <div style='font-size: 13px; color: #64748b;'>Due Date: <strong>{$dueDate}</strong></div>
                                        </div>

                                        <!-- Itemized Breakdown -->
                                        <div style='font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin-bottom: 10px;'>
                                            Statement Breakdown ({$monthName})
                                        </div>
                                        <table width='100%' style='border-collapse: collapse; margin-bottom: 24px;' cellspacing='0' cellpadding='0'>
                                            <thead>
                                                <tr style='background: #f8fafc;'>
                                                    <th style='padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 1px solid #e2e8f0;'>Charge Description</th>
                                                    <th style='padding: 10px 12px; text-align: right; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; border-bottom: 1px solid #e2e8f0;'>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {$itemsHtml}
                                                <tr style='background: #fcfdfd;'>
                                                    <td style='padding: 12px; font-size: 14px; font-weight: 700; color: {$themeColor};'>Total Due</td>
                                                    <td style='padding: 12px; font-size: 15px; font-weight: 800; color: {$themeColor}; text-align: right;'>₱{$formattedTotal}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <!-- Payment Options Info -->
                                        <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 28px; font-size: 13px; color: #475569; line-height: 1.5;'>
                                            <strong>Payment Channels:</strong> You can settle online immediately via GCash, Maya, or Card through PayMongo, or upload a manual bank transfer reference inside your Payment Center.
                                        </div>

                                        <!-- CTA Button -->
                                        <div style='text-align: center; margin-bottom: 16px;'>
                                            <a href='{$portalUrl}' style='display: inline-block; background: {$themeColor}; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 14px 32px; border-radius: 8px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);'>
                                                Pay Dues Online Now
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Footer -->
                                <tr>
                                    <td style='background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 30px; text-align: center; font-size: 12px; color: #94a3b8; line-height: 1.6;'>
                                        <strong style='color: #64748b;'>ISCAG Pilipinas Management & Billing Department</strong><br>
                                        9793 Vitalez Compound, Baltao St., Brgy. Vitalez, Parañaque City, Metro Manila<br>
                                        If you have already settled this bill recently, please disregard this reminder or check your portal.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
            ";

            $mail->AltBody = "Assalamu Alaikum {$toName},\n\n" .
                "{$themeBadge} - ISCAG PILIPINAS\n" .
                "================================================\n" .
                ($refNo ? "Statement Ref:        {$refNo}\n" : "") .
                "Apartment Statement: {$monthName} ({$unit})\n" .
                "Total Amount Due:    PHP {$formattedTotal}\n" .
                "Due Date:            {$dueDate}\n\n" .
                "BREAKDOWN OF DUES:\n" .
                $itemsText .
                "Total Balance: PHP {$formattedTotal}\n\n" .
                "Please pay online via PayMongo (GCash / Maya / Card) or bank reference here:\n" .
                "{$portalUrl}\n\n" .
                "Thank you,\n" .
                "ISCAG Pilipinas Management";

            $mail->send();
            return true;
        } catch (\Throwable $e) {
            error_log("Mailer Error (Billing Reminder): " . $e->getMessage() . ($mail->ErrorInfo ? " | PHPMailer Info: " . $mail->ErrorInfo : ""));
            return false;
        }
    }
}
