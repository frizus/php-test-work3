<?php

use App\Config;
use App\Database\AbstractDatabase;
use App\Database\DatabaseManager;
use App\Database\ILessQL;
use LessQL\Database;

function db_connection(?string $connectionName = null): AbstractDatabase
{
    return DatabaseManager::getInstance()->connection($connectionName);
}

function db(?string $connectionName = null): Database
{
    /** @var ILessQL $connection */
    $connection = db_connection($connectionName);
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

function validate_int($value): bool
{
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

function validate_float($value): bool
{
    return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
}

function resolve_value($value)
{
    if (validate_int($value)) {
        $value = (int)$value;
    } elseif (validate_float($value)) {
        $value = (float)$value;
    }

    return $value;
}