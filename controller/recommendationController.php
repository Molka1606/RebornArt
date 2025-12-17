<?php
require_once __DIR__ . '/../model/config.php';

/* ======================================
   🧠 LOGIQUE RÉUTILISABLE
====================================== */
function getRecommendationsForUser($userId) {

    if ($userId <= 0) return [];

    $db = config::getConnexion();

    // 1️⃣ catégories aimées
    $sqlCategories = "
        SELECT DISTINCT b.categorie
        FROM blog b
        LEFT JOIN commentaires c ON c.blog_id = b.id
        LEFT JOIN reactions r 
            ON r.target_id = b.id AND r.target_type = 'blog'
        WHERE c.id_user = :uid
           OR (r.user_id = :uid AND r.reaction = 'like')
    ";

    $stmt = $db->prepare($sqlCategories);
    $stmt->execute(['uid' => $userId]);
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($categories)) return [];

    // 2️⃣ recommandations
    $placeholders = implode(',', array_fill(0, count($categories), '?'));

    $sqlReco = "
        SELECT id, titre
        FROM blog
        WHERE categorie IN ($placeholders)
        ORDER BY date_pub DESC
        LIMIT 5
    ";

    $stmt = $db->prepare($sqlReco);
    $stmt->execute($categories);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ======================================
   🌐 MODE API JSON (URL)
====================================== */
if (isset($_GET['user_id'])) {

    header('Content-Type: application/json');

    $userId = (int) $_GET['user_id'];

    echo json_encode([
        'recommended_blogs' => getRecommendationsForUser($userId)
    ]);
}
