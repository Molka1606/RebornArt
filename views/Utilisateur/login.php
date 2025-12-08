<?php
session_start();
require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../model/Utilisateur.php';

$controller = new userController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Vérifier si quelqu’un est déjà connecté
    if (isset($_SESSION['role'])) {

        // Si un ADMIN est connecté → blocage
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] !== 'user') {
            echo "<script>
                alert('Un administrateur est déjà connecté. Déconnectez-vous d’abord.');
            </script>";
        }


        // Si un autre USER connecté → blocage
        if ($_SESSION['role'] === 'user') {
            echo "<script>
                alert('Un utilisateur est déjà connecté dans ce navigateur.');
            </script>";
        }
    }

    // ---------------------------
    // 2. TRAITEMENT CONNEXION
    // ---------------------------
    $email = $_POST['email'];
    $motdepasse = $_POST['motdepasse'];

    $user = $controller->login($email, $motdepasse);

    if ($user === "inactive") {
        echo "<script>
            alert('Votre compte est désactivé.');
            window.location.href='signIn.html';
        </script>";
        exit;
    }

    if ($user) {
        // Enregistrer session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email'],
            'photo' => $user['photo'],
            'role' => $user['role']
        ];
        $_SESSION['role'] = 'user';

        echo "<script>
            localStorage.setItem('user', JSON.stringify(".json_encode($_SESSION['user'])."));
            window.location.href='../indexx.php';
        </script>";
        exit;
    }

    // Mauvais login
    echo "<script>
        alert('Email ou mot de passe incorrect.');
        window.location.href='signIn.html';
    </script>";
    exit;
}
?>
