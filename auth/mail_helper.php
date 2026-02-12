<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

function sendOTP($recipientEmail, $otp, $type = 'Verification')
{
    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'linbilcelestre3@gmail.com';
        $mail->Password = 'wovw wjac wzlf pzev';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('linbilcelestre3@gmail.com', 'SMS Official');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "$type Code - SMS Official";

        $mail->Body = "
        <div style='font-family: Poppins, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
            <h2 style='color: #2563eb; text-align: center;'>$type Code</h2>
            <p style='text-align: center; font-size: 1.1rem;'>Your verification code is:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <span style='background-color: #f1f5f9; color: #1e293b; padding: 15px 30px; border-radius: 8px; font-weight: 700; font-size: 2rem; letter-spacing: 5px; border: 2px dashed #cbd5e1;'>
                    $otp
                </span>
            </div>
            <p style='font-size: 0.9rem; color: #64748b; text-align: center;'>This code will expire in 10 minutes. If you did not request this, please ignore this email.</p>
            <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
            <p style='font-size: 0.8rem; color: #94a3b8; text-align: center;'>&copy; " . date('Y') . " SMS Official. All rights reserved.</p>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        return false;
    }
}
