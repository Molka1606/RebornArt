<?php
// ===================== CHARGEMENT DU .env (GLOBAL) =====================
$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // ignorer commentaires
        if (!str_contains($line, '=')) continue;

        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}
// =====================================================================

class config {
    private static $pdo = null;

    public static function getConnexion() {

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
