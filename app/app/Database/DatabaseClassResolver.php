<?php

namespace App\Database;

use App\UnsupportedFeatureException;

class DatabaseClassResolver
{
    protected AbstractDatabase $db;

    public static function resolve(string $driver): string
    {
        switch ($driver) {
            case 'pgsql':
                return PostgreSqlDatabase::class;
                break;
            default:
                throw new UnsupportedFeatureException("DB driver {$driver} is not supported.");
                break;
        }
    }
}
