<?php
class OrderHistory {
    private $id;
    private $data_zamowienia;
    private $user_id;
    private $uwagi;
    private $status;

    public function __construct($data_zamowienia, $user_id, $uwagi = '', $status = 0) {
        $this->data_zamowienia = $data_zamowienia;
        $this->user_id = $user_id;
        $this->uwagi = $uwagi;
        $this->status = $status;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getDataZamowienia() {
        return $this->data_zamowienia;
    }

    public function setDataZamowienia($data_zamowienia) {
        $this->data_zamowienia = $data_zamowienia;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function getUwagi() {
        return $this->uwagi;
    }

    public function setUwagi($uwagi) {
        $this->uwagi = $uwagi;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
}
?>

