<?php
require_once "../../controller/adminController.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $controller = new adminController();

    // Générer code seulement pour admin
    $code = $controller->createAdminResetCode($email);

    if (!$code) {
        $message = "<span style='color:red;'>Email introuvable ou non administrateur.</span>";
    } else {
        $resetLink = "http://localhost/RebornArt/views/admin/adminResetPassword.php?code=" . $code;

        // Pour XAMPP : pas d'email → afficher le lien
        $message = "Voici votre lien de réinitialisation :<br><br>
        <a href='$resetLink'>$resetLink</a>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe Admin oublié</title>

    <style>
        <?php echo file_get_contents("../assets/css/admin.css"); ?>

        .reset-msg {
            background: #eef6f0;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 10px 0 20px;
            font-size: 13px;
            color: #345f41;
            word-break: break-all;
            max-width: 260px;
        }
    </style>
</head>
<body class="body-profil">

<div class="box">
    <div class="form-box">
        <form method="POST">

            <?php if (!empty($message)) : ?>
                <div class="reset-msg"><?php echo $message; ?></div>
            <?php endif; ?>

            <input type="email" name="email" placeholder="Votre email admin">

            <button type="submit">Envoyer le lien</button>

            <a href="adminLogin.php">← Retour</a>
        </form>
    </div>

    <div class="side-box">
        <div class="side">
        </div>
    </div>
</div>

</body>
</html>
