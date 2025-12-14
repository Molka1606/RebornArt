<?php
require_once __DIR__ . '/../model/config.php';

class BlogStatsController {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // Assure que la ligne existe dans blog_stats
    private function ensureRow($blog_id) {
        // Vérifie si le blog existe
        $stmtCheck = $this->pdo->prepare("SELECT id FROM blog WHERE id = :id");
        $stmtCheck->execute(['id' => $blog_id]);
        if (!$stmtCheck->fetch()) {
            // Si l'article n'existe pas, on arrête
            return false;
        }

        // Vérifie ou crée la ligne blog_stats
        $stmt = $this->pdo->prepare("
            INSERT INTO blog_stats (blog_id, vues, likes, dislikes) 
            VALUES (:id, 0, 0, 0)
            ON DUPLICATE KEY UPDATE blog_id = blog_id
        ");
        $stmt->execute(['id' => $blog_id]);
        return true;
    }

    public function addView($blog_id) {
        if (!$this->ensureRow($blog_id)) return;
        $stmt = $this->pdo->prepare("UPDATE blog_stats SET vues = vues + 1 WHERE blog_id = :id");
        $stmt->execute(['id' => $blog_id]);
    }

    public function addLike($blog_id) {
        if (!$this->ensureRow($blog_id)) return;
        $stmt = $this->pdo->prepare("UPDATE blog_stats SET likes = likes + 1 WHERE blog_id = :id");
        $stmt->execute(['id' => $blog_id]);
    }

    public function addDislike($blog_id) {
        if (!$this->ensureRow($blog_id)) return;
        $stmt = $this->pdo->prepare("UPDATE blog_stats SET dislikes = dislikes + 1 WHERE blog_id = :id");
        $stmt->execute(['id' => $blog_id]);
    }

    public function getStatsForBlog($blog_id) {
        if (!$this->ensureRow($blog_id)) return ['views'=>0,'likes'=>0,'dislikes'=>0];
        $stmt = $this->pdo->prepare("
            SELECT vues AS views, likes, dislikes 
            FROM blog_stats 
            WHERE blog_id = :id
        ");
        $stmt->execute(['id' => $blog_id]);
        return $stmt->fetch() ?: ['views'=>0,'likes'=>0,'dislikes'=>0];
    }

    public function getTopArticles($limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT b.id, b.titre, s.vues
            FROM blog_stats s
            JOIN blog b ON s.blog_id = b.id
            ORDER BY s.vues DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

// Endpoints like/dislike
if (isset($_GET['action']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $sc = new BlogStatsController();
    $blog_id = (int)($_POST['blog_id'] ?? 0);
    if (!$blog_id) exit();

    if ($_GET['action'] === 'like') {
        $sc->addLike($blog_id);
    } elseif ($_GET['action'] === 'dislike') {
        $sc->addDislike($blog_id);
    }
    header("Location: ../view/front/detailartc.php?id=$blog_id");
    exit();
}
