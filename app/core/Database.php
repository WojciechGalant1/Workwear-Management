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
        $config = \App\Config\DbConfig::get();
        
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
        
        // Add Unix socket support if configured (for flexibility)
        if (!empty($config['unix_socket'])) {
             $dsn = "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']};charset={$config['charset']}";
        }

        try {
            return new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            error_log("DB connection error: " . $e->getMessage());
            throw new RuntimeException("Database connection failed");
        }
    }
    
    // Prywatny konstruktor zapobiega instancjonowaniu
    private function __construct() {}
}
