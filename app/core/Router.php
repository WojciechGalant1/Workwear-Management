<?php
require_once __DIR__ . '/../helpers/UrlHelper.php';

class Router {
    private array $routes = [];
    private $notFoundCallback = null;

    public function add($path, $routeConfig) {
        $this->routes[$path] = $routeConfig;
    }

    public function setNotFound($callback) {
        $this->notFoundCallback = $callback;
    }

    public function dispatch() {
        $uri = UrlHelper::getCleanUri();
        
        if (isset($this->routes[$uri])) {
            $route = $this->routes[$uri];
            
            if (is_array($route)) {
                // Middleware - Auth check (PRZED kontrolerem)
                if (isset($route['auth'])) {
                    require_once __DIR__ . '/../auth/AccessGuard.php';
                    $guard = new AccessGuard();
                    $guard->requireStatus($route['auth']);
                }
                
                // Wykonanie kontrolera (użytkownik jest już zweryfikowany)
                if (isset($route['controller']) && isset($route['action'])) {
                    require_once __DIR__ . '/../Http/Controllers/' . $route['controller'] . '.php';
                    $controller = new $route['controller']();
                    $controllerResult = $controller->{$route['action']}();
                    
                    // Przekazanie danych do widoku
                    if (isset($controllerResult) && is_array($controllerResult)) {
                        extract($controllerResult, EXTR_SKIP);  // EXTR_SKIP zapobiega nadpisywaniu istniejących zmiennych
                    }
                }
                
                // Renderowanie widoku
                if (isset($route['view']) && file_exists($route['view'])) {
                    include_once $route['view'];
                    return true;
                } else {
                    throw new Exception("View file not found: " . (isset($route['view']) ? $route['view'] : 'unknown'));
                }
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
