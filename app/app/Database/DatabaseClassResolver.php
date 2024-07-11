<?php

namespace App\Database;

use App\UnsupportedFeatureException;

class DatabaseClassResolver
{
    protected AbstractDatabase $db;

    public static function resolve(string $driver): string
    {
        return match ($driver) {
            'pgsql' => PostgreSqlDatabase::class,
            default => throw new UnsupportedFeatureException("DB driver {$driver} is not supported."),
        };
    }
}
