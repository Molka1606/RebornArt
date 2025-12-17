<?php
require_once __DIR__ . '/Database.php';

class Event {

    private $db;
    private $uploadPath;

    public function __construct() {
        $this->db = (new Database())->connect();
        $this->uploadPath = __DIR__ . '/../../uploads/events/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    public function getAll() {
        $sql = "SELECT * FROM events ORDER BY id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
   
        return $this->find($id);
    }

    public function create($title, $location, $event_date, $description, $image, $latitude = null, $longitude = null) {
        $stmt = $this->db->prepare(
            "INSERT INTO events (title, location, event_date, description, image, latitude, longitude)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$title, $location, $event_date, $description, $image, $latitude, $longitude]);
    }

    public function update($id, $title, $location, $event_date, $description, $image = null, $latitude = null, $longitude = null) {
        if ($image) {
            $sql = "UPDATE events SET title=?, location=?, event_date=?, description=?, image=?, latitude=?, longitude=? WHERE id=?";
            $params = [$title, $location, $event_date, $description, $image, $latitude, $longitude, $id];
        } else {
            $sql = "UPDATE events SET title=?, location=?, event_date=?, description=?, latitude=?, longitude=? WHERE id=?";
            $params = [$title, $location, $event_date, $description, $latitude, $longitude, $id];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $event = $this->find($id);
        if ($event && $event["image"] && file_exists($this->uploadPath . $event["image"])) {
            unlink($this->uploadPath . $event["image"]);
        }

        $stmt = $this->db->prepare("DELETE FROM events WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function uploadImage($file) {
        if (!$file || $file["error"] !== 0) return null;

        $allowed = ["jpg", "jpeg", "png"];
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) return null;

        $filename = uniqid("event_") . "." . $ext;
        move_uploaded_file($file["tmp_name"], $this->uploadPath . $filename);

        return $filename;
    }
    public function createPublic($title, $description, $location, $date, $image, $latitude = null, $longitude = null) {
        $sql = "INSERT INTO events (title, description, location, event_date, image, latitude, longitude, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $description, $location, $date, $image, $latitude, $longitude]);
    }

}
