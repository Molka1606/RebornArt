<?php
session_start();
require_once __DIR__ . '/../../Controller/adminController.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../../views/index.php");
    exit();
}

$adminC = new userController();
$user = $_SESSION['user'];

$id = $user['id'];
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$email = $_POST['email'];
$current = $_POST['password'];
$newpass = $_POST['newpassword'];

$photo = null;

// --- GESTION UPLOAD PHOTO ---
if (!empty($_FILES['photo']['name'])) {

    $uploadDir = __DIR__ . '/../../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid() . "_" . basename($_FILES['photo']['name']);
    $target = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
        $photo = '../../uploads/' . $filename;
    }
}

// Update dans la base
$result = $adminC->updateProfile($id, $nom, $prenom, $email, $current, $newpass, $photo);

// Mise à jour session
if ($result) {

    $_SESSION['user']['nom'] = $nom;
    $_SESSION['user']['prenom'] = $prenom;
    $_SESSION['user']['email'] = $email;

    if ($photo !== null)
        $_SESSION['user']['photo'] = $photo;

    header("Location: profiladmin.php?success=1");
    exit;
}

header("Location: profiladmin.php?error=1");
exit;
?>
