<?php
namespace App\Core;

use App\Core\Database;
use App\Services\ClothingExpiryService;
use App\Services\WarehouseService;
use App\Services\IssueService;
use App\Services\OrderService;
use App\Repositories\WarehouseRepository;
use App\Repositories\ClothingRepository;
use App\Repositories\SizeRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderDetailsRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;
use App\Repositories\IssueRepository;
use App\Repositories\IssuedClothingRepository;
use App\Repositories\CodeRepository;
use PDO;

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
            default => throw new \Exception("Service $serviceName not found")
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
            default => throw new \Exception("Repository $repositoryName not found")
        };
    }
}
