<?php
include_once __DIR__ . '/BaseRepository.php';
include_once __DIR__ . '/../models/OrderHistory.php';
include_once __DIR__ . '/ClothingRepository.php';
include_once __DIR__ . '/SizeRepository.php';
include_once __DIR__ . '/WarehouseRepository.php';

class OrderHistoryRepository extends BaseRepository {

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }

    public function create(OrderHistory $zamowienie) {
        $stmt = $this->pdo->prepare("INSERT INTO historia_zamowien (data_zamowienia, user_id, uwagi, status) VALUES (:data_zamowienia, :user_id, :uwagi, :status)");
        $data_zamowienia = $zamowienie->getDataZamowienia()->format('Y-m-d H:i:s');
        $stmt->bindValue(':data_zamowienia', $data_zamowienia);
        $stmt->bindValue(':user_id', $zamowienie->getUserId());
        $stmt->bindValue(':uwagi', $zamowienie->getUwagi());
        $stmt->bindValue(':status', $zamowienie->getStatus());
        return $stmt->execute();
    }
    
    public function getLastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT h.id, h.data_zamowienia, h.user_id, h.uwagi, h.status, s.id AS szczegol_id, s.zamowienie_id, s.id_ubrania, s.id_rozmiaru, s.ilosc, s.firma, 
                             u.nazwa_ubrania AS nazwa_ubrania, r.nazwa_rozmiaru AS rozmiar_ubrania, k.kod_nazwa AS kod, uz.nazwa AS nazwa_uzytkownika  
                             FROM historia_zamowien h 
                             JOIN szczegoly_zamowienia s ON h.id = s.zamowienie_id 
                             JOIN ubranie u ON s.id_ubrania = u.id_ubranie 
                             JOIN rozmiar r ON s.id_rozmiaru = r.id_rozmiar 
                             LEFT JOIN kod k ON s.sz_kodID = k.id_kod 
                             LEFT JOIN uzytkownicy uz ON h.user_id = uz.id
                             ORDER BY h.data_zamowienia DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dodajDoMagazynu(OrderHistory $zamowienie) {
        $szczegolyZamowieniaC = new OrderDetailsRepository($this->pdo);
        $szczegoly = $szczegolyZamowieniaC->getByZamowienieId($zamowienie->getId());

        foreach ($szczegoly as $szczegolData) {
            $ubranieC = new ClothingRepository($this->pdo);
            $rozmiarC = new SizeRepository($this->pdo);
            $stanMagazynuC = new WarehouseRepository($this->pdo);

            $idUbrania = $szczegolData['id_ubrania'];
            $idRozmiaru = $szczegolData['id_rozmiaru'];
            $ilosc = $szczegolData['ilosc'];
            $iloscMin = $szczegolData['iloscMin'];

            $stanMagazynu = new Warehouse($idUbrania, $idRozmiaru, $ilosc, $iloscMin);
            if (!$stanMagazynuC->create($stanMagazynu)) {
                return false;
            }
        }
        return true;
    }
}

