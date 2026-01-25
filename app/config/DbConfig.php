<?php
declare(strict_types=1);
use App\Helpers\EnvLoader;

EnvLoader::load(__DIR__ . '/../../.env');

return [
    'host' => EnvLoader::get('DB_HOST'),
    'database' => EnvLoader::get('DB_NAME'),
    'username' => EnvLoader::get('DB_USER'),
    'password' => EnvLoader::get('DB_PASSWORD'),
];