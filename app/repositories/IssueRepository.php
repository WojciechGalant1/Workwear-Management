<?php
include_once __DIR__ . '/BaseRepository.php';
include_once __DIR__ . '/../entities/Issue.php';

class IssueRepository extends BaseRepository {

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }
    
    public function create(Issue $wydania) {
        $stmt = $this->pdo->prepare("INSERT INTO wydania (pracownik_id, user_id, data_wydania) VALUES (:pracownik_id, :user_id, :data_wydania)");
        $stmt->bindValue(':pracownik_id', $wydania->getIdPracownik());
        $stmt->bindValue(':user_id', $wydania->getUserId());
        $stmt->bindValue(':data_wydania', $wydania->getDataWydania()->format('Y-m-d H:i:s'));
        
        try {
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw $e;
        } 
    }

    public function deleteWydanie($id_wydania) {
        $stmt = $this->pdo->prepare("DELETE FROM wydania WHERE id_wydania = :id_wydania");
        $stmt->bindValue(':id_wydania', $id_wydania, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
}