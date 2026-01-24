<?php
require_once __DIR__ . '/../../BaseHandler.php';

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
