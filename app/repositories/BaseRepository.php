<?php

abstract class BaseRepository {
    protected $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    protected function getPdo() {
        return $this->pdo;
    }
}

