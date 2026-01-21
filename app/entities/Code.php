<?php

class Code {
    private $id_kod;
    private $kod_nazwa;
    private $ubranieID;
    private $rozmiarID;
    private $status;

    public function __construct($kod_nazwa = null, $ubranieID = null, $rozmiarID = null, $status = null) {
        $this->kod_nazwa = $kod_nazwa;
        $this->ubranieID = $ubranieID;
        $this->rozmiarID = $rozmiarID;
        $this->status = $status;
    }

    public function getIdKod() {
        return $this->id_kod;
    }

    public function setIdKod($id_kod) {
        $this->id_kod = $id_kod;
    }

    public function getNazwaKod() {
        return $this->kod_nazwa;
    }

    public function setNazwaKod($kod_nazwa) {
        $this->kod_nazwa = $kod_nazwa;
    }

    public function getUbranieID() {
        return $this->ubranieID;
    }

    public function setUbranieID($ubranieID) {
        $this->ubranieID = $ubranieID;
    }

    public function getRozmiarID() {
        return $this->rozmiarID;
    }

    public function setRozmiarID($rozmiarID) {
        $this->rozmiarID = $rozmiarID;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
}
?>

