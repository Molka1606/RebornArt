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

    // ✅ Sécurisation des champs
    $nom = !empty($_POST['nom']) ? $_POST['nom'] : $userSession['nom'];
    $prenom = !empty($_POST['prenom']) ? $_POST['prenom'] : $userSession['prenom'];
    $email = !empty($_POST['email']) ? $_POST['email'] : $userSession['email'];
    // ✅ Vérifier si email existe et ce n'est pas le mien
    if ($controller->emailExists($email) && $email !== $userSession['email']) {
        echo "<script>
            alert('Cet email est déjà utilisé par un autre compte !');
            window.location.href='infoo.php';
        </script>";
        exit;
    }

    $telephone = !empty($_POST['telephone']) 
        ? $_POST['telephone'] 
        : (isset($userSession['telephone']) ? $userSession['telephone'] : null);

    $date_naissance = !empty($_POST['date_naissance']) 
        ? $_POST['date_naissance'] 
        : (isset($userSession['date_naissance']) ? $userSession['date_naissance'] : null);


    // ✅ Upload photo sécurisé
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

    // ✅ Création de l'objet User AVEC telephone + date_naissance
    $updatedUser = new User(
        $userSession['id'],
        $nom,
        $prenom,
        $email,
        $userSession['motdepasse'],
        $userSession['role'],
        $photo,
        $telephone,
        $date_naissance
    );

    // ✅ Mise à jour en base
    $controller->updateUser($updatedUser);

    // ✅ Mise à jour session COMPLÈTE
    $_SESSION['user'] = array(
        'id' => $updatedUser->getId(),
        'nom' => $updatedUser->getNom(),
        'prenom' => $updatedUser->getPrenom(),
        'email' => $updatedUser->getEmail(),
        'photo' => $updatedUser->getPhoto(),
        'role' => $updatedUser->getRole(),
        'motdepasse' => $updatedUser->getMotdepasse(),
        'telephone' => $telephone,
        'date_naissance' => $date_naissance
    );

    header("Location: infoo.php");
    exit;
}
?>
