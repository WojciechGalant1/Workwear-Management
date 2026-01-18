<?php
require_once __DIR__ . '/../BaseHandler.php';

class FetchWorkersHandler extends BaseHandler {
    protected $requireSession = false;
    protected $requireLocalization = false;
    
    public function handle() {
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        
        $pracownikRepo = $this->getRepository('EmployeeRepository');
        $pracownicy = $pracownikRepo->searchByName($query);
        
        $this->jsonResponse($pracownicy);
    }
}

FetchWorkersHandler::run();
