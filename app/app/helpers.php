<?php

function root_path(): string
{
    static $rootPath;

    if (!isset($rootPath)) {
        $rootPath = $_SERVER['DOCUMENT_ROOT'] ?: realpath(__DIR__ . '/..');
    }

    return $rootPath;
}