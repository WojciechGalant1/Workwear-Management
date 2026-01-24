<?php
class OrderHistory {
    private ?int $id = null;
    private \DateTime $data_zamowienia;
    private int $user_id;
    private string $uwagi = '';
    private int $status = 0;

    public function __construct(\DateTime $data_zamowienia, int $user_id, string $uwagi = '', int $status = 0) {
        $this->data_zamowienia = $data_zamowienia;
        $this->user_id = $user_id;
        $this->uwagi = $uwagi;
        $this->status = $status;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getDataZamowienia(): \DateTime {
        return $this->data_zamowienia;
    }

    public function setDataZamowienia(\DateTime $data_zamowienia): void {
        $this->data_zamowienia = $data_zamowienia;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void {
        $this->user_id = $user_id;
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

    public function setStatus(int $status): void {
        $this->status = $status;
    }
}
