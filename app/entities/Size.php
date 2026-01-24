<?php
class Size {
    private ?int $id_rozmiar = null;
    private string $nazwa_rozmiaru = '';

    public function __construct(string $nazwa_rozmiaru) {
        $this->nazwa_rozmiaru = strtoupper($nazwa_rozmiaru);
    }

    public function getIdRozmiar(): ?int {
        return $this->id_rozmiar;
    }

    public function setIdRozmiar(?int $id_rozmiar): void {
        $this->id_rozmiar = $id_rozmiar;
    }

    public function getNazwaRozmiaru(): string {
        return $this->nazwa_rozmiaru;
    }

    public function setNazwaRozmiaru(string $nazwa_rozmiaru): void {
        $this->nazwa_rozmiaru = strtoupper($nazwa_rozmiaru);
    }
}