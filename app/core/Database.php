<?php
declare(strict_types=1);
namespace App\Core;

use \PDO;
use \PDOException;
use \RuntimeException;


/**
 * Factory do tworzenia połączenia PDO
 * Bezstanowa klasa - nie zarządza cyklem życia PDO
 * Owner PDO: ServiceContainer
 */
class Database
{
    /**
     * Tworzy nowe połączenie PDO
     * @return PDO
     * @throws RuntimeException
     */
    public static function createPdo(): PDO
    {
        $config = require __DIR__ . '/../config/DbConfig.php';
        
        $opts = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        try {
            return new PDO(
                "mysql:host={$config['host']};dbname={$config['database']};charset=utf8",
                $config['username'],
                $config['password'],
                $opts
            );
        } catch (PDOException $e) {
            error_log("DB connection error: " . $e->getMessage());
            throw new RuntimeException("Database connection failed");
        }
    }
    
    // Prywatny konstruktor zapobiega instancjonowaniu
    private function __construct() {}
}
