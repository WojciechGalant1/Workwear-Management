<?php
declare(strict_types=1);
namespace App\Config;

use App\Helpers\EnvLoader;

EnvLoader::load(__DIR__ . '/../../.env');

class DbConfig {
    public static function get(): array {
        return [
            'host' => EnvLoader::get('DB_HOST'),
            'database' => EnvLoader::get('DB_NAME'),
            'username' => EnvLoader::get('DB_USER'),
            'password' => EnvLoader::get('DB_PASSWORD'),
            'charset' => EnvLoader::get('DB_CHARSET') ?? 'utf8', 
            'options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        ];
    }
}