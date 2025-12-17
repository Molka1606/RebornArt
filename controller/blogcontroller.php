<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/blog.php';

class BlogController {

    // ===============================
    // AFFICHER TOUS LES BLOGS
    // ===============================
    public function fetchBlog() {
        $sql = "SELECT * FROM blog ORDER BY date_pub DESC";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // AJOUTER UN BLOG
    // ===============================
    public function addBlog(Blog $blog, int $id_user) {

        $sql = "INSERT INTO blog (titre, contenu, image, date_pub, id_user)
                VALUES (:titre, :contenu, :image, :date_pub, :id_user)";

        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute([
            'titre'     => $blog->getTitre(),
            'contenu'   => $blog->getContenu(),
            'image'     => $blog->getImage(),
            'date_pub'  => $blog->getDate(),
            'id_user'   => $id_user
        ]);
    }

    // ===============================
    // MODIFIER UN BLOG
    // ===============================
    public function updateBlog(Blog $blog, int $id) {
        $sql = "UPDATE blog 
                SET titre = :titre, contenu = :contenu, image = :image, date_pub = :date
                WHERE id = :id";

        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute([
            'titre'   => $blog->getTitre(),
            'contenu' => $blog->getContenu(),
            'image'   => $blog->getImage(),
            'date'    => $blog->getDate(),
            'id'      => $id
        ]);
    }

    // ===============================
    // SUPPRIMER UN BLOG
    // ===============================
    public function deleteBlog($id) {
        $db = config::getConnexion();

        try {
            // 1️⃣ Supprimer stats
            $stmtStats = $db->prepare("DELETE FROM blog_stats WHERE blog_id = :id");
            $stmtStats->execute(['id' => $id]);

            // 2️⃣ Supprimer commentaires
            $stmtComments = $db->prepare("DELETE FROM commentaires WHERE blog_id = :id");
            $stmtComments->execute(['id' => $id]);

            // 3️⃣ Supprimer blog
            $stmtBlog = $db->prepare("DELETE FROM blog WHERE id = :id");
            $stmtBlog->execute(['id' => $id]);

        } catch (PDOException $e) {
            die("Erreur lors de la suppression : " . $e->getMessage());
        }
    }

    // ===============================
    // BLOG PAR ID
    // ===============================
    public function fetchBlogById(int $id) {
        $sql = "SELECT * FROM blog WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }
}

/* =====================================================
   TRAITEMENT : AJOUT D’ARTICLE
===================================================== */

if (isset($_GET['action']) && $_GET['action'] === 'add') {

    if (!isset($_SESSION['user']['id'])) {
        die("❌ Utilisateur non connecté");
    }

    if (empty($_POST['titre']) || empty($_POST['contenu'])) {
        die("❌ Champs manquants");
    }

    $titre   = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $id_user = (int) $_SESSION['user']['id'];

    // ===== UPLOAD IMAGE =====
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
    }

    // ===== OBJET BLOG =====
    $blog = new Blog(
        null,
        $titre,
        $contenu,
        $imageName,
        date('Y-m-d')
    );

    // ===== INSERTION =====
    $controller = new BlogController();
    $controller->addBlog($blog, $id_user);

    header("Location: ../views/view/front/articles.php");
    exit;
}
