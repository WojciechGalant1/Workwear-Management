<?php
declare(strict_types=1);
namespace App\Entities;

class Issue {
    private ?int $id_wydania = null;
    private ?int $pracownik_id = null;
    private int $user_id;
    private ?\DateTime $data_wydania = null;
    private ?string $uwagi = null;

    public function __construct(int $user_id, ?int $pracownik_id = null, ?\DateTime $data_wydania = null, ?string $uwagi = null) {
        $this->pracownik_id = $pracownik_id;
        $this->user_id = $user_id;
        $this->data_wydania = $data_wydania;
        $this->uwagi = $uwagi;
    }

    public function getIdWydania(): ?int {
        return $this->id_wydania;
    }

    public function getIdPracownik(): ?int {
        return $this->pracownik_id;
    }

    public function setIdPracownik(?int $pracownik_id): void {
        $this->pracownik_id = $pracownik_id;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function setIdUser(int $user_id): void {
        $this->user_id = $user_id;
    }

    public function getDataWydania(): ?\DateTime {
        return $this->data_wydania;
    }

    public function setDataWydania(?\DateTime $data_wydania): void {
        $this->data_wydania = $data_wydania;
    }

    public function getUwagi(): ?string {
        return $this->uwagi;
    }

    public function setUwagi(?string $uwagi): void {
        $this->uwagi = $uwagi;
    }
}
