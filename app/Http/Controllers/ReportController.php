<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Repositories\IssuedClothingRepository;

class ReportController extends BaseController {
    
    public function index(): array {
        $issuedClothingRepo = $this->getRepository(IssuedClothingRepository::class);
        
        return [
            'expiringClothing' => $issuedClothingRepo->getExpiringClothingWithEmployeeDetails(),
            'ubraniaPoTerminie' => $issuedClothingRepo->getUbraniaPoTerminie(),
            'pageTitle' => 'reports_issue_title'
        ];
    }
}
