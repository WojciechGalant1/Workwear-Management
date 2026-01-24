<?php
namespace App\Http\Handlers\Employee;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;

class FetchWorkersHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        $query = $_GET['query'] ?? '';
        
        $pracownikRepo = $this->getRepository('EmployeeRepository');
        $pracownicy = $pracownikRepo->searchByName($query);
        
        $this->jsonResponse($pracownicy);
    }
}

FetchWorkersHandler::run();
