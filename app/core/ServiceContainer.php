<?php
include_once __DIR__ . '/database/Database.php'; 
include_once __DIR__ . '/../repositories/BaseRepository.php';
include_once __DIR__ . '/../repositories/WarehouseRepository.php';
include_once __DIR__ . '/../repositories/ClothingRepository.php';
include_once __DIR__ . '/../repositories/SizeRepository.php';
include_once __DIR__ . '/../repositories/OrderHistoryRepository.php';
include_once __DIR__ . '/../repositories/OrderDetailsRepository.php';
include_once __DIR__ . '/../repositories/EmployeeRepository.php';
include_once __DIR__ . '/../repositories/UserRepository.php';
include_once __DIR__ . '/../repositories/IssueRepository.php';
include_once __DIR__ . '/../repositories/IssuedClothingRepository.php';
include_once __DIR__ . '/../repositories/CodeRepository.php';

class ServiceContainer {
    private static $instance = null;
    private $pdo;
    private $repositories = [];
    
    private function __construct() {
        $db = new Database();
        $this->pdo = $db->getPdo();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getPdo() {
        return $this->pdo;
    }
    
    public function getRepository($repositoryName) {
        if (!isset($this->repositories[$repositoryName])) {
            $this->repositories[$repositoryName] = $this->createRepository($repositoryName);
        }
        return $this->repositories[$repositoryName];
    }
    
    private function createRepository($repositoryName) {
        switch ($repositoryName) {
            case 'WarehouseRepository':
                return new WarehouseRepository($this->pdo);
            case 'ClothingRepository':
                return new ClothingRepository($this->pdo);
            case 'SizeRepository':
                return new SizeRepository($this->pdo);
            case 'OrderHistoryRepository':
                return new OrderHistoryRepository($this->pdo);
            case 'OrderDetailsRepository':
                return new OrderDetailsRepository($this->pdo);
            case 'EmployeeRepository':
                return new EmployeeRepository($this->pdo);
            case 'UserRepository':
                return new UserRepository($this->pdo);
            case 'IssueRepository':
                return new IssueRepository($this->pdo);
            case 'IssuedClothingRepository':
                return new IssuedClothingRepository($this->pdo);
            case 'CodeRepository':
                return new CodeRepository($this->pdo);
            default:
                throw new Exception("Repository $repositoryName not found");
        }
    }
}
