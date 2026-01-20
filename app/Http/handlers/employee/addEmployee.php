<?php
require_once __DIR__ . '/../../BaseHandler.php';
require_once __DIR__ . '/../../../models/Employee.php';

class AddEmployeeHandler extends BaseHandler {
    protected $requiredStatus = AccessLevels::SUPERVISOR;
    
    public function handle() {
        if (!$this->isPost()) {
            $this->errorResponse('error_general');
        }
        
        if (!$this->validateCsrf()) {
            $this->errorResponse('error_csrf');
        }
        
        $imie = isset($_POST['imie']) ? trim($_POST['imie']) : '';
        $nazwisko = isset($_POST['nazwisko']) ? trim($_POST['nazwisko']) : '';
        $stanowisko = isset($_POST['stanowisko']) ? trim($_POST['stanowisko']) : '';
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
