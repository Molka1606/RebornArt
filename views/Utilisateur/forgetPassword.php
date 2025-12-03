<?php
require_once "../../controller/userController.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $controller = new userController();

    $code = $controller->createResetCode($email);

    if (!$code) {
        $message = "<span style='color:red;'>Email introuvable.</span>";
    } else {

        $resetLink = "http://localhost/RebornArt/views/Utilisateur/resetPassword.php?code=" . $code;

        $message = "Voici votre lien de réinitialisation :<br><br>
        <a href='$resetLink'>$resetLink</a>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>

<style>
    <?php echo file_get_contents("../assets/css/signup.css"); ?>

    /* Limite la largeur du formulaire */
    .form-box form {
        width: 80%;
        max-width: 320px;
    }

    /* Champ email taille normale */
    .form-box input[type="email"] {
        width: 100%;
        max-width: 280px;
    }

/* Boîte du message */
.reset-msg {
    background: #eef6f0;
    padding: 10px 15px;
    border-radius: 8px;
    margin: 10px 0 20px;
    word-break: break-all;       /* force le texte long à passer à la ligne */
    font-size: 13px;
    color: #345f41;
    max-width: 260px;            /* limite la largeur */
    text-align: left;
}

/* Champ email taille normale */
.form-box input[type="email"] {
    width: 260px !important;
    max-width: 260px;
}

    .reset-msg a {
        color: #2a8c55;
        font-weight: 600;
        word-break: break-all;
    }
</style>

</head>
<body>

<div class="box">
    <div class="form-box">
        <form method="POST">

            <?php if (!empty($message)) : ?>
                <div class="reset-msg"><?php echo $message; ?></div>
            <?php endif; ?>

            <input type="email" name="email" placeholder="Votre email"  >

            <button type="submit">Envoyer le lien</button>

            <a href="signIn.html">← Retour</a>
        </form>
    </div>

    <div class="side-box">
        <div class="side">
            <p>Entrez votre email pour recevoir un lien sécurisé.</p>
            <a href="signIn.html">
                <button>Se connecter</button>
            </a>
        </div>
    </div>
</div>

</body>
</html>
