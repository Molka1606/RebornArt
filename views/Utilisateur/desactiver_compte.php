<?php
session_start();
require_once "../../controller/userController.php";  

if (!isset($_SESSION['user'])) {
    header("Location: signIn.html");
    exit;
}

if (!isset($_POST['id'])) {
    header("Location: infoo.php");
    exit;
}

$id = $_POST['id'];

$controller = new userController();

// Désactiver dans la base
$controller->desactiverCompte($id);

// Déconnexion automatique
session_unset();
session_destroy();

header("Location: signIn.html?msg=desactive");
exit;
?>
