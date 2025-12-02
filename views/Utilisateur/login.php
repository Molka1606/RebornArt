<?php
session_start();

require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../model/Utilisateur.php';

$controller = new userController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $motdepasse = $_POST['motdepasse'];

    // Vérifier si déjà connecté
    if (isset($_SESSION['user'])) {
        $emailSession = $_SESSION['user']['email'];

        if ($email === $emailSession) {
            // Même compte → message
            echo "<script>
                alert('Vous êtes déjà connecté avec ce compte.');
                window.location.href = '../indexx.php';
            </script>";
            exit;
        } else {
            // Autre compte → message et empêcher la connexion
            echo "<script>
                alert('Vous êtes déjà connecté avec un autre compte. Déconnectez-vous d\'abord.');
                window.location.href = 'signIn.html';
            </script>";
            exit;
        }
    }

    // Si pas de session active, on tente la connexion
    $user = $controller->login($email, $motdepasse);

    if ($user) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email']
        ];

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
