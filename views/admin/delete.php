<?php
session_start();
require_once __DIR__ . '/../../Controller/adminController.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../admin/adminLogin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userC = new userController();
    $userC->deleteUser($_POST['id']); 
    session_unset();
    session_destroy();
    header("Location: ../../../index.php"); 
    exit();
}
?>
