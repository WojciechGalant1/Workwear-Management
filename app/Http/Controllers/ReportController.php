<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

class ReportController extends BaseController {
    
    public function index(): array {
        $issuedClothingRepo = $this->getRepository('IssuedClothingRepository');
        
        return [
            'expiringClothing' => $issuedClothingRepo->getExpiringClothingWithEmployeeDetails(),
            'ubraniaPoTerminie' => $issuedClothingRepo->getUbraniaPoTerminie(),
            'pageTitle' => 'reports_issue_title'
        ];
    }
}
