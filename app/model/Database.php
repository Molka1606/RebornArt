<?php
class Database {
    private $host = "localhost";
    private $db   = "taskdb";
    private $user = "root";
    private $pass = "";

    public function connect() {
        try {
            $pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4",
                $this->user,
                $this->pass
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;

        } catch (PDOException $e) {
            die("DB ERROR: " . $e->getMessage());
        }
    }
}
