<?php
namespace App\Repositories;

use PDO;

abstract class BaseRepository {
    protected PDO $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    protected function getPdo(): PDO {
        return $this->pdo;
    }
}

