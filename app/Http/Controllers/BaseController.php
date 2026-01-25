<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Core\ServiceContainer;

abstract class BaseController {
    protected ServiceContainer $serviceContainer;
    
    public function __construct() {
        $this->serviceContainer = ServiceContainer::getInstance();
    }
    
    /**
     * Pobiera repozytorium z ServiceContainer
     * @param string $name Nazwa repozytorium
     * @return object
     */
    protected function getRepository(string $name): object {
        return $this->serviceContainer->getRepository($name);
    }
    
    /**
     * Pobiera serwis z ServiceContainer
     * @param string $name Nazwa serwisu
     * @return object
     */
    protected function getService(string $name): object {
        return $this->serviceContainer->getService($name);
    }
}
