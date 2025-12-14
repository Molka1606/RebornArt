<?php
require_once '../../model/config.php'; // connexion PDO

if (!isset($_GET['id'])) {
    die("ID manquant");
}

$id = (int)$_GET['id'];

try {
    $db = config::getConnexion();
    $stmt = $db->prepare("DELETE FROM blog WHERE id=:id");
    $stmt->execute([':id' => $id]);
    
    // Redirige vers showblog.php après suppression
    header("Location: showblog.php");
    exit();
} catch (Exception $e) {
    die("Erreur lors de la suppression : " . $e->getMessage());
}
