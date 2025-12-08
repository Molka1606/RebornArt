<?php
session_start();
require_once __DIR__ . '/../../controller/adminController.php';

$userC = new adminController();

// Bloquer si un autre rôle est connecté
if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
    echo "<script>
        alert('Un utilisateur est déjà connecté sur ce navigateur. Déconnectez-le d’abord.');
        window.location.href='../../Utilisateur/indexx.php';
    </script>";
    exit;
}

$loginError = '';
$emailError = '';
$passwordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $motdepasse = trim($_POST['motdepasse'] ?? '');

    if (empty($email)) {
        $emailError = "Veuillez entrer votre adresse e-mail.";
    }

    if (empty($motdepasse)) {
        $passwordError = "Veuillez entrer votre mot de passe.";
    }

    if (!$emailError && !$passwordError) {
        $user = $userC->login($email, $motdepasse);

        // 🔍 Debug (à utiliser si ça bloque encore)
        // var_dump($user); die();

        if ($user === "inactive") {
            $passwordError = "Votre compte admin est désactivé.";
        } elseif (is_array($user) && $user['role'] === 'admin') {
            // Connexion réussie
            $_SESSION['user'] = $user;
            $_SESSION['role'] = 'admin';
            header("Location: ../index.php");
            exit();
        } elseif (is_array($user)) {
            $passwordError = "Vous n'êtes pas autorisé à accéder à cette page.";
        } else {
            $passwordError = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <title>Connexion Administrateur</title>
</head>
<body class="body-profil">
    <div class="box">
        <div class="form-box">
            <form method="post" action="">
                <h1 class="htitre">Connexion Administrateur</h1>

                <input type="email" name="email" placeholder="Adresse e-mail" value="<?= htmlspecialchars($email ?? '') ?>">
                <span style="color:red; font-size:14px;"><?= $emailError ?></span>

                <input type="password" name="motdepasse" placeholder="Mot de passe">
                <span style="color:red; font-size:14px;"><?= $passwordError ?></span>

                <a href="adminForgetPassword.php">Mot de passe oublié ?</a>
                <button type="submit">Se connecter</button>
            </form>
        </div>
        <div class="side-box">
            <div class="side"></div>
        </div>
    </div>
</body>
</html>
