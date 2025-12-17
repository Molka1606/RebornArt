<?php

class BlogStats {

    private $pdo;

    public function __construct() {
        require_once __DIR__ . '/../model/config.php';
        $this->pdo = config::getConnexion();
    }

    // 🚀 Ajouter une vue
    public function addView($blog_id) {
        $sql = "INSERT INTO blog_stats (blog_id, vues)
                VALUES (:blog_id, 1)
                ON DUPLICATE KEY UPDATE vues = vues + 1";
        $req = $this->pdo->prepare($sql);
        $req->execute(['blog_id' => $blog_id]);
    }

    // 🔥 Obtenir les vues d’un article
    public function getViews($blog_id) {
        $sql = "SELECT vues FROM blog_stats WHERE blog_id = :id";
        $req = $this->pdo->prepare($sql);
        $req->execute(['id' => $blog_id]);
        $res = $req->fetch();
        return $res ? $res['vues'] : 0;
    }

    // 📊 Top articles les plus vus
    public function getTopArticles($limit = 5) {
        $sql = "SELECT blog_id, vues 
                FROM blog_stats 
                ORDER BY vues DESC 
                LIMIT $limit";
        return $this->pdo->query($sql)->fetchAll();
    }

    // 📝 Nombre total de vues (dashboard)
    public function getTotalViews() {
        $sql = "SELECT SUM(vues) AS total FROM blog_stats";
        return $this->pdo->query($sql)->fetch()['total'];
    }
}
