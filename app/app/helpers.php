<?php

use App\Config;
use App\Database\DatabaseManager;
use App\Database\ILessQL;
use LessQL\Database;

function db(?string $connectionName = null): Database
{
    /** @var ILessQL $connection */
    $connection = DatabaseManager::getInstance()->connection($connectionName);
    return $connection->lessQL();
}

function config(string $key, mixed $default = null): mixed
{
    return Config::getInstance()->get($key, $default);
}

function has_config(string $key): bool
{
    return Config::getInstance()->has($key);
}

function root_path(): string
{
    static $rootPath;

    if (!isset($rootPath)) {
        $rootPath = $_SERVER['DOCUMENT_ROOT'] ?: realpath(__DIR__ . '/..');
    }

    return $rootPath;
}