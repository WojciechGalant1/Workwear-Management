<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Repositories\EmployeeRepository;

class EmployeeController extends BaseController {
    
    public function list(): array {
        $employeeRepo = $this->getRepository(EmployeeRepository::class);
        
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
