<?php
require_once __DIR__ . '/AccessLevels.php';

class RouteConfig {
    
    private static ?array $pageMap = null;
    private static ?array $urlMap = null;
    
    public static function getRoutes(): array {
        return [
            '/' => [
                'controller' => 'IssueController',
                'action' => 'issue',
                'view' => './views/issue_clothing.php',
                'auth' => AccessLevels::USER
            ],
            '/issue-clothing' => [
                'controller' => 'IssueController',
                'action' => 'issue',
                'view' => './views/issue_clothing.php',
                'auth' => AccessLevels::USER
            ],
            '/order-history' => [
                'controller' => 'OrderController',
                'action' => 'history',
                'view' => './views/order_history.php',
                'auth' => AccessLevels::WAREHOUSE
            ],
            '/clothing-history' => [
                'controller' => 'ClothingController',
                'action' => 'history',
                'view' => './views/clothing_history.php',
                'auth' => AccessLevels::ADMIN
            ],
            '/issue-history' => [
                'controller' => 'IssueController',
                'action' => 'history',
                'view' => './views/issue_history.php',
                'auth' => AccessLevels::SUPERVISOR
            ],
            '/employees' => [
                'controller' => 'EmployeeController',
                'action' => 'list',
                'view' => './views/employee_list.php',
                'auth' => AccessLevels::SUPERVISOR
            ],
            '/warehouse' => [
                'controller' => 'WarehouseController',
                'action' => 'list',
                'view' => './views/warehouse_list.php',
                'auth' => AccessLevels::WAREHOUSE
            ],
            '/add-order' => [
                'controller' => 'OrderController',
                'action' => 'create',
                'view' => './views/add_order.php',
                'auth' => AccessLevels::WAREHOUSE
            ],
            '/report' => [
                'controller' => 'ReportController',
                'action' => 'index',
                'view' => './views/raport.php',
                'auth' => AccessLevels::SUPERVISOR
            ],
            '/add-employee' => [
                'controller' => 'EmployeeController',
                'action' => 'create',
                'view' => './views/add_employee.php',
                'auth' => AccessLevels::SUPERVISOR
            ],
            '/login' => [
                'controller' => 'AuthController',
                'action' => 'login',
                'view' => './views/auth/login.php'
            ]
        ];
    }
    
    public static function getPageMap(): array {
        if (self::$pageMap === null) {
            self::$pageMap = self::buildPageMap();
        }
        return self::$pageMap;
    }
    
    public static function getUrlMap(): array {
        if (self::$urlMap === null) {
            self::$urlMap = self::buildUrlMap();
        }
        return self::$urlMap;
    }
    
    private static function buildPageMap(): array {
        $map = [];
        foreach (self::getRoutes() as $uri => $route) {
            $view = is_array($route) ? $route['view'] : $route;
            $map[$uri] = basename($view);
        }
        return $map;
    }
    
    private static function buildUrlMap(): array {
        $map = [];
        foreach (self::getRoutes() as $uri => $route) {
            if ($uri === '/') {
                continue;
            }
            $view = is_array($route) ? $route['view'] : $route;
            $fileName = basename($view);
            if (!isset($map[$fileName])) {
                $map[$fileName] = $uri;
            }
        }
        return $map;
    }
    
    public static function getPageFromUri(string $uri): string {
        $pageMap = self::getPageMap();
        return $pageMap[$uri] ?? basename($_SERVER['PHP_SELF'] ?? 'index.php');
    }
    
    public static function getUrlFromPage(string $fileName): string {
        $urlMap = self::getUrlMap();
        return $urlMap[$fileName] ?? $fileName;
    }
}
