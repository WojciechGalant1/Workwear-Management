<?php
namespace App\Repositories;

use App\Entities\OrderDetails;
use App\Repositories\BaseRepository;

use PDO;

class OrderDetailsRepository extends BaseRepository {
    
    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }

    public function create(OrderDetails $szczegol): bool {
        $stmt = $this->pdo->prepare("INSERT INTO szczegoly_zamowienia (zamowienie_id, id_ubrania, id_rozmiaru, ilosc, iloscMin, firma, sz_kodID) VALUES (:zamowienie_id, :id_ubrania, :id_rozmiaru, :ilosc, :iloscMin, :firma, :sz_kodID)");
        $stmt->bindValue(':zamowienie_id', $szczegol->getZamowienieId());
        $stmt->bindValue(':id_ubrania', $szczegol->getIdUbrania());
        $stmt->bindValue(':id_rozmiaru', $szczegol->getIdRozmiaru());
        $stmt->bindValue(':ilosc', $szczegol->getIlosc());
        $stmt->bindValue(':iloscMin', $szczegol->getIloscMin());
        $stmt->bindValue(':firma', $szczegol->getFirma());
        $stmt->bindValue(':sz_kodID', $szczegol->getSzKodID());

        return $stmt->execute();
    }

    public function getByZamowienieId(int $zamowienieId): array {
        $stmt = $this->pdo->prepare("SELECT zamowienie_id, id_ubrania, id_rozmiaru, ilosc, iloscMin FROM szczegoly_zamowienia WHERE zamowienie_id = :zamowienie_id");
        $stmt->bindValue(':zamowienie_id', $zamowienieId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
