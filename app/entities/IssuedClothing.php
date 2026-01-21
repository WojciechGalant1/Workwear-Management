<?php

class IssuedClothing {
    private $id;
    private $id_wydania;
    private $id_ubrania;
    private $id_rozmiaru;
    private $ilosc;
    private $data_waznosci;
    private $status;

    public function __construct($data_waznosci, $id_wydania = null, $id_ubrania = null, $id_rozmiaru = null, $ilosc = 0, $status = 0) {
        $this->id_wydania = $id_wydania;
        $this->id_ubrania = $id_ubrania;
        $this->id_rozmiaru = $id_rozmiaru;
        $this->ilosc = $ilosc;
        $this->data_waznosci = $data_waznosci;
        $this->status = $status;
    }

    public function getId() {
        return $this->id;
    }

    public function getIdWydania() {
        return $this->id_wydania;
    }

    public function setIdWydania($id_wydania) {
        $this->id_wydania = $id_wydania;
    }

    public function getIdUbrania() {
        return $this->id_ubrania;
    }

    public function setIdUbrania($id_ubrania) {
        $this->id_ubrania = $id_ubrania;
    }

    public function getIdRozmiaru() {
        return $this->id_rozmiaru;
    }

    public function setIdRozmiaru($id_rozmiaru) {
        $this->id_rozmiaru = $id_rozmiaru;
    }

    public function getIlosc() {
        return $this->ilosc;
    }

    public function setIlosc($ilosc) {
        $this->ilosc = $ilosc;
    }

    public function getDataWaznosci() {
        return $this->data_waznosci;
    }

    public function setDataWaznosci($data_waznosci) {
        $this->data_waznosci = $data_waznosci;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

}
?>

