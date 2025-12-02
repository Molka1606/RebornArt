<?php
require_once "../../controller/userController.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $mdp = $_POST["motdepasse"];
    $cmdp = $_POST["cmotdepasse"];

    if (empty($email) || empty($mdp) || empty($cmdp)) {
        echo "<script>alert('Veuillez remplir tous les champs'); window.history.back();</script>";
        exit;
    }

    if ($mdp !== $cmdp) {
        echo "<script>alert('Les mots de passe ne correspondent pas'); window.history.back();</script>";
        exit;
    }

    $controller = new userController();
    $result = $controller->resetPassword($email, $mdp);

    if ($result === 'ok') {
        echo "<script>alert('Mot de passe modifié avec succès'); window.location='signin.html';</script>";
    } else {
        echo "<script>alert('$result'); window.history.back();</script>";
    }
}
?>
