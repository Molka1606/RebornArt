<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_POST['email'])) {
    echo "no_email";
    exit();
}

$email = $_POST['email'];

// Générer un code à 6 chiffres
$code = rand(100000, 999999);

// Sauvegarder le code en session
$_SESSION['code_verif'] = $code;
$_SESSION['email_verif'] = $email;

// Envoyer l’email
$mail = new PHPMailer(true);

try {
    // Config SMTP Gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'molkazouaoui200@gmail.com'; 
    $mail->Password = 'oalv gjge nofr aobj'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Expéditeur
    $mail->setFrom('molkazouaoui200@gmail.com', 'RebornArt Support');

    // Destinataire
    $mail->addAddress($email);

    // Contenu
    $mail->isHTML(true);
    $mail->Subject = 'Code de vérification';
    $mail->Body = "<h3>Votre code de vérification est :</h3>
                   <h2><b>$code</b></h2>";

    $mail->send();
    echo "success";

} catch (Exception $e) {
    echo "error: {$mail->ErrorInfo}";
}
