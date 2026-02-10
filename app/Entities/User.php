<?php
declare(strict_types=1);
namespace App\Entities;

class User {
    private ?int $u_id = null;
    private string $name = '';
    private string $u_kod = '';
    private int $status = 1;
    
    public function __construct(string $name = '', string $u_kod = '', int $status = 1) {
        $this->name = $name;
        $this->u_kod = $u_kod;
        $this->status = $status;
    }

    public function getIdUser(): ?int {
        return $this->u_id;
    }

    public function setIdUser(?int $u_id): void {
        $this->u_id = $u_id;
    }
    
    public function get_name(): string {
        return $this->name;
    }
    
    public function set_name(string $name): void {
        $this->name = $name;
    }

    public function get_status(): int {
        return $this->status;
    }

    public function set_status(int $status): void {
        $this->status = $status;
    }

    public function get_kod(): string {
        return $this->u_kod;
    }

    public function set_kod(string $u_kod): void {
        $this->u_kod = $u_kod;
    }
}
