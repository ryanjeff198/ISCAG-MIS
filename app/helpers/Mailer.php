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
    public static function sendOTP($toEmail, $otp)
    {
        $toEmail = trim($toEmail);
        $otp = trim($otp);

        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            error_log("Mailer Error: Invalid recipient email address '$toEmail'");
            return false;
        }

        // Load mail configuration
        $config = require BASE_PATH . '/config/mail.php';
        $mail = new PHPMailer(true);

        try {
            // Server settings
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

            // Sender & Recipients
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($toEmail);
            $mail->addReplyTo($config['from_email'], $config['from_name']);

            // Content
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
            error_log("Mailer Error: " . $e->getMessage() . ($mail->ErrorInfo ? " | PHPMailer Info: " . $mail->ErrorInfo : ""));
            return false;
        }
    }
}
