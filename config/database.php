<?php
// PDO connection. Adjust credentials for your XAMPP setup.

class Database
{
    private static $pdo = null;

    public static function connect()
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=127.0.0.1;dbname=techhouse;charset=utf8mb4';
            $opts = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            );
            try {
                self::$pdo = new PDO($dsn, 'root', '', $opts);
            } catch (PDOException $e) {
                echo 'Erreur de connexion : ' . $e->getMessage();
                exit;
            }
        }
        return self::$pdo;
    }
}
