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

// Initialiser la photo depuis la session
$photo = isset($userSession['photo']) ? $userSession['photo'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer les valeurs postées ou garder celles de la session
    $nom = !empty($_POST['nom']) ? $_POST['nom'] : $userSession['nom'];
    $prenom = !empty($_POST['prenom']) ? $_POST['prenom'] : $userSession['prenom'];
    $email = !empty($_POST['email']) ? $_POST['email'] : $userSession['email'];

    // --- Gestion de l'upload de la photo ---
    if(isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0){
        $uploadDir = __DIR__ . '/../../uploads/';
        if(!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }

        $filename = uniqid() . '_' . basename($_FILES['profileImage']['name']);
        $targetFile = $uploadDir . $filename;

        if(move_uploaded_file($_FILES['profileImage']['tmp_name'], $targetFile)){
            $photo = 'uploads/' . $filename; // Mettre à jour le chemin
        } else {
            echo "Erreur : impossible de déplacer le fichier.";
        }
    }

    // Créer l'objet User avec la photo
    $updatedUser = new User(
        $userSession['id'],
        $nom,
        $prenom,
        $email,
        isset($userSession['motdepasse']) ? $userSession['motdepasse'] : '',
        isset($userSession['role']) ? $userSession['role'] : 'user',
        $photo
    );

    // Mettre à jour l'utilisateur dans la base
    $controller->updateUser($updatedUser);

    // Mettre à jour la session
    $_SESSION['user'] = array(
        'id' => $updatedUser->getId(),
        'nom' => $updatedUser->getNom(),
        'prenom' => $updatedUser->getPrenom(),
        'email' => $updatedUser->getEmail(),
        'photo' => $updatedUser->getPhoto(),
        'role' => isset($userSession['role']) ? $userSession['role'] : 'user'
    );

    // Redirection vers le profil
    header("Location: infoo.php");
    exit;
}
?>
