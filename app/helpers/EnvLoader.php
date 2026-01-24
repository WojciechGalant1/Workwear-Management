<?php
namespace App\Helpers;

use Exception;

class EnvLoader {
    private static array $variables = [];

    public static function load(string $path): void {
        if (!file_exists($path)) {
            throw new Exception('.env file not found');
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with($line, '#')) {
                continue;
            }
            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }
            [$name, $value] = $parts;
            $name = trim($name);
            $value = trim($value);
            
            if (preg_match('/^([\'"])(.*)\1$/', $value, $matches)) {
                $value = $matches[2];
            }
            
            self::$variables[$name] = $value;
        }
    }

    public static function get(string $key): ?string {
        return self::$variables[$key] ?? null;
    }

}