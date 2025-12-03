 <?php

require_once __DIR__ . '/../../controller/adminController.php';

// Instantiate the controller
$userC = new adminController();


$loginError = '';
$emailError = '';
$passwordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $motdepasse = $_POST['motdepasse'] ?? '';

    // Vérifier champs vides
    if (empty($email)) {
        $emailError = "Veuillez entrer votre adresse e-mail.";
    }

    if (empty($motdepasse)) {
        $passwordError = "Veuillez entrer votre mot de passe.";
    }

    // Si aucune erreur → procéder au login
    if (!$emailError && !$passwordError) {

        $user = $userC->login($email, $motdepasse);
        if ($user === "inactive") {
            $passwordError = "Votre compte admin est désactivé.";
        }

        if ($user) {
            if ($user['role'] === 'admin') {
                session_start();
                $_SESSION['user'] = $user;
                header("Location: ../index.php");
                exit();
            } else {
                $passwordError = "Vous n'êtes pas autorisé à accéder à cette page.";
            }
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
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/user.js"></script>
    <title>Connexion Administrateur</title>
</head>
<body class="body-profil">
    <div class="box">
        <div class="form-box">

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <h1 class="htitre">Connexion Administrateur</h1>

                <!-- Email -->
                <input type="email" id="email" name="email" placeholder="Adresse e-mail"
                       value="<?php echo htmlspecialchars($email ?? ''); ?>">

                <span style="color:red; font-size:14px;">
                    <?php echo $emailError; ?>
                </span>

                <!-- Mot de passe -->
                <input type="password" id="motdepasse" name="motdepasse" placeholder="Mot de passe">

                <span style="color:red; font-size:14px;">
                    <?php echo $passwordError; ?>
                </span>
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