<?php
session_start();

if (!isset($_SESSION['user']['id'])) {
    header("Location: Utilisateur/signIn.html");
    exit;
}

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/notif.php';

$db = config::getConnexion();
$notifModel = new Notif($db);

// récupérer toutes les notifications
$stmt = $db->prepare("
    SELECT * FROM notifications
    WHERE id_user = :id_user
    ORDER BY date_created DESC
");
$stmt->execute([
    'id_user' => $_SESSION['user']['id']
]);

$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes notifications</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .notif {
            background: white;
            padding: 15px;
            margin: 10px auto;
            width: 60%;
            border-left: 5px solid #ff4d4d;
        }
        .read { opacity: 0.6; }
    </style>
</head>
<body>

<h2 style="text-align:center;">🔔 Mes notifications</h2>

<?php if (empty($notifications)): ?>
    <p style="text-align:center;">Aucune notification</p>
<?php endif; ?>

<?php foreach ($notifications as $n): ?>
    <div class="notif <?= $n['is_read'] ? 'read' : '' ?>">
        <strong><?= htmlspecialchars($n['username']) ?></strong>
        <?= htmlspecialchars($n['message']) ?>
        <br>
        <small><?= $n['date_created'] ?></small>
    </div>
<?php endforeach; ?>

</body>
</html>
