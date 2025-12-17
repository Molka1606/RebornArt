<?php
session_start(); 

require_once __DIR__ . '/../../controller/userController.php';

$userC = new userController();

if(isset($_GET['id'])) {
    $userC->deleteUser($_GET['id']);
    session_unset();
    session_destroy();
}

header('Location: signIn.html');
exit;
?>