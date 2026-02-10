<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Core\ServiceContainer;

abstract class BaseController {
    protected ServiceContainer $serviceContainer;
    
    public function __construct() {
        $this->serviceContainer = ServiceContainer::getInstance();
    }
    
    protected function getRepository(string $name): object {
        return $this->serviceContainer->getRepository($name);
    }
    
    protected function getService(string $name): object {
        return $this->serviceContainer->getService($name);
    }
}
