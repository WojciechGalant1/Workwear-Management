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
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getPdo(): PDO {
        return $this->pdo;
    }
    
    public function getRepository(string $repositoryName): object {
        if (!isset($this->repositories[$repositoryName])) {
            $this->repositories[$repositoryName] = $this->createRepository($repositoryName);
        }
        return $this->repositories[$repositoryName];
    }
    
    /**
     * Pobiera serwis z kontenera (lazy loading + singleton per container)
     * @param string $serviceName Nazwa serwisu
     * @return object
     */
    public function getService(string $serviceName): object {
        if (!isset($this->services[$serviceName])) {
            $this->services[$serviceName] = $this->createService($serviceName);
        }
        return $this->services[$serviceName];
    }
    
    private function createService(string $serviceName): object {
        return match($serviceName) {
            'ClothingExpiryService' => new ClothingExpiryService(),
            'WarehouseService' => new WarehouseService($this),
            'IssueService' => new IssueService($this),
            'OrderService' => new OrderService($this),
            default => throw new Exception("Service $serviceName not found")
        };
    }
    
    private function createRepository(string $repositoryName): object {
        return match($repositoryName) {
            'WarehouseRepository' => new WarehouseRepository($this->pdo),
            'ClothingRepository' => new ClothingRepository($this->pdo),
            'SizeRepository' => new SizeRepository($this->pdo),
            'OrderHistoryRepository' => new OrderHistoryRepository($this->pdo),
            'OrderDetailsRepository' => new OrderDetailsRepository($this->pdo),
            'EmployeeRepository' => new EmployeeRepository($this->pdo),
            'UserRepository' => new UserRepository($this->pdo),
            'IssueRepository' => new IssueRepository($this->pdo),
            'IssuedClothingRepository' => new IssuedClothingRepository($this->pdo, $this->getService('ClothingExpiryService')),
            'CodeRepository' => new CodeRepository($this->pdo),
            default => throw new Exception("Repository $repositoryName not found")
        };
    }
}
