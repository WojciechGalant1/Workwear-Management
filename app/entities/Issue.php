<?php

class Issue {
    private $id_wydania;
    private $pracownik_id;
    private $user_id;
    private $data_wydania;
    private $uwagi;

    public function __construct($user_id, $pracownik_id = null, $data_wydania = null, $uwagi = null) {
        $this->pracownik_id = $pracownik_id;
        $this->user_id = $user_id;
        $this->data_wydania = $data_wydania;
        $this->uwagi = $uwagi;
    }

    public function getIdWydania() {
        return $this->id_wydania;
    }

    public function getIdPracownik() {
        return $this->pracownik_id;
    }

    public function setIdPracownik($pracownik_id) {
        $this->pracownik_id = $pracownik_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setIdUser( $user_id) {
        $this->user_id = $user_id;
    }

    public function getDataWydania() {
        return $this->data_wydania;
    }

    public function setDataWydania( $data_wydania) {
        $this->data_wydania = $data_wydania;
    }

    public function getUwagi() {
        return $this->uwagi;
    }

    public function setUwagi( $uwagi) {
        $this->uwagi = $uwagi;
    }
}
?>

