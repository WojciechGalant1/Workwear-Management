<?php
require_once __DIR__ . '/../helpers/EnvLoader.php';

EnvLoader::load(__DIR__ . '/../../.env');

return [
    'host' => EnvLoader::get('DB_HOST'),
    'database' => EnvLoader::get('DB_NAME'),
    'username' => EnvLoader::get('DB_USER'),
    'password' => EnvLoader::get('DB_PASSWORD'),
];