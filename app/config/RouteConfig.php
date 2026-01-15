<?php

/**
 * Central configuration for all route mappings in the application
 */
class RouteConfig {
    /**
     * Get routes mapping clean URLs to view files
     */
    public static function getRoutes() {
        return array(
            '/' => array(
                'controller' => 'IssueController',
                'action' => 'issue',
                'view' => './views/issue_clothing.php',
                'auth' => 1
            ),
            '/issue-clothing' => array(
                'controller' => 'IssueController',
                'action' => 'issue',
                'view' => './views/issue_clothing.php',
                'auth' => 1
            ),
            '/order-history' => array(
                'controller' => 'OrderController',
                'action' => 'history',
                'view' => './views/order_history.php',
                'auth' => 2
            ),
            '/clothing-history' => array(
                'controller' => 'ClothingController',
                'action' => 'history',
                'view' => './views/clothing_history.php',
                'auth' => 5
            ),
            '/issue-history' => array(
                'controller' => 'IssueController',
                'action' => 'history',
                'view' => './views/issue_history.php',
                'auth' => 4
            ),
            '/employees' => array(
                'controller' => 'EmployeeController',
                'action' => 'list',
                'view' => './views/employee_list.php',
                'auth' => 4
            ),
            '/warehouse' => array(
                'controller' => 'WarehouseController',
                'action' => 'list',
                'view' => './views/warehouse_list.php',
                'auth' => 2
            ),
            '/add-order' => array(
                'controller' => 'OrderController',
                'action' => 'create',
                'view' => './views/add_order.php',
                'auth' => 2
            ),
            '/report' => array(
                'controller' => 'ReportController',
                'action' => 'index',
                'view' => './views/raport.php',
                'auth' => 4
            ),
            '/add-employee' => array(
                'controller' => 'EmployeeController',
                'action' => 'create',
                'view' => './views/add_employee.php',
                'auth' => 4
            ),
            '/login' => './views/auth/login.php'
        );
    }
    
    /**
     * Get mapping from clean URLs to page filenames (without path)
     */
    public static function getPageMap() {
        return array(
            '/' => 'issue_clothing.php',
            '/issue-clothing' => 'issue_clothing.php',
            '/order-history' => 'order_history.php',
            '/clothing-history' => 'clothing_history.php',
            '/issue-history' => 'issue_history.php',
            '/employees' => 'employee_list.php',
            '/warehouse' => 'warehouse_list.php',
            '/add-order' => 'add_order.php',
            '/report' => 'raport.php',
            '/add-employee' => 'add_employee.php',
            '/login' => 'login.php'
        );
    }
    
    /**
     * Get mapping from page filenames to clean URLs
     */
    public static function getUrlMap() {
        return array(
            'issue_history.php' => '/issue-history',
            'order_history.php' => '/order-history',
            'clothing_history.php' => '/clothing-history',
            'employee_list.php' => '/employees',
            'warehouse_list.php' => '/warehouse',
            'add_order.php' => '/add-order',
            'issue_clothing.php' => '/issue-clothing',
            'raport.php' => '/report',
            'add_employee.php' => '/add-employee',
            'login.php' => '/login'
        );
    }
} 