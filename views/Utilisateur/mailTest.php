<?php
require __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'molkazaza9@gmail.com';
    $mail->Password = 'kpze hwlj fzor bwqz';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('molkazaza9@gmail.com', 'Test RebornArt');
    $mail->addAddress('molkazaza9@gmail.com'); // toi-même pour tester
    $mail->Subject = 'Test mail PHPMailer';
    $mail->Body = 'Ceci est un test.';

    $mail->send();
    echo "Mail envoyé !";

} catch (Exception $e) {
    echo "Erreur : " . $mail->ErrorInfo;
}
