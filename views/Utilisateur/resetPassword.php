<?php
require_once "../../controller/userController.php";

$controller = new userController();

if (!isset($_GET['code']) || empty($_GET['code'])) {
    die("Lien invalide !");
}

$code = $_GET['code'];
$user = $controller->checkResetCode($code);

if (!$user) {
    die("Lien expiré ou invalide.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $mdp = $_POST["motdepasse"];
    $cmdp = $_POST["cmotdepasse"];

    if (empty($mdp) || empty($cmdp)) {
        $message = "<span style='color:red;'>Veuillez remplir tous les champs.</span>";
    } elseif ($mdp !== $cmdp) {
        $message = "<span style='color:red;'>Les mots de passe ne correspondent pas.</span>";
    } else {

        $ok = $controller->updatePasswordFromCode($code, $mdp);

        if ($ok) {
            echo "<script>alert('Mot de passe modifié avec succès'); window.location='signIn.html';</script>";
            exit();
        } else {
            $message = "<span style='color:red;'>Erreur lors de la mise à jour.</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau mot de passe</title>

<style>
    <?php echo file_get_contents("../assets/css/signup.css"); ?>
</style>


</head>
<body>

<div class="box">
    <div class="form-box">
        <form method="POST">

    <?php if (!empty($message)) : ?>
        <div class="reset-msg"><?php echo $message; ?></div>
    <?php endif; ?>



            <input type="password" name="motdepasse" placeholder="Nouveau mot de passe" >
            <input type="password" name="cmotdepasse" placeholder="Confirmer le mot de passe" >

            <button type="submit">Changer le mot de passe</button>

            <a href="signIn.html">← Retour</a>
        </form>
    </div>

    <div class="side-box">
        <div class="side">
            <p>Choisissez un nouveau mot de passe sécurisé.</p>
            <a href="signIn.html">
                <button>Connexion</button>
            </a>
        </div>
    </div>
</div>

</body>
</html>
