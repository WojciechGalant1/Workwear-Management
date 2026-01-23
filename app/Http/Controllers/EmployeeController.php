<?php
require_once __DIR__ . '/BaseController.php';

class EmployeeController extends BaseController {
    
    public function list() {
        $employeeRepo = $this->getRepository('EmployeeRepository');
        
        return [
            'pracownicy' => $employeeRepo->getAll(),
            'pageTitle' => 'employee_title'
        ];
    }
    
    public function create() {
        return [
            'pageTitle' => 'employee_add_title'
        ];
    }
}
