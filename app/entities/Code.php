<?php
declare(strict_types=1);
namespace App\Entities;

class Code {
    private ?int $id_kod = null;
    private ?string $kod_nazwa = null;
    private ?int $ubranieID = null;
    private ?int $rozmiarID = null;
    private ?int $status = null;

    public function __construct(?string $kod_nazwa = null, ?int $ubranieID = null, ?int $rozmiarID = null, ?int $status = null) {
        $this->kod_nazwa = $kod_nazwa;
        $this->ubranieID = $ubranieID;
        $this->rozmiarID = $rozmiarID;
        $this->status = $status;
    }

    public function getIdKod(): ?int {
        return $this->id_kod;
    }

    public function setIdKod(?int $id_kod): void {
        $this->id_kod = $id_kod;
    }

    public function getNazwaKod(): ?string {
        return $this->kod_nazwa;
    }

    public function setNazwaKod(?string $kod_nazwa): void {
        $this->kod_nazwa = $kod_nazwa;
    }

    public function getUbranieID(): ?int {
        return $this->ubranieID;
    }

    public function setUbranieID(?int $ubranieID): void {
        $this->ubranieID = $ubranieID;
    }

    public function getRozmiarID(): ?int {
        return $this->rozmiarID;
    }

    public function setRozmiarID(?int $rozmiarID): void {
        $this->rozmiarID = $rozmiarID;
    }

    public function getStatus(): ?int {
        return $this->status;
    }

    public function setStatus(?int $status): void {
        $this->status = $status;
    }
}
