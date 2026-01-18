<?php
require_once __DIR__ . '/../BaseHandler.php';
require_once __DIR__ . '/../../models/OrderHistory.php';
require_once __DIR__ . '/../../models/OrderDetails.php';
require_once __DIR__ . '/../../models/Clothing.php';
require_once __DIR__ . '/../../models/Size.php';
require_once __DIR__ . '/../../models/Code.php';

class AddOrderHandler extends BaseHandler {
    
    public function handle() {
        if (!$this->isPost()) {
            $this->errorResponse('error_general');
        }
        
        if (!$this->validateCsrf()) {
            $this->errorResponse('error_csrf');
        }
        
        // Walidacja użytkownika
        $currentUserId = $this->getUserId();
        $userRepo = $this->getRepository('UserRepository');
        $currentUser = $userRepo->getUserById($currentUserId);
        
        if (!$currentUser) {
            $this->errorResponse('error_user_not_found');
        }
        
        $dataZamowieniaObj = new DateTime();
        $status = 1;
        $uwagi = isset($_POST['uwagi']) ? trim($_POST['uwagi']) : '';
        
        $zamowienie = new OrderHistory($dataZamowieniaObj, $currentUserId, $uwagi, $status);
        $zamowienieRepo = $this->getRepository('OrderHistoryRepository');
        $szczegolyZamowieniaRepo = $this->getRepository('OrderDetailsRepository');
        $kodRepo = $this->getRepository('CodeRepository');
        
        if (!$zamowienieRepo->create($zamowienie)) {
            $this->errorResponse('order_create_error');
        }
        
        $zamowienieId = $zamowienieRepo->getLastInsertId();
        $zamowienie->setId($zamowienieId);
        
        $ubrania = isset($_POST['ubrania']) ? $_POST['ubrania'] : array();
        
        if (empty($ubrania) || !is_array($ubrania)) {
            $this->errorResponse('order_no_items');
        }
        
        $ubranieRepo = $this->getRepository('ClothingRepository');
        $rozmiarRepo = $this->getRepository('SizeRepository');
        
        foreach ($ubrania as $ubranie) {
            $nazwa = isset($ubranie['nazwa']) ? trim($ubranie['nazwa']) : '';
            $rozmiar = isset($ubranie['rozmiar']) ? trim($ubranie['rozmiar']) : '';
            $firma = isset($ubranie['firma']) ? trim($ubranie['firma']) : '';
            $ilosc = isset($ubranie['ilosc']) ? intval($ubranie['ilosc']) : 0;
            $iloscMin = isset($ubranie['iloscMin']) ? intval($ubranie['iloscMin']) : 0;
            $kodNazwa = isset($ubranie['kod']) ? trim($ubranie['kod']) : '';
            
            if (empty($nazwa) || empty($rozmiar) || empty($firma) || $ilosc <= 0) {
                $this->errorResponse('order_required_fields');
            }
            
            $idUbrania = $ubranieRepo->firstOrCreate(new Clothing($nazwa));
            $idRozmiaru = $rozmiarRepo->firstOrCreate(new Size($rozmiar));
            
            $kod = $kodRepo->findKodByNazwa($kodNazwa);
            
            if (!$kod) {
                $nowyKod = new Code($kodNazwa, $idUbrania, $idRozmiaru, $status);
                $kodId = $kodRepo->create($nowyKod);
            } else {
                $kodId = $kod->getIdKod();
            }
            
            $szczegol = new OrderDetails($zamowienieId, $idUbrania, $idRozmiaru, $ilosc, $iloscMin, $firma, $kodId);
            
            if (!$szczegolyZamowieniaRepo->create($szczegol)) {
                $this->errorResponse('order_details_error');
            }
        }
        
        if ($status == 1) {
            $zamowienieRepo->dodajDoMagazynu($zamowienie);
        }
        
        $this->successResponse('order_add_success');
    }
}

AddOrderHandler::run();
