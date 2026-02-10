<?php
declare(strict_types=1);
namespace App\Http\Handlers\Employee;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;
use App\Entities\Employee;
use App\Exceptions\ValidationException;
use App\Exceptions\AuthorizationException;
use App\Repositories\EmployeeRepository;

class AddEmployeeHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle(): void {
        $this->throttle('modify:add_employee', 30, 60);
        
        if (!$this->isPost()) {
            throw new ValidationException('error_general');
        }
        
        if (!$this->validateCsrf()) {
            throw new AuthorizationException('error_csrf');
        }
        
        $imie = trim($_POST['imie'] ?? '');
        $nazwisko = trim($_POST['nazwisko'] ?? '');
        $stanowisko = trim($_POST['stanowisko'] ?? '');
        $status = 1;
        
        if (empty($imie) || empty($nazwisko) || empty($stanowisko)) {
            throw new ValidationException('employee_required_fields');
        }
        
        // Strict validation: Names should contain only letters, spaces, and hyphens
        if (!preg_match('/^[\p{L}\s-]+$/u', $imie) || !preg_match('/^[\p{L}\s-]+$/u', $nazwisko)) {
            throw new ValidationException('validation_name_invalid_characters');
        }
        
        $pracownik = new Employee($imie, $nazwisko, $stanowisko, $status);
        $pracownikRepo = $this->getRepository(EmployeeRepository::class);
        
        if ($pracownikRepo->create($pracownik)) {
            $this->successResponse('employee_add_success');
        } else {
            throw new \Exception('Failed to create employee');
        }
    }
}

AddEmployeeHandler::run();
