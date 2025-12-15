<?php

class config {
    private static $pdo = null;

    public static function getConnexion() {

        // Charger le fichier .env s’il existe
        if (file_exists(__DIR__ . '/../.env')) {
            $lines = file(__DIR__ . '/../.env');
            foreach ($lines as $line) {
                if (trim($line) !== '' && strpos($line, '=') !== false) {
                    putenv(trim($line));
                }
            }
        }

        if (!isset(self::$pdo)) {

            // Lire depuis .env
            $servername = getenv('DB_HOST');
            $dbname     = getenv('DB_NAME');
            $username   = getenv('DB_USER');
            $password   = getenv('DB_PASS');

            try {
                self::$pdo = new PDO(
                    "mysql:host=$servername;dbname=$dbname",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (Exception $e) {
                die("Erreur connexion : " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
?>
