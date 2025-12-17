<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Notif.php';

class NotifController {

    private $notif;

    public function __construct() {
        $this->notif = new Notif(config::getConnexion());
    }

    // 🔔 récupérer notifications non lues
    public function unread() {
        session_start();
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            exit;
        }

        echo json_encode(
            $this->notif->getUnreadByUser($_SESSION['user']['id'])
        );
    }

    // ✔️ marquer comme lue
    public function read() {
        if (!isset($_POST['id'])) exit;

        $this->notif->markAsRead((int)$_POST['id']);
    }
}
