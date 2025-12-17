<?php
session_start();
require_once __DIR__ . "/../../model/config.php";

if(isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['motdepasse'])) {
    $sql = "INSERT INTO user (Nom, Prenom, Email, Motdepasse) VALUES (:nom, :prenom, :email, :motdepasse)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':nom' => $_POST['nom'],
        ':prenom' => $_POST['prenom'],
        ':email' => $_POST['email'],
        ':motdepasse' => $_POST['motdepasse'] 
    ]);

    $_SESSION['nom'] = $_POST['nom'];
    $_SESSION['prenom'] = $_POST['prenom'];

    header("Location: indexx.php");
    exit;
}
?>
