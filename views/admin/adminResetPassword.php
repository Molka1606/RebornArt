<?php
require_once "../../controller/adminController.php";

$controller = new adminController();

if (!isset($_GET['code']) || empty($_GET['code'])) {
    die("Lien invalide !");
}

$code = $_GET['code'];

$user = $controller->checkAdminResetCode($code);

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

        $ok = $controller->updateAdminPasswordFromCode($code, $mdp);

        if ($ok) {
            echo "<script>alert('Mot de passe modifié avec succès'); window.location='adminLogin.php';</script>";
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
    <title>Nouveau mot de passe Admin</title>

    <style>
        <?php echo file_get_contents("../assets/css/admin.css"); ?>

        .reset-msg {
            background: #eef6f0;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 10px 0 20px;
            font-size: 13px;
            color: #345f41;
        }
    </style>
</head>
<body class="body-profil">

<div class="box">
    <div class="form-box">
        <form method="POST">
            <h2 class="htitre">Nouveau mot de passe Admin</h2>

            <?php if (!empty($message)) : ?>
                <div class="reset-msg"><?php echo $message; ?></div>
            <?php endif; ?>

            <input type="password" name="motdepasse" placeholder="Nouveau mot de passe">
            <input type="password" name="cmotdepasse" placeholder="Confirmer le mot de passe">

            <button type="submit">Modifier</button>

            <a href="adminLogin.php">← Retour</a>
        </form>
    </div>

    <div class="side-box">
        <div class="side"></div>
    </div>
</div>

</body>
</html>
