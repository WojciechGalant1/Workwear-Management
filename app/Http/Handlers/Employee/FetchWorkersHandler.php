<?php
declare(strict_types=1);
namespace App\Http\Handlers\Employee;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;
use App\Repositories\EmployeeRepository;

class FetchWorkersHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        $this->throttle('search:workers', 60, 60);
        
        $query = $_GET['query'] ?? '';
        
        $pracownikRepo = $this->getRepository(EmployeeRepository::class);
        $pracownicy = $pracownikRepo->searchByName($query);
        
        $this->jsonResponse($pracownicy);
    }
}

FetchWorkersHandler::run();
