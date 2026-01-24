<?php
require_once __DIR__ . '/BaseController.php';

class ClothingController extends BaseController {
    
    public function history(): array {
        $issuedClothingRepo = $this->getRepository('IssuedClothingRepository');
        
        return [
            'data' => $issuedClothingRepo->getWydaneUbraniaWithDetails(),
            'pageTitle' => 'history_clothing_title'
        ];
    }
}
