<?php
declare(strict_types=1);
namespace App\Http\Handlers\Employee;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;
use App\Entities\Employee;

class AddEmployeeHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle(): void {
        if (!$this->isPost()) {
            $this->errorResponse('error_general');
        }
        
        if (!$this->validateCsrf()) {
            $this->errorResponse('error_csrf');
        }
        
        $imie = trim($_POST['imie'] ?? '');
        $nazwisko = trim($_POST['nazwisko'] ?? '');
        $stanowisko = trim($_POST['stanowisko'] ?? '');
        $status = 1;
        
        if (empty($imie) || empty($nazwisko) || empty($stanowisko)) {
            $this->errorResponse('employee_required_fields');
        }
        
        $pracownik = new Employee($imie, $nazwisko, $stanowisko, $status);
        $pracownikRepo = $this->getRepository('EmployeeRepository');
        
        if ($pracownikRepo->create($pracownik)) {
            $this->successResponse('employee_add_success');
        } else {
            $this->errorResponse('error_general');
        }
    }
}

AddEmployeeHandler::run();
