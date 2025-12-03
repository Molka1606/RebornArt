<?php
session_start();
require_once __DIR__ . '/../../Controller/userController.php';
require_once __DIR__ . '/../../Model/Utilisateur.php';

if(!isset($_SESSION['user'])){
    header("Location: signIn.html");
    exit;
}

$controller = new userController();
$userSession = $_SESSION['user'];

$photo = isset($userSession['photo']) ? $userSession['photo'] : "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = isset($_POST['nom']) ? $_POST['nom'] : $userSession['nom'];
    $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : $userSession['prenom'];
    $email = isset($_POST['email']) ? $_POST['email'] : $userSession['email'];

    // Upload photo
    if(isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === 0){

        $uploadDir = __DIR__ . '/../../uploads/';

        if(!is_dir($uploadDir)){
            mkdir($uploadDir, 0755, true);
        }

        $filename = uniqid() . "_" . basename($_FILES['profileImage']['name']);
        $targetFile = $uploadDir . $filename;

        if(move_uploaded_file($_FILES['profileImage']['tmp_name'], $targetFile)){
            $photo = '../../uploads/' . $filename;
        }
    }

    // Création User corrigée
    $updatedUser = new User(
        $userSession['id'],
        $nom,
        $prenom,
        $email,
        $userSession['motdepasse'],
        $userSession['role'],
        $photo
    );

    $controller->updateUser($updatedUser);

    $_SESSION['user'] = array(
        'id' => $updatedUser->getId(),
        'nom' => $updatedUser->getNom(),
        'prenom' => $updatedUser->getPrenom(),
        'email' => $updatedUser->getEmail(),
        'photo' => $updatedUser->getPhoto(),
        'role' => $updatedUser->getRole(),
        'motdepasse' => $updatedUser->getMotdepasse()
    );

    header("Location: infoo.php");
    exit;
}
?>
