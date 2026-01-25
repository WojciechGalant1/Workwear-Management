<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Entities\Issue;
use App\Repositories\BaseRepository;

use PDO;

class IssueRepository extends BaseRepository {

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }
    
    public function create(Issue $wydania): string|false {
        $stmt = $this->pdo->prepare("INSERT INTO wydania (pracownik_id, user_id, data_wydania) VALUES (:pracownik_id, :user_id, :data_wydania)");
        $stmt->bindValue(':pracownik_id', $wydania->getIdPracownik());
        $stmt->bindValue(':user_id', $wydania->getUserId());
        $dataWydania = $wydania->getDataWydania();
        $stmt->bindValue(':data_wydania', $dataWydania ? $dataWydania->format('Y-m-d H:i:s') : null);
        
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function deleteWydanie(int $id_wydania): bool {
        $stmt = $this->pdo->prepare("DELETE FROM wydania WHERE id_wydania = :id_wydania");
        $stmt->bindValue(':id_wydania', $id_wydania, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
}