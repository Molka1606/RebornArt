<?php
session_start();
require_once __DIR__ . '/../../controller/adminController.php';

if (!isset($_SESSION['user'])) {
    header("Location: adminLogin.php");
    exit();
}

if (!isset($_POST['id'])) {
    header("Location: profiladmin.php");
    exit();
}

$id = $_POST['id'];

$adminC = new userController();

// Désactiver l'admin
$adminC->desactiverAdmin($id);

// Déconnexion
session_unset();
session_destroy();

header("Location: adminLogin.php?msg=desactive");
exit();
?>
