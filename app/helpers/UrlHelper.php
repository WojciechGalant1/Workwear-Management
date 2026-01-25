<?php
namespace App\Helpers;

class UrlHelper {

    public static function getBaseUrl(): string {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        return $basePath === '/' ? '' : $basePath;
    }


    public static function getAppBaseUrl(): string {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $segments = [];
        if ($basePath !== '/') {
            $trimmed = trim($basePath, '/');
            $segments = $trimmed === '' ? [] : explode('/', $trimmed);
        }

        $stopDirs = ['handlers', 'views', 'log', 'app'];
        foreach ($stopDirs as $stop) {
            $pos = array_search($stop, $segments);
            if ($pos !== false) {
                $segments = array_slice($segments, 0, $pos);
                break;
            }
        }

        $prefix = '/' . implode('/', $segments);
        if ($prefix === '/') {
            return '';
        }
        return $prefix;
    }
    

    public static function getCleanUri(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Remove basePath if it exists
        $basePath = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
        // Normalize slashes for Windows/XAMPP
        $basePath = str_replace('\\', '/', $basePath);
        
        if ($basePath !== '/' && $basePath !== '.' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Ensure uri starts with /
        if (empty($uri) || substr($uri, 0, 1) !== '/') {
            $uri = '/' . $uri;
        }
        
        // Default path for home page
        if ($uri === '' || $uri === '/') {
            $uri = '/issue-clothing';
        }
        
        return $uri;
    }
    
    /**
     * Get query parameters from current request
     */
    public static function getQueryParams(): array {
        $params = [];
        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        if ($queryString !== '') {
            parse_str($queryString, $params);
        }
        return $params;
    }
    
    /**
     * Build URL with query parameters
     */
    public static function buildUrl(string $path, array $params = []): string {
        $baseUrl = self::getBaseUrl();
        $url = $baseUrl . $path;
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }
} 