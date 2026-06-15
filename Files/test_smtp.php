<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 2; // Detailed debug
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'linbilcelestre31@gmail.com';
    $mail->Password = 'ncimrfhgjisuzzam';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom('linbilcelestre31@gmail.com', 'Test');
    $mail->addAddress('linbilcelestre31@gmail.com');
    $mail->Subject = 'Test';
    $mail->Body = 'Test';
    $mail->send();
    echo "Success";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
