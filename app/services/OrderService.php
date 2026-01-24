<?php
require_once __DIR__ . '/../entities/OrderHistory.php';
require_once __DIR__ . '/../entities/OrderDetails.php';
require_once __DIR__ . '/../entities/Clothing.php';
require_once __DIR__ . '/../entities/Size.php';
require_once __DIR__ . '/../entities/Code.php';
require_once __DIR__ . '/../entities/Warehouse.php';
require_once __DIR__ . '/../helpers/LocalizationHelper.php';

/**
 * Serwis obsługujący logikę biznesową zamówień
 */
class OrderService {
    private ServiceContainer $serviceContainer;
    private OrderHistoryRepository $orderHistoryRepo;
    private OrderDetailsRepository $orderDetailsRepo;
    private ClothingRepository $clothingRepo;
    private SizeRepository $sizeRepo;
    private CodeRepository $codeRepo;
    private WarehouseRepository $warehouseRepo;
    private WarehouseService $warehouseService;
    
    public function __construct(ServiceContainer $serviceContainer) {
        $this->serviceContainer = $serviceContainer;
        $this->orderHistoryRepo = $this->serviceContainer->getRepository('OrderHistoryRepository');
        $this->orderDetailsRepo = $this->serviceContainer->getRepository('OrderDetailsRepository');
        $this->clothingRepo = $this->serviceContainer->getRepository('ClothingRepository');
        $this->sizeRepo = $this->serviceContainer->getRepository('SizeRepository');
        $this->codeRepo = $this->serviceContainer->getRepository('CodeRepository');
        $this->warehouseRepo = $this->serviceContainer->getRepository('WarehouseRepository');
        $this->warehouseService = $this->serviceContainer->getService('WarehouseService');
    }
    
    /**
     * Tworzy nowe zamówienie z wszystkimi szczegółami
     * 
     * @param int $userId ID użytkownika tworzącego zamówienie
     * @param array $ubrania Tablica z danymi ubrań:
     *   - nazwa (string)
     *   - rozmiar (string)
     *   - firma (string)
     *   - ilosc (int)
     *   - iloscMin (int)
     *   - kod (string)
     * @param string $uwagi Opcjonalne uwagi
     * @param int $status Status zamówienia (1 = aktywne, 2 = zmiana magazynu)
     * @return int ID utworzonego zamówienia
     * @throws Exception Gdy walidacja nie przejdzie lub wystąpi błąd
     */
    public function createOrder(int $userId, array $ubrania, string $uwagi = '', int $status = 1): int {
        // Walidacja danych
        if (empty($ubrania) || !is_array($ubrania)) {
            throw new Exception(LocalizationHelper::translate('order_no_items'));
        }
        
        // Walidacja użytkownika
        $userRepo = $this->serviceContainer->getRepository('UserRepository');
        $user = $userRepo->getUserById($userId);
        if (!$user) {
            throw new Exception(LocalizationHelper::translate('error_user_not_found'));
        }
        
        // Utwórz zamówienie
        $dataZamowienia = new DateTime();
        $zamowienie = new OrderHistory($dataZamowienia, $userId, $uwagi, $status);
        
        if (!$this->orderHistoryRepo->create($zamowienie)) {
            throw new Exception(LocalizationHelper::translate('order_create_error'));
        }
        
        $zamowienieId = $this->orderHistoryRepo->getLastInsertId();
        $zamowienie->setId($zamowienieId);
        
        // Utwórz szczegóły zamówienia
        $this->createOrderDetails($zamowienieId, $ubrania, $status);
        
        // Jeśli status = 1 (aktywne), dodaj do magazynu
        if ($status == 1) {
            $this->addOrderToWarehouse($zamowienie);
        }
        
        return $zamowienieId;
    }
    
    /**
     * Tworzy szczegóły zamówienia
     * 
     * @param int $zamowienieId
     * @param array $ubrania
     * @param int $status
     * @throws Exception
     */
    private function createOrderDetails(int $zamowienieId, array $ubrania, int $status): void {
        foreach ($ubrania as $ubranie) {
            $nazwa = trim($ubranie['nazwa'] ?? '');
            $rozmiar = trim($ubranie['rozmiar'] ?? '');
            $firma = trim($ubranie['firma'] ?? '');
            $ilosc = intval($ubranie['ilosc'] ?? 0);
            $iloscMin = intval($ubranie['iloscMin'] ?? 0);
            $kodNazwa = trim($ubranie['kod'] ?? '');
            
            // Walidacja
            if (empty($nazwa) || empty($rozmiar) || empty($firma) || $ilosc <= 0) {
                throw new Exception(LocalizationHelper::translate('order_required_fields'));
            }
            
            // Znajdź lub utwórz ubranie
            $idUbrania = $this->clothingRepo->firstOrCreate(new Clothing($nazwa));
            
            // Znajdź lub utwórz rozmiar
            $idRozmiaru = $this->sizeRepo->firstOrCreate(new Size($rozmiar));
            
            // Znajdź lub utwórz kod
            $kod = $this->codeRepo->findKodByNazwa($kodNazwa);
            if (!$kod) {
                $nowyKod = new Code($kodNazwa, $idUbrania, $idRozmiaru, $status);
                $kodId = $this->codeRepo->create($nowyKod);
            } else {
                // findKodByNazwa zwraca obiekt Code
                $kodId = $kod->getIdKod();
            }
            
            // Utwórz szczegół zamówienia
            $szczegol = new OrderDetails($zamowienieId, $idUbrania, $idRozmiaru, $ilosc, $iloscMin, $firma, $kodId);
            
            if (!$this->orderDetailsRepo->create($szczegol)) {
                throw new Exception(LocalizationHelper::translate('order_details_error'));
            }
        }
    }
    
    /**
     * Dodaje zamówienie do magazynu
     * (logika biznesowa przeniesiona z OrderHistoryRepository::dodajDoMagazynu())
     * 
     * @param OrderHistory $zamowienie
     * @throws Exception
     */
    public function addOrderToWarehouse(OrderHistory $zamowienie): void {
        $szczegoly = $this->orderDetailsRepo->getByZamowienieId($zamowienie->getId());
        
        if (empty($szczegoly)) {
            throw new Exception(LocalizationHelper::translate('order_no_items'));
        }
        
        foreach ($szczegoly as $szczegolData) {
            $idUbrania = $szczegolData['id_ubrania'];
            $idRozmiaru = $szczegolData['id_rozmiaru'];
            $ilosc = $szczegolData['ilosc'];
            $iloscMin = $szczegolData['iloscMin'];
            
            $stanMagazynu = new Warehouse($idUbrania, $idRozmiaru, $ilosc, $iloscMin);
            
            if (!$this->warehouseService->addToWarehouse($stanMagazynu)) {
                throw new Exception(LocalizationHelper::translate('warehouse_update_error'));
            }
        }
    }
}