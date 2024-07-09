<?php

namespace App\Database;

use LessQL\Database;

class PostgreSqlDatabase extends AbstractDatabase implements ILessQL, IPDOAble
{
    protected array $config;

    protected \PDO $pdo;

    protected Database $lessQL;

    public function __construct($config = null)
    {
        $this->pdo = new \PDO(
            'pgsql:dbname=' . $config['database'] . ';host=' . $config['host'] . ';port=' . $config['port'],
            $config['username'] ?? null,
            $config['password'] ?? null,
            $config['options'] ?? null
        );
        $this->lessQL = new Database($this->pdo);
        $this->lessQL->setIdentifierDelimiter('"');
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function lessQL(): Database
    {
        return $this->lessQL;
    }
}