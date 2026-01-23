<?php
class Warehouse
{
    private ?int $id = null;
    private ?int $id_ubrania = null;
    private ?int $id_rozmiaru = null;
    private int $ilosc = 0;
    private int $iloscMin = 0;

    public function __construct(?int $id_ubrania = null, ?int $id_rozmiaru = null, int $ilosc = 0, int $iloscMin = 0) {
        $this->id_ubrania = $id_ubrania;
        $this->id_rozmiaru = $id_rozmiaru;
        $this->ilosc = $ilosc;
        $this->iloscMin = $iloscMin;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUbrania(): ?int
    {
        return $this->id_ubrania;
    }

    public function setIdUbrania(?int $id_ubrania): void
    {
        $this->id_ubrania = $id_ubrania;
    }

    public function getIdRozmiaru(): ?int
    {
        return $this->id_rozmiaru;
    }

    public function setIdRozmiaru(?int $id_rozmiaru): void
    {
        $this->id_rozmiaru = $id_rozmiaru;
    }

    public function getIlosc(): int
    {
        return $this->ilosc;
    }

    public function setIlosc(int $ilosc): void
    {
        $this->ilosc = $ilosc;
    }

    public function getIloscMin(): int
    {
        return $this->iloscMin;
    }

    public function setIloscMin(int $iloscMin): void
    {
        $this->iloscMin = $iloscMin;
    }
}

