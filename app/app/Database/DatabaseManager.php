<?php

namespace App\Database;

use App\UnsupportedFeatureException;

class DatabaseManager
{
    /**
     * @var AbstractDatabase[] $connections
     */
    protected array $connections;

    protected static self $instance;

    public static function getInstance(): static
    {
        if (!isset(static::$instance)) {
            static::$instance = new DatabaseManager();
        }

        return static::$instance;
    }

    /**
     * @throws UnsupportedConnectionException|UnsupportedFeatureException
     */
    public function connection(?string $connectionName = null): AbstractDatabase
    {
        $connectionName = $connectionName ?: config('database.default');

        if (!isset($this->connections[$connectionName])) {
            $key = 'database.connections.' . $connectionName;

            if (!has_config($key)) {
                throw new UnsupportedConnectionException("Config {$key} for connection {$connectionName} is not set.");
            }

            $this->connections[$connectionName] = (new DatabaseBuilder(config($key)))->build();
        }

        return $this->connections[$connectionName];
    }

    public function has(string $name): bool
    {
        return isset($this->connections[$name]);
    }
}
