<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class MailService
{
    private function mailer(): PHPMailer
    {
        $mail = new PHPMailer(true);

        if (config('mail.default') === 'smtp') {
            $mail->isSMTP();
            $mail->Host       = config('mail.mailers.smtp.host');
            $mail->SMTPAuth   = true;
            $mail->Username   = config('mail.mailers.smtp.username');
            $mail->Password   = config('mail.mailers.smtp.password');
            $mail->Port       = config('mail.mailers.smtp.port', 587);
            $mail->SMTPSecure = $mail->Port == 465 
                                ? PHPMailer::ENCRYPTION_SMTPS 
                                : PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->setFrom(
            config('mail.from.address', 'noreply@mainyuk.id'),
            config('mail.from.name', 'MainYuk')
        );
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        return $mail;
    }

    /**
     * Send booking confirmation email.
     */
    public function sendBookingConfirmation(array $user, array $booking): bool
    {
        try {
            $mail = $this->mailer();
            $mail->addAddress($user['email'], $user['name']);
            $mail->Subject = "✅ Booking Dikonfirmasi — {$booking['booking_code']}";
            $mail->Body    = $this->bookingConfirmationTemplate($user, $booking);

            // Fallback to log if SMTP not configured
            if (config('mail.default') !== 'smtp') {
                Log::info("📧 [EMAIL] To: {$user['email']} | Subject: {$mail->Subject}");
                return true;
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            Log::error("MailService::sendBookingConfirmation — " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email.
     */
    public function sendPasswordReset(string $email, string $name, string $resetUrl): bool
    {
        try {
            $mail = $this->mailer();
            $mail->addAddress($email, $name);
            $mail->Subject = '🔐 Reset Kata Sandi — MainYuk';
            $mail->Body    = $this->resetPasswordTemplate($name, $resetUrl);

            if (config('mail.default') !== 'smtp') {
                Log::info("📧 [EMAIL] To: {$email} | Reset URL: {$resetUrl}");
                return true;
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            Log::error("MailService::sendPasswordReset — " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send welcome email on register.
     */
    public function sendWelcome(string $email, string $name): bool
    {
        try {
            $mail = $this->mailer();
            $mail->addAddress($email, $name);
            $mail->Subject = '🎉 Selamat Bergabung di MainYuk!';
            $mail->Body    = $this->welcomeTemplate($name);

            if (config('mail.default') !== 'smtp') {
                Log::info("📧 [EMAIL] Welcome sent to: {$email}");
                return true;
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            Log::error("MailService::sendWelcome — " . $e->getMessage());
            return false;
        }
    }

    private function bookingConfirmationTemplate(array $user, array $booking): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <body style="font-family: 'Manrope', sans-serif; background: #f2f0eb; padding: 24px;">
          <div style="max-width: 520px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,0.08);">
            <div style="background: #1E3932; padding: 28px 32px; text-align: center;">
              <h1 style="color: #fff; margin: 0; font-size: 24px;">Main<span style="color: #cba258;">Yuk</span></h1>
            </div>
            <div style="padding: 28px 32px;">
              <h2 style="color: #1E3932; margin-top: 0;">✅ Booking Dikonfirmasi!</h2>
              <p>Halo, <strong>{$user['name']}</strong>! Booking Anda telah berhasil dikonfirmasi.</p>
              <div style="background: #f0faf5; border-radius: 12px; padding: 16px; margin: 16px 0;">
                <p style="margin: 4px 0;"><strong>ID Booking:</strong> {$booking['booking_code']}</p>
                <p style="margin: 4px 0;"><strong>Lapangan:</strong> {$booking['court_name']}</p>
                <p style="margin: 4px 0;"><strong>Tanggal:</strong> {$booking['date']}</p>
                <p style="margin: 4px 0;"><strong>Waktu:</strong> {$booking['time_start']} - {$booking['time_end']}</p>
                <p style="margin: 4px 0;"><strong>Total:</strong> Rp {$booking['total_price']}</p>
              </div>
              <p style="color: #888; font-size: 13px;">Tunjukkan kode booking ini saat check-in.</p>
            </div>
            <div style="background: #f9f9f9; padding: 16px 32px; text-align: center; color: #888; font-size: 12px;">
              © 2026 MainYuk. All rights reserved.
            </div>
          </div>
        </body>
        </html>
        HTML;
    }

    private function resetPasswordTemplate(string $name, string $resetUrl): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <body style="font-family: 'Manrope', sans-serif; background: #f2f0eb; padding: 24px;">
          <div style="max-width: 520px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,0.08);">
            <div style="background: #1E3932; padding: 28px 32px; text-align: center;">
              <h1 style="color: #fff; margin: 0; font-size: 24px;">Main<span style="color: #cba258;">Yuk</span></h1>
            </div>
            <div style="padding: 28px 32px;">
              <h2 style="color: #1E3932;">🔐 Reset Kata Sandi</h2>
              <p>Halo, <strong>{$name}</strong>! Klik tombol di bawah untuk mereset kata sandi Anda.</p>
              <div style="text-align: center; margin: 24px 0;">
                <a href="{$resetUrl}" style="display: inline-block; background: #006241; color: #fff; padding: 14px 32px; border-radius: 50px; font-weight: bold; text-decoration: none;">Reset Kata Sandi</a>
              </div>
              <p style="color: #888; font-size: 13px;">Link ini berlaku selama 5 menit. Abaikan email ini jika Anda tidak meminta reset password.</p>
            </div>
          </div>
        </body>
        </html>
        HTML;
    }

    private function welcomeTemplate(string $name): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <body style="font-family: 'Manrope', sans-serif; background: #f2f0eb; padding: 24px;">
          <div style="max-width: 520px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,0.08);">
            <div style="background: #1E3932; padding: 28px 32px; text-align: center;">
              <h1 style="color: #fff; margin: 0; font-size: 24px;">Main<span style="color: #cba258;">Yuk</span></h1>
            </div>
            <div style="padding: 28px 32px; text-align: center;">
              <h2 style="color: #1E3932;">🎉 Selamat Bergabung!</h2>
              <p>Halo, <strong>{$name}</strong>! Akun MainYuk Anda telah berhasil dibuat.</p>
              <p>Mulai temukan lapangan olahraga terbaik dan booking sekarang!</p>
              <div style="margin: 24px 0;">
                <a href="' . route('dashboard') . '" style="display: inline-block; background: #006241; color: #fff; padding: 14px 32px; border-radius: 50px; font-weight: bold; text-decoration: none;">Cari Lapangan</a>
              </div>
              <p style="color: #888; font-size: 13px;">Gunakan kode <strong>MAINYUK20</strong> untuk diskon 20% booking pertama!</p>
            </div>
          </div>
        </body>
        </html>
        HTML;
    }
}
