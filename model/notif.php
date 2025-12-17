<?php

class Notif {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function add($idUser, $username, $type, $referenceId, $message) {

        $sql = "INSERT INTO notifications (id_user, username, type, reference_id, message)
                VALUES (:id_user, :username, :type, :reference_id, :message)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id_user'      => $idUser,
            'username'     => $username,
            'type'         => $type,
            'reference_id' => $referenceId,
            'message'      => $message
        ]);
    }

    public function getUnreadByUser($idUser) {
        $sql = "SELECT * FROM notifications
                WHERE id_user = :id_user AND is_read = 0
                ORDER BY date_created DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_user' => $idUser]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
