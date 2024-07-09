<?php

use App\Config;

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