<?php
require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/blog.php';

class BlogController {

    function fetchBlog() {
        $sql = "SELECT * FROM blog";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            echo "Erreur : ".$e->getMessage();
            return [];
        }
    }

    function addBlog($blog) {
        $sql = "INSERT INTO blog(titre, contenu, date_pub) VALUES (:titre, :contenu, :date)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(":titre", $blog->getTitre());
            $query->bindValue(":contenu", $blog->getContenu());
            $query->bindValue(":date", $blog->getDate());
            $query->execute();
        } catch (Exception $e) {
            echo "Erreur : ".$e->getMessage();
        }
    }

    function updateBlog($blog, $id) {
        $sql = "UPDATE blog SET titre=:titre, contenu=:contenu, date_pub=:date WHERE id=:id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        try {
            $query->execute([
                "titre" => $blog->getTitre(),
                "contenu" => $blog->getContenu(),
                "date" => $blog->getDate(),
                "id" => $id
            ]);
        } catch (Exception $e) {
            echo "Erreur : ".$e->getMessage();
        }
    }

    function fetchBlogById($id) {
        $sql = "SELECT * FROM blog WHERE id=:id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);
        $query->execute();
        return $query->fetch();
    }

    public function fetchBlogsWithStats() {
        $sql = "
            SELECT 
                b.id, b.titre, b.date_pub,
                IFNULL(s.vues, 0) AS vues,
                IFNULL(s.likes, 0) AS likes,
                IFNULL(s.dislikes, 0) AS dislikes,
                (SELECT COUNT(*) FROM commentaire c WHERE c.id_blog = b.id) AS nb_commentaires
            FROM blog b
            LEFT JOIN blog_stats s ON b.id = s.blog_id
            ORDER BY b.date_pub DESC
        ";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            echo "Erreur : ".$e->getMessage();
            return [];
        }
    }

    public function searchBlogs($params = []) {
        $sql = "
            SELECT 
                b.id, b.titre, b.date_pub,
                IFNULL(s.vues, 0) AS vues,
                IFNULL(s.likes, 0) AS likes,
                IFNULL(s.dislikes, 0) AS dislikes,
                (SELECT COUNT(*) FROM commentaire c WHERE c.id_blog = b.id) AS nb_commentaires
            FROM blog b
            LEFT JOIN blog_stats s ON b.id = s.blog_id
            WHERE 1
        ";

        $conditions = [];
        $values = [];

        // Filtrer par titre
        if (!empty($params['titre'])) {
            $conditions[] = "b.titre LIKE :titre";
            $values[':titre'] = "%" . $params['titre'] . "%";
        }

        // Ajouter d'autres filtres ici si nécessaire

        if ($conditions) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        // Tri
        if (!empty($params['sort'])) {
            switch($params['sort']) {
                case 'vues_desc': $sql .= " ORDER BY vues DESC"; break;
                case 'likes_desc': $sql .= " ORDER BY likes DESC"; break;
                case 'date_asc': $sql .= " ORDER BY b.date_pub ASC"; break;
                case 'date_desc': $sql .= " ORDER BY b.date_pub DESC"; break;
                default: $sql .= " ORDER BY b.date_pub DESC";
            }
        } else {
            $sql .= " ORDER BY b.date_pub DESC";
        }

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($values);
            return $query->fetchAll();
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        }
    }

    function deleteBlog($id) {
        $sql = "DELETE FROM blog WHERE id=:id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        try {
            $query->execute(["id" => $id]);
        } catch (Exception $e) {
            echo "Erreur : ".$e->getMessage();
        }
    }

}

// Gestion des actions pour ajouter un blog
if (isset($_GET['action'])) {
    $blogC = new BlogController();

    if ($_GET['action'] === 'add') {
        if (!empty($_POST['titre']) && !empty($_POST['contenu'])) {
            $blog = new Blog(
                null,
                $_POST['titre'],
                $_POST['contenu'],
                date("Y-m-d")
            );
            $blogC->addBlog($blog);
            header("Location: ../view/front/articles.php");
            exit();
        } else {
            echo "Veuillez remplir tous les champs.";
        }
    }
}
?>
