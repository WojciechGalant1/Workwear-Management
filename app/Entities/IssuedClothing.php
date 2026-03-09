<?php
declare(strict_types=1);
namespace App\Entities;

class IssuedClothing {
    private ?int $id = null;
    private int $id_wydania;
    private int $id_ubrania;
    private int $id_rozmiaru;
    private int $ilosc;
    private \DateTime $data_waznosci;
    private int $status;

    public function __construct(\DateTime $data_waznosci, int $id_wydania, int $id_ubrania, int $id_rozmiaru, int $ilosc, int $status = 1) {
        if ($id_wydania <= 0) {
            throw new \InvalidArgumentException('ID wydania musi być większe od 0.');
        }
        if ($id_ubrania <= 0) {
            throw new \InvalidArgumentException('ID ubrania musi być większe od 0.');
        }
        if ($id_rozmiaru <= 0) {
            throw new \InvalidArgumentException('ID rozmiaru musi być większe od 0.');
        }
        if ($ilosc <= 0) {
            throw new \InvalidArgumentException('Ilość musi być większa od 0.');
        }
        if (!in_array($status, [0, 1, 2, 3], true)) {
            throw new \InvalidArgumentException('Status musi być wartością 0, 1, 2 lub 3.');
        }
        $this->data_waznosci = $data_waznosci;
        $this->id_wydania = $id_wydania;
        $this->id_ubrania = $id_ubrania;
        $this->id_rozmiaru = $id_rozmiaru;
        $this->ilosc = $ilosc;
        $this->status = $status;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getIdWydania(): int {
        return $this->id_wydania;
    }

    public function getIdUbrania(): int {
        return $this->id_ubrania;
    }

    public function getIdRozmiaru(): int {
        return $this->id_rozmiaru;
    }

    public function getIlosc(): int {
        return $this->ilosc;
    }

    public function getDataWaznosci(): \DateTime {
        return $this->data_waznosci;
    }

    public function getStatus(): int {
        return $this->status;
    }
}