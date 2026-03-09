<?php
declare(strict_types=1);
namespace App\Entities;

class Issue {
    private ?int $id_wydania = null;
    private int $user_id;
    private int $pracownik_id;
    private \DateTime $data_wydania;
    private string $uwagi;

    public function __construct(int $user_id, int $pracownik_id, \DateTime $data_wydania, string $uwagi = '') {
        if ($user_id <= 0) {
            throw new \InvalidArgumentException('ID użytkownika musi być większe od 0.');
        }
        if ($pracownik_id <= 0) {
            throw new \InvalidArgumentException('ID pracownika musi być większe od 0.');
        }
        $this->user_id = $user_id;
        $this->pracownik_id = $pracownik_id;
        $this->data_wydania = $data_wydania;
        $this->uwagi = $uwagi;
    }

    public function getIdWydania(): ?int {
        return $this->id_wydania;
    }

    public function getIdPracownik(): int {
        return $this->pracownik_id;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function getDataWydania(): \DateTime {
        return $this->data_wydania;
    }

    public function getUwagi(): string {
        return $this->uwagi;
    }
}
