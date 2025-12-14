<?php

class config {
  private static $pdo=null;
  public static function getConnexion(){
    if(!isset(self::$pdo))
    {
      try {
        $server_name = "localhost";
        $db_name = "rebornart_db";
        $db_user = "root";
        $db_pss = "";
        self::$pdo = new PDO("mysql:host=$server_name;dbname=$db_name",$db_user,$db_pss,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        //echo "database connected successfully";
      } catch (Exception $e) {
        die("Erreur ".$e->getMessage());
      }
    }
    return self::$pdo;
  }
}



?>