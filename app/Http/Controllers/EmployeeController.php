<?php
require_once __DIR__ . '/../../core/ServiceContainer.php';


class EmployeeController {
    
    public function list() {
        $serviceContainer = ServiceContainer::getInstance();
        $employeeRepo = $serviceContainer->getRepository('EmployeeRepository');
        
        return array(
            'pracownicy' => $employeeRepo->getAll(),
            'pageTitle' => 'employee_title'
        );
    }
    
    public function create() {
        // Widok formularza dodawania nie wymaga danych z repozytorium
        return array(
            'pageTitle' => 'employee_add_title'
        );
    }
}
