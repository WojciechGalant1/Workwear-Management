<?php
namespace App\Entities;

class IssuedClothing {
    private ?int $id = null;
    private ?int $id_wydania = null;
    private ?int $id_ubrania = null;
    private ?int $id_rozmiaru = null;
    private int $ilosc = 0;
    private \DateTime $data_waznosci;
    private int $status = 0;

    public function __construct(\DateTime $data_waznosci, ?int $id_wydania = null, ?int $id_ubrania = null, ?int $id_rozmiaru = null, int $ilosc = 0, int $status = 0) {
        $this->id_wydania = $id_wydania;
        $this->id_ubrania = $id_ubrania;
        $this->id_rozmiaru = $id_rozmiaru;
        $this->ilosc = $ilosc;
        $this->data_waznosci = $data_waznosci;
        $this->status = $status;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getIdWydania(): ?int {
        return $this->id_wydania;
    }

    public function setIdWydania(?int $id_wydania): void {
        $this->id_wydania = $id_wydania;
    }

    public function getIdUbrania(): ?int {
        return $this->id_ubrania;
    }

    public function setIdUbrania(?int $id_ubrania): void {
        $this->id_ubrania = $id_ubrania;
    }

    public function getIdRozmiaru(): ?int {
        return $this->id_rozmiaru;
    }

    public function setIdRozmiaru(?int $id_rozmiaru): void {
        $this->id_rozmiaru = $id_rozmiaru;
    }

    public function getIlosc(): int {
        return $this->ilosc;
    }

    public function setIlosc(int $ilosc): void {
        $this->ilosc = $ilosc;
    }

    public function getDataWaznosci(): \DateTime {
        return $this->data_waznosci;
    }

    public function setDataWaznosci(\DateTime $data_waznosci): void {
        $this->data_waznosci = $data_waznosci;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setStatus(int $status): void {
        $this->status = $status;
    }
}