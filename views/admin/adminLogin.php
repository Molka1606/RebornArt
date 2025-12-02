<?php

require_once __DIR__ . '/../../controller/adminController.php';

// Instantiate the controller
$userC = new userController();

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['motdepasse'])) {
        $email = $_POST['email'];
        $motdepasse = $_POST['motdepasse'];

        if (!empty($email) && !empty($motdepasse)) {
            $user = $userC->login($email, $motdepasse);

            if ($user) {
                if ($user['role'] === 'admin') {
                    session_start();
                    $_SESSION['user'] = $user;
                    header("Location: ../../../index.php"); 
                    exit();
                } else {
                    $loginError = "Vous n'étes pas autorisé à accéder à cette page.";
                }
            } else {
                $loginError = "Email ou mot de passe incorrect.";
            }
        } else {
            $loginError = "Veuillez remplir tous les champs.";
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
            <form method="post" action="../index.php">
                <h1 class="htitre">Connexion Administrateur</h1>

                <?php if($loginError): ?>
                    <p style="color:red;"><?php echo $loginError; ?></p>
                <?php endif; ?>

                <input type="email" id="email" name="email" placeholder="Adresse e-mail" 
                       value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                <input type="password" id="motdepasse" name="motdepasse" placeholder="Mot de passe">
                <button type="submit">Se connecter</button>
            </form>
        </div>
        <div class="side-box">
            <div class="side">
            </div>
        </div>
    </div>
</body>
</html>
