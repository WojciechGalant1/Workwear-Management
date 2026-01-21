<?php
class User {
    private $u_id;
    private $name;
    private $u_kod;
    private $status;
    
    public function __construct($name = '', $u_kod = '', $status = '1') {
        $this->name = $name;
        $this->u_kod = $u_kod;
        $this->status = $status;
    }

    public function getIdUser() {
        return $this->u_id;
    }

    public function setIdUser($u_id) {
        $this->u_id = $u_id;
    }
    public function get_name() {
        return $this->name;
    }
    public function set_name($name) {
        $this->name = $name;
    }

    public function get_status() {
        return $this->status;
    }

    public function set_status($status) {
        $this->status = $status;
    }

    public function get_kod() {
        return $this->u_kod;
    }

    public function set_kod($u_kod) {
        $this->u_kod = $u_kod;
    }
}
?>
