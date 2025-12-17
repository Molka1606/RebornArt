<?php
require_once "Database.php";

class Reservation {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function create($name, $email, $event_id, $msg) {
        $sql = "INSERT INTO reservation(full_name, email, event_id, message)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute([$name, $email, $event_id, $msg])) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM reservation ORDER BY id DESC")
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM reservation WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name, $email, $event_id, $msg) {
        $sql = "UPDATE reservation 
                SET full_name=?, email=?, event_id=?, message=? 
                WHERE id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $email, $event_id, $msg, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM reservation WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function countAll() {
        return $this->db->query("SELECT COUNT(*) FROM reservation")->fetchColumn();
    }

    public function countPending() {
        return $this->db->query("SELECT COUNT(*) FROM reservation WHERE message != ''")->fetchColumn();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM reservation WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
