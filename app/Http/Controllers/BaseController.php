<?php
require_once __DIR__ . '/../../core/ServiceContainer.php';

abstract class BaseController {
    protected ServiceContainer $serviceContainer;
    
    public function __construct() {
        $this->serviceContainer = ServiceContainer::getInstance();
    }
    
    /**
     * Pobiera repozytorium z ServiceContainer
     * @param string $name Nazwa repozytorium
     * @return mixed
     */
    protected function getRepository($name) {
        return $this->serviceContainer->getRepository($name);
    }
    
    /**
     * Pobiera serwis z ServiceContainer
     * @param string $name Nazwa serwisu
     * @return mixed
     */
    protected function getService($name) {
        return $this->serviceContainer->getService($name);
    }
}
