<?php

class Database
{
    private static $pdo = null;
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/DbConfig.php';
    }

    public function getPdo()
    {
        if (self::$pdo instanceof \PDO) {
            return self::$pdo;
        } else {
            try {
                $opts = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false];
                self::$pdo = new PDO("mysql:host={$this->config['host']};dbname={$this->config['database']};charset=utf8",$this->config['username'],$this->config['password'],$opts);
                return self::$pdo;
            } catch (PDOException $e) {
                error_log("Błąd połączenia: " . $e->getMessage());
                throw new Exception("Nie udało się połączyć z bazą danych.");
            }
        }
    }
}
