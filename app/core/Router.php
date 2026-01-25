<?php
declare(strict_types=1);
namespace App\Core;

use App\Helpers\UrlHelper;
use App\Auth\AccessGuard;

class Router {
    private array $routes = [];
    /** @var callable|null */
    private $notFoundCallback = null;

    public function add(string $path, array|string $routeConfig): void {
        $this->routes[$path] = $routeConfig;
    }

    public function setNotFound(callable $callback): void {
        $this->notFoundCallback = $callback;
    }

    /**
     * Dispatches the route based on the current URI
     * @return mixed Returns true on success, result of callback, or false on failure
     */
    public function dispatch() {
        $uri = UrlHelper::getCleanUri();
        
        if (isset($this->routes[$uri])) {
            $route = $this->routes[$uri];
            
            if (is_array($route)) {
                // Middleware - Auth check (PRZED kontrolerem)
                if (isset($route['auth'])) {
                    $guard = new AccessGuard();
                    $guard->requireStatus($route['auth']);
                }
                
                // Wykonanie kontrolera (użytkownik jest już zweryfikowany)
                if (isset($route['controller']) && isset($route['action'])) {
                    $controllerClass = 'App\\Http\\Controllers\\' . $route['controller'];
                    if (!class_exists($controllerClass)) {
                        throw new \Exception("Controller {$controllerClass} not found");
                    }
                    $controller = new $controllerClass();
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
                    throw new \Exception("View file not found: " . ($route['view'] ?? 'unknown'));
                }
            }
            
            return false;
        } else {
            if (is_callable($this->notFoundCallback)) {
                return call_user_func($this->notFoundCallback);
            }
            
            header("HTTP/1.0 404 Not Found");
            echo "Page not found";
            return false;
        }
    }
} 
