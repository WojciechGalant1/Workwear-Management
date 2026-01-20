<?php
class EnvLoader {
    private static $variables = array();

    public static function load($path) {
        if (!file_exists($path)) {
            throw new Exception('.env file not found');
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            if (preg_match('/^([\'"])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }
            
            self::$variables[$name] = $value;
        }
    }

    public static function get($key) {
        return isset(self::$variables[$key]) ? self::$variables[$key] : null;
    }

}