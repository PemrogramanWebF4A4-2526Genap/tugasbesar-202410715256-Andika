<?php
// src/helpers/EmailHelper.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load autoload Composer (pastikan vendor/ ada)
require_once __DIR__ . '/../../vendor/autoload.php';

class EmailHelper {
    private $mail;
    private $config;

    public function __construct() {
        $this->config = [
            'smtp_host'   => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
            'smtp_port'   => getenv('SMTP_PORT') ?: 587,
            'smtp_user'   => getenv('SMTP_USER'),
            'smtp_pass'   => getenv('SMTP_PASS'),
            'from_email'  => getenv('SMTP_FROM_EMAIL') ?: getenv('SMTP_USER'),
            'from_name'   => getenv('SMTP_FROM_NAME') ?: 'Digital Product Store'
        ];

        if (empty($this->config['smtp_user']) || empty($this->config['smtp_pass']) || empty($this->config['from_email'])) {
            $this->mail = null;
            error_log("EmailHelper: SMTP credentials incomplete.");
            return;
        }

        $this->initMail();
    }

    private function initMail() {
        try {
            $this->mail = new PHPMailer(true);
            $this->mail->isSMTP();
            $this->mail->Host = $this->config['smtp_host'];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $this->config['smtp_user'];
            $this->mail->Password = $this->config['smtp_pass'];
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = $this->config['smtp_port'];
            $this->mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';
        } catch (Exception $e) {
            error_log("EmailHelper init error: " . $e->getMessage());
            $this->mail = null;
        }
    }

    public function send($toEmail, $toName, $subject, $htmlBody, $textBody = '') {
        if ($this->mail === null) {
            error_log("Email not sent: Mailer not initialized");
            return false;
        }
        if (empty($toEmail)) {
            error_log("Email not sent: Recipient email empty");
            return false;
        }
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $toName);
            $this->mail->Subject = $subject;
            $this->mail->Body = $htmlBody;
            $this->mail->AltBody = $textBody ?: strip_tags($htmlBody);
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email failed to {$toEmail}: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    // ===================== TEMPLATE EMAIL =====================
    public static function getRegisterTemplate($name) {
        $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
        return <<<HTML
        <div style="font-family:Arial; max-width:600px; margin:auto; border:1px solid #e2e8f0; border-radius:12px;">
            <div style="background:#10B981; padding:20px; text-align:center; color:white;">
                <h2>Selamat Datang di DigiStore!</h2>
            </div>
            <div style="padding:20px;">
                <p>Halo <strong>{$name}</strong>,</p>
                <p>Terima kasih telah mendaftar. Akun Anda telah berhasil dibuat.</p>
                <div style="text-align:center; margin-top:30px;">
                    <a href="{$baseUrl}/index.php?url=home" style="background:#10B981; color:white; padding:12px 24px; text-decoration:none; border-radius:8px;">Belanja Sekarang</a>
                </div>
            </div>
            <div style="background:#f8fafc; padding:12px; text-align:center; font-size:12px; color:#64748b;">
                &copy; " . date('Y') . " DigiStore
            </div>
        </div>
        HTML;
    }

    public static function getOrderCreatedTemplate($orderId, $total) {
        $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
        $totalFormatted = number_format($total, 0, ',', '.');
        return <<<HTML
        <div style="font-family:Arial; max-width:600px; margin:auto; border:1px solid #e2e8f0; border-radius:12px;">
            <div style="background:#10B981; padding:20px; text-align:center; color:white;">
                <h2>Pesanan Diterima</h2>
            </div>
            <div style="padding:20px;">
                <p>Pesanan <strong>#{$orderId}</strong> telah diterima. Total: <strong>Rp {$totalFormatted}</strong></p>
                <div style="text-align:center; margin-top:30px;">
                    <a href="{$baseUrl}/index.php?url=buyer/myOrders" style="background:#10B981; color:white; padding:12px 24px; text-decoration:none; border-radius:8px;">Lihat Pesanan</a>
                </div>
            </div>
            <div style="background:#f8fafc; padding:12px; text-align:center; font-size:12px; color:#64748b;">
                &copy; " . date('Y') . " DigiStore
            </div>
        </div>
        HTML;
    }

    public static function getPaymentConfirmedTemplate($orderId) {
        $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
        return <<<HTML
        <div style="font-family:Arial; max-width:600px; margin:auto; border:1px solid #e2e8f0; border-radius:12px;">
            <div style="background:#10B981; padding:20px; text-align:center; color:white;">
                <h2>Pembayaran Dikonfirmasi</h2>
            </div>
            <div style="padding:20px;">
                <p>Pembayaran untuk pesanan <strong>#{$orderId}</strong> telah dikonfirmasi. Silakan download produk Anda.</p>
                <div style="text-align:center; margin-top:30px;">
                    <a href="{$baseUrl}/index.php?url=buyer/myOrders" style="background:#10B981; color:white; padding:12px 24px; text-decoration:none; border-radius:8px;">Download Sekarang</a>
                </div>
            </div>
            <div style="background:#f8fafc; padding:12px; text-align:center; font-size:12px; color:#64748b;">
                &copy; " . date('Y') . " DigiStore
            </div>
        </div>
        HTML;
    }
}
?>