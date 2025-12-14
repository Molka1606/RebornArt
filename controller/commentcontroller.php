<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/commentaire.php';

class CommentaireController {

    // Récupérer tous les commentaires
    public function fetchCommentaires() {
        $sql = "SELECT * FROM commentaire ORDER BY date_pub DESC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Récupérer un commentaire par id
    public function fetchCommentaireById($id) {
        $sql = "SELECT * FROM commentaire WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return null;
        }
    }

    // Récupérer les commentaires d'un blog spécifique
    public function fetchCommentairesByBlog($blog_id) {
        $sql = "SELECT * FROM commentaire WHERE blog_id = :blog_id ORDER BY date_pub DESC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':blog_id', $blog_id, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Ajouter un commentaire
    public function addCommentaire($commentaire) {
        $sql = "INSERT INTO commentaire(blog_id, nom, contenu, date_pub) 
                VALUES (:blog_id, :nom, :contenu, :date)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(":blog_id", $commentaire->getIdBlog(), PDO::PARAM_INT);
            $query->bindValue(":nom", $commentaire->getAuteur(), PDO::PARAM_STR);
            $query->bindValue(":contenu", $commentaire->getContenu(), PDO::PARAM_STR);
            $query->bindValue(":date", $commentaire->getDate(), PDO::PARAM_STR);
            return $query->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    // Mettre à jour un commentaire

public function updateCommentaire($commentaire, $id) {
    $sql = "UPDATE commentaire 
            SET contenu = :contenu, 
                nom = :nom,
                date_pub = :date_pub
            WHERE id = :id";

    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $result = $query->execute([
            ":contenu" => $commentaire->getContenu(),
            ":nom" => $commentaire->getAuteur(),
            ":date_pub" => $commentaire->getDate(),
            ":id" => $id
        ]);
        error_log("UPDATE SQL: $sql");
        error_log("UPDATE PARAMS - ID: $id, NOM: " . $commentaire->getAuteur() . ", CONTENU_LEN: " . strlen($commentaire->getContenu()) . ", DATE: " . $commentaire->getDate());
        error_log("UPDATE ROWS AFFECTED: " . $query->rowCount());
        return $result;
    } catch (Exception $e) {
        error_log("UPDATE EXCEPTION: " . $e->getMessage());
        return false;
    }
}

    

    // Supprimer un commentaire
    public function deleteCommentaire($id) {
        $sql = "DELETE FROM commentaire WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            return $query->execute(["id" => (int)$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    // Like un commentaire
    public function likeCommentaire($id) {
        $sql = "UPDATE commentaire SET likes = COALESCE(likes,0) + 1 WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            return $query->execute(['id' => $id]);
        } catch (Exception $e) {
            return false;
        }
    }

    // Dislike un commentaire
    public function dislikeCommentaire($id) {
        $sql = "UPDATE commentaire SET dislikes = COALESCE(dislikes,0) + 1 WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            return $query->execute(['id' => $id]);
        } catch (Exception $e) {
            return false;
        }
    }
}

// ---------------------------
// Gestion des actions front/back
// ---------------------------
if (isset($_GET['action'])) {

    $commentC = new CommentaireController();

    // Ajouter un commentaire
    if ($_GET['action'] === 'add') {
        if (!empty($_POST['id_blog']) && !empty($_POST['contenu'])) {
            $nom = $_SESSION['username'] ?? 'Invité';
            $commentaire = new Commentaire(
                null,
                (int)$_POST['id_blog'],
                $nom,
                trim($_POST['contenu']),
                date("Y-m-d H:i:s")
            );
            $commentC->addCommentaire($commentaire);
            header("Location: ../view/front/detailartc.php?id=" . (int)$_POST['id_blog']);
            exit();
        }
    }

    // Mettre à jour un commentaire
    if ($_GET['action'] === 'update') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $id_blog = isset($_POST['id_blog']) ? (int)$_POST['id_blog'] : (isset($_GET['id_blog']) ? (int)$_GET['id_blog'] : null);
        $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
        $nom = isset($_POST['auteur']) && !empty(trim($_POST['auteur'])) ? trim($_POST['auteur']) : ($_SESSION['username'] ?? 'Invité');

        // Debug log
        error_log("UPDATE DEBUG - ID: $id, ID_BLOG: $id_blog, CONTENU_LENGTH: " . strlen($contenu) . ", NOM: $nom");

        // Validation
        if ($id && $id_blog && !empty($contenu)) {
            $commentaire = new Commentaire(
                $id,
                $id_blog,
                $nom,
                $contenu,
                date("Y-m-d H:i:s")
            );

            $result = $commentC->updateCommentaire($commentaire, $id);
            error_log("UPDATE RESULT: " . ($result ? 'SUCCESS' : 'FAILED'));

            if ($result) {
                header("Location: ../view/front/detailartc.php?id=" . $id_blog . "&success=updated");
                exit();
            } else {
                header("Location: ../view/front/detailartc.php?id=" . $id_blog . "&error=update_failed");
                exit();
            }
        } else {
            error_log("UPDATE VALIDATION FAILED - ID: $id, ID_BLOG: $id_blog, CONTENU_EMPTY: " . (empty($contenu) ? 'YES' : 'NO'));
            // Missing or invalid parameters, redirect
            if ($id_blog) {
                header("Location: ../view/front/detailartc.php?id=" . $id_blog . "&error=missing_fields");
            } else {
                header("Location: ../view/front/articles.php?error=missing_data");
            }
            exit();
        }
    }

    // Supprimer un commentaire
    if ($_GET['action'] === 'delete') {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $redirect_blog = $_GET['blog'] ?? null;
            $commentC->deleteCommentaire($id);
            if ($redirect_blog) {
                header("Location: ../view/front/detailartc.php?id=" . (int)$redirect_blog);
            } else {
                header("Location: ../view/back/showcomment.php");
            }
            exit();
        }
    }

    // Like un commentaire
    if ($_GET['action'] === 'like' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $id_blog = $_GET['id_blog'] ?? null;
        $commentC->likeCommentaire($id);
        header("Location: ../view/front/detailartc.php?id=" . (int)$id_blog);
        exit();
    }

    // Dislike un commentaire
    if ($_GET['action'] === 'dislike' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $id_blog = $_GET['id_blog'] ?? null;
        $commentC->dislikeCommentaire($id);
        header("Location: ../view/front/detailartc.php?id=" . (int)$id_blog);
        exit();
    }

}
?>
