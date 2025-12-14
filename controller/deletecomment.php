<?php
require_once __DIR__ . '/CommentController.php';

$commentController = new CommentaireController();

// Vérifie si un ID a été envoyé
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_commentaire = intval($_GET['id']);

    // Récupération optionnelle : redirection après suppression
    $redirect_blog = $_GET['blog'] ?? null;

    // Suppression
    $commentController->deleteCommentaire($id_commentaire);

    // Si on vient du front → retour à l’article
    if ($redirect_blog !== null) {
        header("Location: ../view/front/detailartc.php?id=" . $redirect_blog);
        exit();
    }

    // Sinon → on vient du back → retour à la liste des commentaires
    header("Location: ../view/back/showcomment.php");
    exit();
}

// Si aucun id → erreur simple
echo "Erreur : aucun ID de commentaire spécifié.";
exit();
