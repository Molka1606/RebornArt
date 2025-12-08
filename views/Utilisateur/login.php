<?php
session_start();
require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../model/Utilisateur.php';

$controller = new userController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $motdepasse = $_POST['motdepasse'];

    // Bloquer si un autre rôle est connecté
    if (isset($_SESSION['role']) && $_SESSION['role'] !== 'user') {
        echo "<script>
            alert('Un administrateur est déjà connecté. Déconnectez-vous d’abord.');
            window.location.href='../Utilisateur/signIn.html';
        </script>";
        exit;
    }

    // Connexion
    $user = $controller->login($email, $motdepasse);

    if ($user === "inactive") {
        echo "<script>
            alert('Votre compte est désactivé.');
            window.location.href='signIn.html';
        </script>";
        exit;
    }

    if ($user) {
        // Stocker les infos de session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email'],
            'photo' => $user['photo'],
            'role' => $user['role']
        ];
        $_SESSION['role'] = 'user'; // rôle global

        echo "<script>
            localStorage.setItem('user', JSON.stringify(".json_encode($_SESSION['user'])."));
            window.location.href='../indexx.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Email ou mot de passe incorrect');
            window.location.href='signIn.html';
        </script>";
        exit;
    }
}
?>
