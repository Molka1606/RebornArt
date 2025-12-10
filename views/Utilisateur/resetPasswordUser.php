<?php
session_start();
require_once __DIR__ . '/../../controller/userController.php';

// Vérifier que l'utilisateur a passé la vérification du code
if (!isset($_SESSION['email_verif'])) {
    header("Location: userForgetPassword.php");
    exit();
}

$passwordError = '';
$confirmError = '';
$successMsg = '';

// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mdp = $_POST['mdp'] ?? '';
    $mdpConfirm = $_POST['mdpConfirm'] ?? '';

    // Vérification
    if ($mdp === '') {
        $passwordError = "Veuillez saisir un mot de passe.";
    } elseif ($mdpConfirm === '') {
        $confirmError = "Veuillez confirmer le mot de passe.";
    } elseif ($mdp !== $mdpConfirm) {
        $confirmError = "Les mots de passe ne correspondent pas.";
    } else {
        // Tout est correct → mettre à jour le mot de passe
        $adminC = new userController();
        $email = $_SESSION['email_verif'];

        $adminC->updatePassword($email, $mdp); // Crée cette fonction dans adminController

        // Nettoyer la session
        unset($_SESSION['code_verif']);
        unset($_SESSION['email_verif']);

        $successMsg = "Mot de passe mis à jour avec succès !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="body-profil">
    <div class="box">
        <div class="form-box">


            <?php if ($successMsg): ?>
                <p style="color:green;"><?php echo $successMsg; ?></p>
                <a href="signIn.html">Se connecter</a>
            <?php else: ?>
                <form method="post" action="">
                    <input type="password" name="mdp" placeholder="Nouveau mot de passe">
                    <span style="color:red;"><?php echo $passwordError; ?></span>

                    <input type="password" name="mdpConfirm" placeholder="Confirmer mot de passe">
                    <span style="color:red;"><?php echo $confirmError; ?></span>

                    <button type="submit">Réinitialiser</button>
                </form>
            <?php endif; ?>

        </div>
                <div class="side-box">
            <div class="side"></div>
        </div>
    </div>
</body>
</html>
