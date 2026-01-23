<?php

class UrlHelper {

    public static function getBaseUrl() {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        return $basePath === '/' ? '' : $basePath;
    }


    public static function getAppBaseUrl() {
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
    

    public static function getCleanUri() {
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Remove basePath if it exists
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
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
    public static function getQueryParams() {
        $params = [];
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $params);
        }
        return $params;
    }
    
    /**
     * Build URL with query parameters
     */
    public static function buildUrl($path, $params = []) {
        $baseUrl = self::getBaseUrl();
        $url = $baseUrl . $path;
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }
    
} 