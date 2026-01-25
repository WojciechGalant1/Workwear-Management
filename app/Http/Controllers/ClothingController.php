<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

class ClothingController extends BaseController {
    
    public function history(): array {
        $issuedClothingRepo = $this->getRepository('IssuedClothingRepository');
        
        return [
            'data' => $issuedClothingRepo->getWydaneUbraniaWithDetails(),
            'pageTitle' => 'history_clothing_title'
        ];
    }
}
