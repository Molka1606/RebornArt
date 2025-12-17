<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/CommentController.php';

if (!isset($_SESSION['user']['id'])) {
    die("Utilisateur non connecté");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de commentaire invalide");
}

$id_commentaire = (int) $_GET['id'];

$commentController = new CommentaireController();
$commentController->deleteCommentaire($id_commentaire);

// 🔁 REDIRECTION
if (isset($_GET['blog']) && is_numeric($_GET['blog'])) {
    header("Location: ../views/view/front/detailartc.php?id=".(int)$_GET['blog']);
    exit();
}

// fallback back-office
header("Location: ../views/view/back/showblog.php");
exit();
