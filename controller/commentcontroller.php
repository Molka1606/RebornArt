<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/Notif.php';
require_once __DIR__ . '/../model/commentaire.php';

class CommentaireController {

    /* =========================
       FETCH
    ========================== */

    public function fetchCommentairesByBlog($blog_id) {
        $sql = "SELECT * FROM commentaires WHERE blog_id = :blog_id ORDER BY date_pub DESC";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['blog_id' => $blog_id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchCommentaireById($id) {
        $db = config::getConnexion();
        $stmt = $db->prepare("SELECT * FROM commentaires WHERE id=:id");
        $stmt->execute(['id'=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       CRUD COMMENTAIRE
    ========================== */

public function addCommentaire($commentaire) {
    $db = config::getConnexion();

    $sql = "INSERT INTO commentaires 
            (blog_id, id_user, nom, contenu, date_pub)
            VALUES (:blog_id, :id_user, :nom, :contenu, :date)";

    $stmt = $db->prepare($sql);

    return $stmt->execute([
        'blog_id' => $commentaire->getIdBlog(),
        'id_user' => $commentaire->getIdUser(), // ✅ FK RESPECTÉE
        'nom'     => $commentaire->getAuteur(),
        'contenu' => $commentaire->getContenu(),
        'date'    => $commentaire->getDate()
    ]);
}

public function updateCommentaire(Commentaire $c, int $id) {
    $db = config::getConnexion();

    $sql = "UPDATE commentaires 
            SET contenu = :contenu,
                nom = :nom,
                date_pub = :date_pub
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    return $stmt->execute([
        'contenu'  => $c->getContenu(),
        'nom'      => $c->getAuteur(),   // ⚠️ méthode correcte
        'date_pub' => $c->getDate(),
        'id'       => $id
    ]);
}


    public function deleteCommentaire($id) {
        $db = config::getConnexion();

        $sql = "DELETE FROM commentaires WHERE id = :id"; // ✅ PLURIEL

        $stmt = $db->prepare($sql);
        return $stmt->execute(['id' => (int)$id]);
    }


    /* =========================
       🔥 LIKE / DISLIKE AVEC ANNULATION
    ========================== */
public function fetchCommentaires() {
    $db = config::getConnexion();

    $sql = "
        SELECT 
            c.id,
            c.contenu,
            c.blog_id,
            c.date_pub,
            u.nom
        FROM commentaires c
        JOIN user u ON c.id_user = u.id
        ORDER BY c.date_pub DESC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function reactCommentaire($userId, $commentaireId, $reaction) {
        $db = config::getConnexion();

        $stmt = $db->prepare("
            SELECT reaction FROM reactions
            WHERE user_id=:uid
            AND target_type='commentaire'
            AND target_id=:cid
        ");
        $stmt->execute([
            'uid'=>$userId,
            'cid'=>$commentaireId
        ]);
        $existing = $stmt->fetch();

        if ($existing) {
            if ($existing['reaction'] === $reaction) {
                // 🔄 ANNULER
                $del = $db->prepare("
                    DELETE FROM reactions
                    WHERE user_id=:uid AND target_type='commentaire' AND target_id=:cid
                ");
                $del->execute(['uid'=>$userId,'cid'=>$commentaireId]);
            } else {
                // 🔁 SWITCH
                $upd = $db->prepare("
                    UPDATE reactions SET reaction=:reaction
                    WHERE user_id=:uid AND target_type='commentaire' AND target_id=:cid
                ");
                $upd->execute([
                    'reaction'=>$reaction,
                    'uid'=>$userId,
                    'cid'=>$commentaireId
                ]);
            }
        } else {
            // ➕ NOUVELLE RÉACTION
            $ins = $db->prepare("
                INSERT INTO reactions (user_id,target_type,target_id,reaction)
                VALUES (:uid,'commentaire',:cid,:reaction)
            ");
            $ins->execute([
                'uid'=>$userId,
                'cid'=>$commentaireId,
                'reaction'=>$reaction
            ]);
        }

        $this->updateCommentStats($commentaireId);
    }

    private function updateCommentStats($commentaireId) {
        $db = config::getConnexion();
        $sql = "
            UPDATE commentaires SET
            likes = (SELECT COUNT(*) FROM reactions 
                     WHERE target_type='commentaire' AND target_id=:id AND reaction='like'),
            dislikes = (SELECT COUNT(*) FROM reactions 
                        WHERE target_type='commentaire' AND target_id=:id AND reaction='dislike')
            WHERE id=:id
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id'=>$commentaireId]);
    }
}

/* =========================
   ROUTER ACTIONS
========================= */

if (isset($_GET['action'])) {

    $commentC = new CommentaireController();

if ($_GET['action'] === 'add') {

    if (!isset($_SESSION['user']['id'])) {
        die("Utilisateur non connecté");
    }

    if (!empty($_POST['id_blog']) && !empty($_POST['contenu'])) {

        $id_user = (int) $_SESSION['user']['id'];     // ✅ INT
        $nom     = $_SESSION['user']['nom'];          // ✅ STRING

        $commentaire = new Commentaire(
            null,
            (int) $_POST['id_blog'],
            $id_user,                                 // ✅ CORRECT
            $nom,
            trim($_POST['contenu']),
            date("Y-m-d H:i:s")
        );

        $commentC = new CommentaireController();
        $commentC->addCommentaire($commentaire);

        /* =========================
        🔔 NOTIFICATION
        ========================= */

        // connexion DB
        $db = config::getConnexion();

        // récupérer le propriétaire de l’article
        $stmt = $db->prepare("SELECT id_user FROM blog WHERE id = :id");
        $stmt->execute(['id' => (int)$_POST['id_blog']]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        // créer notification si ce n’est pas lui-même
        if ($article && $article['id_user'] != $_SESSION['user']['id']) {

            $notif = new Notif($db);

            $notif->add(
                $article['id_user'],               // utilisateur à notifier
                $_SESSION['user']['nom'],           // auteur du commentaire
                'comment',                          // type
                (int)$_POST['id_blog'],             // référence article
                'a commenté votre article'           // message
            );
            // 📧 ENVOI EMAIL AU PROPRIÉTAIRE DE L’ARTICLE

$mail = new PHPMailer(true);

try {
    // récupérer email du propriétaire
    $stmt = $db->prepare("SELECT email, nom FROM user WHERE id = :id");
    $stmt->execute(['id' => $article['id_user']]);
    $owner = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($owner) {

        // ⚙️ CONFIG SMTP (Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'molkazaza9@gmail.com';        // 🔴 change
        $mail->Password   = 'milj syuv ssff tuba';   // 🔴 change
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // expéditeur
        $mail->setFrom('molkazaza9@gmail.com', 'RebornArt');

        // destinataire
        $mail->addAddress($owner['email'], $owner['nom']);

        // contenu
        $mail->isHTML(true);
        $mail->Subject = 'Nouveau commentaire sur votre article';
        $mail->Body = "
            <p>Bonjour <strong>{$owner['nom']}</strong>,</p>
            <p><strong>{$_SESSION['user']['nom']}</strong> a commenté votre article.</p>
            <p>
                <a href='http://localhost/RebornArt/views/view/front/detailartc.php?id=".(int)$_POST['id_blog']."'>
                    👉 Voir l’article
                </a>
            </p>
            <br>
            <p>L’équipe RebornArt</p>
        ";

        $mail->send();
    }

} catch (Exception $e) {
    // optionnel : log erreur
}

        }


        header("Location: ../views/view/front/detailartc.php?id=".(int)$_POST['id_blog']);
        exit;

    }
}

if ($_GET['action'] === 'update') {

    if (!isset($_SESSION['user']['id'])) {
        die("Utilisateur non connecté");
    }

    $id       = (int)($_GET['id'] ?? 0);
    $id_blog  = (int)($_POST['id_blog'] ?? 0);
    $contenu = trim($_POST['contenu'] ?? '');
    $nom = !empty($_POST['nom']) ? trim($_POST['nom']) : $_SESSION['user']['nom'];

    if ($id && $id_blog && !empty($contenu)) {

        $commentaire = new Commentaire(
            $id,
            $id_blog,
            (int)$_SESSION['user']['id'],   // ✅ id_user OBLIGATOIRE
            $nom,
            $contenu,
            date("Y-m-d H:i:s")
        );

        $commentC->updateCommentaire($commentaire, $id);

        header("Location: ../views/view/front/detailartc.php?id=".$id_blog);
        exit();
    }

    header("Location: ../views/view/front/detailartc.php?id=".$id_blog."&error=update_failed");
    exit();
}

    if ($_GET['action'] === 'delete') {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $redirect_blog = $_GET['blog'] ?? null;
            $commentC->deleteCommentaire($id);
            if ($redirect_blog) {
                header("Location: ../views/view/front/detailartc.php?id=" . (int)$redirect_blog);
            } else {
                header("Location: ../views/view/back/showcomment.php");
            }
            exit();
        }
    }
    if (($_GET['action'] === 'like' || $_GET['action'] === 'dislike') && isset($_GET['id'])) {

    // ✅ même session que add()
    if (!isset($_SESSION['user']['id'])) {
        header("Location: ../views/view/front/detailartc.php");
        exit();
    }

    // ✅ blog id obligatoire pour revenir à detailartc
    $idBlog = isset($_GET['id_blog']) ? (int)$_GET['id_blog'] : 0;

    $commentC->reactCommentaire(
        (int)$_SESSION['user']['id'],  // ✅ au lieu de $_SESSION['user_id']
        (int)$_GET['id'],              // id commentaire
        $_GET['action']                // like ou dislike
    );

    header("Location: ../views/view/front/detailartc.php?id=".$idBlog);
    exit();
}

}
