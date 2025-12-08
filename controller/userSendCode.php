<?php

session_start();
require __DIR__ . '/../vendor/autoload.php';  // <-- corrigé
require_once __DIR__ . "/../model/config.php";
require_once __DIR__ . "/userController.php";  // si userController.php est déjà dans controller

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Vérifier si l'email est fourni
if (!isset($_POST['email']) || empty($_POST['email'])) {
    echo "no_email";
    exit();
}

$email = trim($_POST['email']);

// Vérifier si l'email existe dans la base
$userCtrl = new userController();
if (!$userCtrl->emailExists($email)) {
    echo "email_not_found";
    exit();
}

// Générer un code à 6 chiffres
$code = rand(100000, 999999);

// Sauvegarder le code et l'email en session
$_SESSION['code_user'] = $code;
$_SESSION['email_verif'] = $email;

// Création du mail
$mail = new PHPMailer(true);

try {
    // Config SMTP Gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'molkazaza9@gmail.com'; // ton email
    $mail->Password = 'milj syuv ssff tuba';   // mot de passe d'application Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('molkazaza9@gmail.com', 'RebornArt Support');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Code de verification RebornArt';
    $mail->Body = "
        <h3>Bonjour,</h3>
        <p>Voici votre code de verification pour réinitialiser votre mot de passe :</p>
        <h2 style='color: #2E86C1;'>$code</h2>
        <p>Si vous n'avez pas demandé ce code, ignorez ce mail.</p>
    ";

    $mail->send();

    echo "success";

} catch (Exception $e) {
    echo "error: {$mail->ErrorInfo}";
}
?>

