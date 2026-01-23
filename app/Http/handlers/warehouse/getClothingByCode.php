<?php
require_once __DIR__ . '/../../BaseHandler.php';

class GetClothingByCodeHandler extends BaseHandler {
    protected $requiredStatus = AccessLevels::USER;
    
    public function handle() {
        if (!isset($_GET['kod'])) {
            $this->jsonResponse(['error' => $this->translate('validation_required')]);
            return;
        }
        
        $kodRepo = $this->getRepository('CodeRepository');
        $kodData = $kodRepo->findByNazwa($_GET['kod']);
        
        if ($kodData) {
            $this->jsonResponse([
                'id_ubrania' => $kodData['id_ubrania'],
                'nazwa_ubrania' => $kodData['nazwa_ubrania'],
                'id_rozmiar' => $kodData['id_rozmiar'],
                'nazwa_rozmiaru' => $kodData['nazwa_rozmiaru']
            ]);
        } else {
            $this->jsonResponse(['error' => $this->translate('clothing_code_not_found')]);
        }
    }
}

GetClothingByCodeHandler::run();
