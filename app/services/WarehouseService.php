<?php
require_once __DIR__ . '/../entities/Warehouse.php';
require_once __DIR__ . '/../entities/Clothing.php';
require_once __DIR__ . '/../entities/Size.php';
require_once __DIR__ . '/../helpers/LocalizationHelper.php';

/**
 * Serwis obsługujący logikę biznesową magazynu
 */
class WarehouseService {
    private ServiceContainer $serviceContainer;
    private WarehouseRepository $warehouseRepo;
    private ClothingRepository $clothingRepo;
    private SizeRepository $sizeRepo;
    
    public function __construct($serviceContainer) {
        $this->serviceContainer = $serviceContainer;
        $this->warehouseRepo = $this->serviceContainer->getRepository('WarehouseRepository');
        $this->clothingRepo = $this->serviceContainer->getRepository('ClothingRepository');
        $this->sizeRepo = $this->serviceContainer->getRepository('SizeRepository');
    }
    
    /**
     * Dodaje ubranie do magazynu lub zwiększa ilość jeśli już istnieje
     * 
     * @param Warehouse $stanMagazynu
     * @return bool
     * @throws Exception
     */
    public function addToWarehouse(Warehouse $stanMagazynu) {
        $existingStan = $this->warehouseRepo->findByUbranieAndRozmiar(
            $stanMagazynu->getIdUbrania(),
            $stanMagazynu->getIdRozmiaru()
        );
        
        if ($existingStan) {
            return $this->warehouseRepo->increaseIlosc($existingStan['id'], $stanMagazynu->getIlosc());
        } else {
            return $this->warehouseRepo->insertNew($stanMagazynu);
        }
    }
    
    /**
     * Aktualizuje pozycję w magazynie
     * Jeśli zmieniła się ilość, tworzy wpis w historii zamówień
     * 
     * @param int $id ID pozycji w magazynie
     * @param string $nazwa Nazwa ubrania
     * @param string $rozmiar Rozmiar
     * @param int $ilosc Nowa ilość
     * @param int $iloscMin Nowa minimalna ilość
     * @param string $uwagi Uwagi
     * @param int|null $currentUserId ID użytkownika
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateWarehouseItem($id, $nazwa, $rozmiar, $ilosc, $iloscMin, $uwagi, $currentUserId = null) {
        try {
            $existingUbranie = $this->clothingRepo->findByName($nazwa);
            $idUbrania = $existingUbranie ? $existingUbranie->getIdUbranie() : $this->clothingRepo->create(new Clothing($nazwa));
            
            if (!$this->warehouseRepo->updateUbranieId($id, $idUbrania)) {
                return ['success' => false, 'message' => LocalizationHelper::translate('warehouse_update_clothing_error')];
            }
            
            $existingRozmiar = $this->sizeRepo->findByName($rozmiar);
            $idRozmiaru = $existingRozmiar ? $existingRozmiar->getIdRozmiar() : $this->sizeRepo->create(new Size($rozmiar));
            
            if (!$this->warehouseRepo->updateRozmiarId($id, $idRozmiaru)) {
                return ['success' => false, 'message' => LocalizationHelper::translate('warehouse_update_size_error')];
            }
            
            $oldIlosc = $this->warehouseRepo->getIloscById($id);
            $iloscDiff = $ilosc - $oldIlosc;
            
            if (!$this->warehouseRepo->updateIloscAndMin($id, $ilosc, $iloscMin)) {
                return ['success' => false, 'message' => LocalizationHelper::translate('warehouse_update_error')];
            }
            
            if ($iloscDiff !== 0) {
                $this->createOrderFromWarehouseChange($idUbrania, $idRozmiaru, $iloscDiff, $uwagi, $currentUserId);
            }
            return ['success' => true, 'message' => LocalizationHelper::translate('warehouse_update_success')];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Tworzy zamówienie w historii na podstawie zmiany w magazynie
     * (używane gdy zmienia się ilość w magazynie)
     * 
     * @param int $idUbrania
     * @param int $idRozmiaru
     * @param int $iloscDiff Różnica ilości (może być ujemna)
     * @param string $uwagi
     * @param int|null $currentUserId
     * @throws Exception
     */
    private function createOrderFromWarehouseChange($idUbrania, $idRozmiaru, $iloscDiff, $uwagi, $currentUserId = null) {
        // Używamy repozytoriów bezpośrednio, aby uniknąć cyklicznej zależności z OrderService
        $orderHistoryRepo = $this->serviceContainer->getRepository('OrderHistoryRepository');
        $orderDetailsRepo = $this->serviceContainer->getRepository('OrderDetailsRepository');
        
        $userId = $currentUserId !== null ? $currentUserId : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);
        if (!$userId) {
            throw new Exception(LocalizationHelper::translate('error_user_not_found'));
        }
        
        require_once __DIR__ . '/../entities/OrderHistory.php';
        require_once __DIR__ . '/../entities/OrderDetails.php';
        
        $zamowienie = new OrderHistory(new DateTime(), $userId, $uwagi, 2); // Status 2 = zmiana magazynu
        
        if (!$orderHistoryRepo->create($zamowienie)) {
            throw new Exception("Nie udało się zapisać historii zamówienia.");
        }
        
        $zamowienieId = $orderHistoryRepo->getLastInsertId();
        if (!$zamowienieId) {
            throw new Exception("Nie udało się pobrać ID ostatniego zamówienia.");
        }
        
        $szczegol = new OrderDetails($zamowienieId, $idUbrania, $idRozmiaru, $iloscDiff, 0, "-", 0);
        
        if (!$orderDetailsRepo->create($szczegol)) {
            throw new Exception("Nie udało się zapisać szczegółów zamówienia.");
        }
    }
    
    /**
     * Aktualizuje ilość w magazynie (zwiększa lub zmniejsza)
     * 
     * @param int $idUbrania
     * @param int $idRozmiaru
     * @param int $ilosc Ilość do dodania/odjęcia
     * @param bool $anulowanie Jeśli true, dodaje z powrotem (anulowanie wydania)
     * @return bool
     */
    public function updateIlosc($idUbrania, $idRozmiaru, $ilosc, $anulowanie = false) {
        return $this->warehouseRepo->updateIlosc($idUbrania, $idRozmiaru, $ilosc, $anulowanie);
    }
    
    /**
     * Sprawdza czy są niedobory w magazynie
     * 
     * @return bool
     */
    public function hasShortages() {
        return $this->warehouseRepo->checkIlosc();
    }
}