<?php
include_once __DIR__ . '/Database.php'; 
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
include_once __DIR__ . '/../services/ClothingExpiryService.php';
include_once __DIR__ . '/../services/WarehouseService.php';
include_once __DIR__ . '/../services/IssueService.php';
include_once __DIR__ . '/../services/OrderService.php';

/**
 * Kontener zależności - jedyny właściciel PDO i zarządca cyklu życia obiektów
 */
class ServiceContainer {
    private static ?ServiceContainer $instance = null;
    private PDO $pdo;
    private array $repositories = [];
    private array $services = [];
    
    private function __construct() {
        $this->pdo = Database::createPdo();
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
    
    /**
     * Pobiera serwis z kontenera (lazy loading + singleton per container)
     * @param string $serviceName Nazwa serwisu
     * @return mixed
     */
    public function getService($serviceName) {
        if (!isset($this->services[$serviceName])) {
            $this->services[$serviceName] = $this->createService($serviceName);
        }
        return $this->services[$serviceName];
    }
    
    private function createService($serviceName) {
        switch ($serviceName) {
            case 'ClothingExpiryService':
                return new ClothingExpiryService();
            case 'WarehouseService':
                return new WarehouseService($this);
            case 'IssueService':
                return new IssueService($this);
            case 'OrderService':
                return new OrderService($this);
            default:
                throw new Exception("Service $serviceName not found");
        }
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
                return new IssuedClothingRepository($this->pdo, $this->getService('ClothingExpiryService'));
            case 'CodeRepository':
                return new CodeRepository($this->pdo);
            default:
                throw new Exception("Repository $repositoryName not found");
        }
    }
}
