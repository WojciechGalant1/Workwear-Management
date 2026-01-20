<?php
require_once __DIR__ . '/AccessLevels.php';

class RouteConfig {
    
    private static $pageMap = null;
    private static $urlMap = null;
    
    public static function getRoutes() {
        return array(
            '/' => array(
                'controller' => 'IssueController',
                'action' => 'issue',
                'view' => './views/issue_clothing.php',
                'auth' => AccessLevels::USER
            ),
            '/issue-clothing' => array(
                'controller' => 'IssueController',
                'action' => 'issue',
                'view' => './views/issue_clothing.php',
                'auth' => AccessLevels::USER
            ),
            '/order-history' => array(
                'controller' => 'OrderController',
                'action' => 'history',
                'view' => './views/order_history.php',
                'auth' => AccessLevels::WAREHOUSE
            ),
            '/clothing-history' => array(
                'controller' => 'ClothingController',
                'action' => 'history',
                'view' => './views/clothing_history.php',
                'auth' => AccessLevels::ADMIN
            ),
            '/issue-history' => array(
                'controller' => 'IssueController',
                'action' => 'history',
                'view' => './views/issue_history.php',
                'auth' => AccessLevels::SUPERVISOR
            ),
            '/employees' => array(
                'controller' => 'EmployeeController',
                'action' => 'list',
                'view' => './views/employee_list.php',
                'auth' => AccessLevels::SUPERVISOR
            ),
            '/warehouse' => array(
                'controller' => 'WarehouseController',
                'action' => 'list',
                'view' => './views/warehouse_list.php',
                'auth' => AccessLevels::WAREHOUSE
            ),
            '/add-order' => array(
                'controller' => 'OrderController',
                'action' => 'create',
                'view' => './views/add_order.php',
                'auth' => AccessLevels::WAREHOUSE
            ),
            '/report' => array(
                'controller' => 'ReportController',
                'action' => 'index',
                'view' => './views/raport.php',
                'auth' => AccessLevels::SUPERVISOR
            ),
            '/add-employee' => array(
                'controller' => 'EmployeeController',
                'action' => 'create',
                'view' => './views/add_employee.php',
                'auth' => AccessLevels::SUPERVISOR
            ),
            '/login' => array(
                'controller' => 'AuthController',
                'action' => 'login',
                'view' => './views/auth/login.php'
            )
        );
    }
    
    public static function getPageMap() {
        if (self::$pageMap === null) {
            self::$pageMap = self::buildPageMap();
        }
        return self::$pageMap;
    }
    
    public static function getUrlMap() {
        if (self::$urlMap === null) {
            self::$urlMap = self::buildUrlMap();
        }
        return self::$urlMap;
    }
    
    private static function buildPageMap() {
        $map = array();
        foreach (self::getRoutes() as $uri => $route) {
            $view = is_array($route) ? $route['view'] : $route;
            $map[$uri] = basename($view);
        }
        return $map;
    }
    
    private static function buildUrlMap() {
        $map = array();
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
    
    public static function getPageFromUri($uri) {
        $pageMap = self::getPageMap();
        return isset($pageMap[$uri]) ? $pageMap[$uri] : basename($_SERVER['PHP_SELF']);
    }
    
    public static function getUrlFromPage($fileName) {
        $urlMap = self::getUrlMap();
        return isset($urlMap[$fileName]) ? $urlMap[$fileName] : $fileName;
    }
}
