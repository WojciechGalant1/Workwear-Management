<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Entities\Clothing;
use App\Repositories\BaseRepository;

use PDO;

class ClothingRepository extends BaseRepository {

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }

    public function create(Clothing $ubranie): int {
        $stmt = $this->pdo->prepare("INSERT INTO ubranie (nazwa_ubrania) VALUES (:nazwa_ubrania)");
        
        $nazwa_ubrania = $ubranie->getNazwaUbrania();
        $stmt->bindParam(':nazwa_ubrania', $nazwa_ubrania);
        
        $stmt->execute();
        return (int) $this->pdo->lastInsertId(); 
    }

    public function firstOrCreate(Clothing $ubranie): int {
        $existing = $this->findByName($ubranie->getNazwaUbrania());
        if ($existing) {
            return $existing->getIdUbranie();
        }
        return $this->create($ubranie);
    }

    public function findByName(string $nazwa): ?Clothing {
        $stmt = $this->pdo->prepare("SELECT * FROM ubranie WHERE nazwa_ubrania = :nazwa_ubrania");
        $stmt->bindParam(':nazwa_ubrania', $nazwa);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $ubranie = new Clothing($result['nazwa_ubrania']);
            $ubranie->setIdUbranie($result['id_ubranie']);
            return $ubranie;
        }
        return null;
    }

    public function searchByName(string $query): array {
        $stmt = $this->pdo->prepare('SELECT nazwa_ubrania AS nazwa FROM ubranie WHERE nazwa_ubrania LIKE :query LIMIT 10');
        $searchQuery = "%$query%";
        $stmt->bindParam(':query', $searchQuery);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUnique(): array {
        $stmt = $this->pdo->query("SELECT DISTINCT u.id_ubranie AS id, u.nazwa_ubrania AS nazwa FROM ubranie u JOIN stan_magazynu sm ON u.id_ubranie = sm.id_ubrania");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRozmiaryByUbranieId(int $ubranieId): array {
        $stmt = $this->pdo->prepare("SELECT r.id_rozmiar AS id, r.nazwa_rozmiaru AS rozmiar, sm.ilosc AS ilosc 
            FROM rozmiar r INNER JOIN stan_magazynu sm ON r.id_rozmiar = sm.id_rozmiaru WHERE sm.id_ubrania = :ubranieId");
        $stmt->bindParam(':ubranieId', $ubranieId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        
}

