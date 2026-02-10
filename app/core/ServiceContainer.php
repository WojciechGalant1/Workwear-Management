<?php
declare(strict_types=1);
namespace App\Core;

use App\Core\Database;
use App\Services\ClothingExpiryService;
use App\Services\WarehouseService;
use App\Services\IssueService;
use App\Services\OrderService;
use App\Auth\AccessGuard;
use App\Auth\SessionManager;
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
    
    public function getService(string $serviceName): object {
        if (!isset($this->services[$serviceName])) {
            $this->services[$serviceName] = $this->createService($serviceName);
        }
        return $this->services[$serviceName];
    }
    
    private function createService(string $serviceName): object {
        return match($serviceName) {
            ClothingExpiryService::class => new ClothingExpiryService(),
            WarehouseService::class => new WarehouseService(
                $this->getRepository(WarehouseRepository::class),
                $this->getRepository(ClothingRepository::class),
                $this->getRepository(SizeRepository::class),
                $this->getRepository(OrderHistoryRepository::class),
                $this->getRepository(OrderDetailsRepository::class)
            ),
            IssueService::class => new IssueService(
                $this->getRepository(EmployeeRepository::class),
                $this->getRepository(UserRepository::class),
                $this->getRepository(WarehouseRepository::class),
                $this->getRepository(IssueRepository::class),
                $this->getRepository(IssuedClothingRepository::class)
            ),
            OrderService::class => new OrderService(
                $this->getRepository(OrderHistoryRepository::class),
                $this->getRepository(OrderDetailsRepository::class),
                $this->getRepository(ClothingRepository::class),
                $this->getRepository(SizeRepository::class),
                $this->getRepository(CodeRepository::class),
                $this->getRepository(WarehouseRepository::class),
                $this->getService(WarehouseService::class),
                $this->getRepository(UserRepository::class)
            ),
            SessionManager::class => new SessionManager(),
            AccessGuard::class => new AccessGuard(
                $this->getService(SessionManager::class),
                $this->getRepository(UserRepository::class)
            ),
            default => throw new \Exception("Service $serviceName not found")
        };
    }
    
    private function createRepository(string $repositoryName): object {
        return match($repositoryName) {
            WarehouseRepository::class => new WarehouseRepository($this->pdo),
            ClothingRepository::class => new ClothingRepository($this->pdo),
            SizeRepository::class => new SizeRepository($this->pdo),
            OrderHistoryRepository::class => new OrderHistoryRepository($this->pdo),
            OrderDetailsRepository::class => new OrderDetailsRepository($this->pdo),
            EmployeeRepository::class => new EmployeeRepository($this->pdo),
            UserRepository::class => new UserRepository($this->pdo),
            IssueRepository::class => new IssueRepository($this->pdo),
            IssuedClothingRepository::class => new IssuedClothingRepository($this->pdo, $this->getService(ClothingExpiryService::class)),
            CodeRepository::class => new CodeRepository($this->pdo),
            default => throw new \Exception("Repository $repositoryName not found")
        };
    }
}
