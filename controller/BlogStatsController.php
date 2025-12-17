<?php

require_once __DIR__ . '/../model/config.php';

class BlogStatsController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // ==================================================
    // S'assurer que la ligne blog_stats existe
    // ==================================================
    private function ensureRow(int $blog_id): bool {

        // Récupérer id_user depuis blog
        $stmtCheck = $this->pdo->prepare("
            SELECT id_user 
            FROM blog 
            WHERE id = :id
        ");
        $stmtCheck->execute(['id' => $blog_id]);
        $blog = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$blog) {
            return false; // blog inexistant
        }

        $id_user = $blog['id_user'];

        // Insérer stats si inexistantes
        $stmt = $this->pdo->prepare("
            INSERT INTO blog_stats (blog_id, id_user, vues, likes, dislikes)
            VALUES (:blog_id, :id_user, 0, 0, 0)
            ON DUPLICATE KEY UPDATE blog_id = blog_id
        ");

        $stmt->execute([
            'blog_id' => $blog_id,
            'id_user' => $id_user
        ]);

        return true;
    }

    // ==================================================
    // Ajouter une vue
    // ==================================================
    public function addView(int $blog_id): void {
        if (!$this->ensureRow($blog_id)) return;

        $stmt = $this->pdo->prepare("
            UPDATE blog_stats 
            SET vues = vues + 1 
            WHERE blog_id = :id
        ");
        $stmt->execute(['id' => $blog_id]);
    }

    // ==================================================
    // Ajouter un like
    // ==================================================
    public function addLike(int $blog_id): void {
        if (!$this->ensureRow($blog_id)) return;

        $stmt = $this->pdo->prepare("
            UPDATE blog_stats 
            SET likes = likes + 1 
            WHERE blog_id = :id
        ");
        $stmt->execute(['id' => $blog_id]);
    }

    // ==================================================
    // Ajouter un dislike
    // ==================================================
    public function addDislike(int $blog_id): void {
        if (!$this->ensureRow($blog_id)) return;

        $stmt = $this->pdo->prepare("
            UPDATE blog_stats 
            SET dislikes = dislikes + 1 
            WHERE blog_id = :id
        ");
        $stmt->execute(['id' => $blog_id]);
    }

    // ==================================================
    // Récupérer stats d’un blog
    // ==================================================
    public function getStatsForBlog(int $blog_id): array {
        if (!$this->ensureRow($blog_id)) {
            return ['views' => 0, 'likes' => 0, 'dislikes' => 0];
        }

        $stmt = $this->pdo->prepare("
            SELECT vues AS views, likes, dislikes
            FROM blog_stats
            WHERE blog_id = :id
        ");
        $stmt->execute(['id' => $blog_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC)
            ?: ['views' => 0, 'likes' => 0, 'dislikes' => 0];
    }

    // ==================================================
    // Articles les plus vus
    // ==================================================
    public function getTopArticles(int $limit = 5): array {
        $stmt = $this->pdo->prepare("
            SELECT b.id, b.titre, s.vues
            FROM blog_stats s
            JOIN blog b ON s.blog_id = b.id
            ORDER BY s.vues DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// ==================================================
// ENDPOINTS LIKE / DISLIKE
// ==================================================
if (isset($_GET['action']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $blog_id = (int)($_POST['blog_id'] ?? 0);
    if (!$blog_id) exit;

    $sc = new BlogStatsController();

    if ($_GET['action'] === 'like') {
        $sc->addLike($blog_id);
    } elseif ($_GET['action'] === 'dislike') {
        $sc->addDislike($blog_id);
    }

    header("Location: /RebornArt/views/view/front/detailartc.php?id=" . $blog_id);
exit;

    
}
