<?php
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

class EmployeeController extends BaseController {
    
    public function list(): array {
        $employeeRepo = $this->getRepository('EmployeeRepository');
        
        return [
            'pracownicy' => $employeeRepo->getAll(),
            'pageTitle' => 'employee_title'
        ];
    }
    
    public function create(): array {
        return [
            'pageTitle' => 'employee_add_title'
        ];
    }
}
