<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Entities\Warehouse;
use App\Repositories\BaseRepository;

use PDO;

class WarehouseRepository extends BaseRepository {

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }

    public function insertNew(Warehouse $stanMagazynu): bool {
        $stmt = $this->pdo->prepare("INSERT INTO stan_magazynu (id_ubrania, id_rozmiaru, ilosc, iloscMin) VALUES (:id_ubrania, :id_rozmiaru, :ilosc, :iloscMin)");
        $id_ubrania = $stanMagazynu->getIdUbrania();
        $id_rozmiaru = $stanMagazynu->getIdRozmiaru();
        $ilosc = $stanMagazynu->getIlosc();
        $iloscMin = $stanMagazynu->getIloscMin();
        $stmt->bindParam(':id_ubrania', $id_ubrania);
        $stmt->bindParam(':id_rozmiaru', $id_rozmiaru);
        $stmt->bindParam(':ilosc', $ilosc);
        $stmt->bindParam(':iloscMin', $iloscMin);
        return $stmt->execute();
    }
    
    public function readAll(): array {
        $stmt = $this->pdo->prepare("SELECT s.id, u.nazwa_ubrania, r.nazwa_rozmiaru,s.ilosc, s.iloscMin FROM stan_magazynu s
         JOIN ubranie u ON s.id_ubrania = u.id_ubranie JOIN rozmiar r ON s.id_rozmiaru = r.id_rozmiar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIlosc(?int $id_ubrania, ?int $id_rozmiaru): int {
        $stmt = $this->pdo->prepare("SELECT ilosc FROM stan_magazynu WHERE id_ubrania = :id_ubrania AND id_rozmiaru = :id_rozmiaru");
        $stmt->bindParam(':id_ubrania', $id_ubrania);
        $stmt->bindParam(':id_rozmiaru', $id_rozmiaru);
        $stmt->execute();
        
        return (int)$stmt->fetchColumn();
    }

    public function findByUbranieAndRozmiar(?int $id_ubrania, ?int $id_rozmiaru): ?array {
        $stmt = $this->pdo->prepare("SELECT id FROM stan_magazynu WHERE id_ubrania = :id_ubrania AND id_rozmiaru = :id_rozmiaru");
        $stmt->bindParam(':id_ubrania', $id_ubrania);
        $stmt->bindParam(':id_rozmiaru', $id_rozmiaru);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function findByUbranieAndRozmiarByName(string $nazwaUbrania, string $nazwaRozmiaru): ?array {
        $stmt = $this->pdo->prepare("SELECT id FROM stan_magazynu s JOIN ubranie u ON s.id_ubrania = u.id_ubranie
            JOIN rozmiar r ON s.id_rozmiaru = r.id_rozmiar WHERE u.nazwa_ubrania = :nazwaUbrania AND r.nazwa_rozmiaru = :nazwaRozmiaru");
        $stmt->bindParam(':nazwaUbrania', $nazwaUbrania);
        $stmt->bindParam(':nazwaRozmiaru', $nazwaRozmiaru);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }    

    public function updateIlosc(?int $id_ubrania, ?int $id_rozmiaru, int $ilosc, bool $anulowanie = false): bool {
        $operation = $anulowanie ? "+" : "-";
        $stmt = $this->pdo->prepare("UPDATE stan_magazynu SET ilosc = ilosc $operation :ilosc WHERE id_ubrania = :id_ubrania AND id_rozmiaru = :id_rozmiaru");
        $stmt->bindParam(':id_ubrania', $id_ubrania);
        $stmt->bindParam(':id_rozmiaru', $id_rozmiaru);
        $stmt->bindParam(':ilosc', $ilosc);
        return $stmt->execute();
    }
    
    public function increaseIlosc(int $id, int $ilosc): bool {
        $stmt = $this->pdo->prepare("UPDATE stan_magazynu SET ilosc = ilosc + :ilosc WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ilosc', $ilosc);
        return $stmt->execute();
    }

    public function checkIlosc(): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM stan_magazynu WHERE ilosc < iloscMin");
        $stmt->execute();
        return $stmt->fetchColumn() > 0; 
    }
    
    public function updateUbranieId(int $id, ?int $idUbrania): bool {
        $stmt = $this->pdo->prepare("UPDATE stan_magazynu SET id_ubrania = :idUbrania WHERE id = :id");
        $stmt->bindValue(':idUbrania', $idUbrania, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function updateRozmiarId(int $id, ?int $idRozmiaru): bool {
        $stmt = $this->pdo->prepare("UPDATE stan_magazynu SET id_rozmiaru = :idRozmiaru WHERE id = :id");
        $stmt->bindValue(':idRozmiaru', $idRozmiaru, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    

    public function getIloscById(int $id): int {
        $stmt = $this->pdo->prepare("SELECT ilosc FROM stan_magazynu WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    
    public function updateIloscAndMin(int $id, int $ilosc, int $iloscMin): bool {
        $stmt = $this->pdo->prepare("UPDATE stan_magazynu SET ilosc = :ilosc, iloscMin = :iloscMin WHERE id = :id");
        $stmt->bindValue(':ilosc', $ilosc, PDO::PARAM_INT);
        $stmt->bindValue(':iloscMin', $iloscMin, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

}