<?php
declare(strict_types=1);
namespace App\Entities;

class OrderHistory {
    private ?int $id = null;
    private \DateTime $data_zamowienia;
    private int $user_id;
    private string $uwagi;
    private int $status;

    public function __construct(\DateTime $data_zamowienia, int $user_id, string $uwagi = '', int $status = 1) {
        if ($user_id <= 0) {
            throw new \InvalidArgumentException('ID użytkownika musi być większe od 0.');
        }
        if (!in_array($status, [1, 2], true)) {
            throw new \InvalidArgumentException('Status zamówienia musi wynosić 1 lub 2.');
        }
        $this->data_zamowienia = $data_zamowienia;
        $this->user_id = $user_id;
        $this->uwagi = $uwagi;
        $this->status = $status;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getDataZamowienia(): \DateTime {
        return $this->data_zamowienia;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function getUwagi(): string {
        return $this->uwagi;
    }

    public function setUwagi(string $uwagi): void {
        $this->uwagi = $uwagi;
    }

    public function getStatus(): int {
        return $this->status;
    }
}
