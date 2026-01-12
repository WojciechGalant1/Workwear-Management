<?php
require_once __DIR__ . '/../helpers/UrlHelper.php';

class Router {
    private $routes = array();
    private $notFoundCallback;

    public function add($path, $viewFile) {
        $this->routes[$path] = $viewFile;
    }

    public function setNotFound($callback) {
        $this->notFoundCallback = $callback;
    }

    public function dispatch($uri) {
        $uri = UrlHelper::getCleanUri();
        
        if (isset($this->routes[$uri])) {
            $viewFile = $this->routes[$uri];
            
            if (file_exists($viewFile)) {
                include_once $viewFile;
                return true;
            } else {
                throw new Exception("View file not found: $viewFile");
            }
        } else {
            if (is_callable($this->notFoundCallback)) {
                return call_user_func($this->notFoundCallback);
            }
            
            header("HTTP/1.0 404 Not Found");
            echo "Page not found";
        }
    }
} 
