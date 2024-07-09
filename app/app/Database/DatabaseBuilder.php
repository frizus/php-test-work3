<?php

namespace App\Database;

use App\UnsupportedFeatureException;

class DatabaseBuilder
{
    protected AbstractDatabase $db;

    /**
     * @throws UnsupportedFeatureException
     */
    public function __construct(array $config)
    {
        /** @var AbstractDatabase $className */
        $className = DatabaseClassResolver::resolve($config['driver']);
        $this->db = new $className($config);
    }

    public function build(): AbstractDatabase
    {
        return $this->db;
    }
}