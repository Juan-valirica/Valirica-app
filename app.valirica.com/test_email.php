<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/mailer/Mailer.php';

// Prueba directa con PHPMailer mostrando el error
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug  = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Hostname = '<app.valirica.com>';

    $mail->Host       = SES_SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SES_SMTP_USER;
    $mail->Password   = SES_SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port       = 587;
    $mail->setFrom(SES_FROM_EMAIL, 'Valírica');
    $mail->addAddress('vale@valirica.com'); // ← pon tu email
    $mail->Subject = 'Test SES';
    $mail->Body    = 'Prueba de conexión Amazon SES';
    $mail->send();
    echo '✅ Enviado correctamente';
} catch (Exception $e) {
    echo '❌ Error: ' . $mail->ErrorInfo;
}
