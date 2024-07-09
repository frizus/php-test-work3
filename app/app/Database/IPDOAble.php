<?php

namespace App\Database;

interface IPDOAble
{
    public function getPdo(): \PDO;
}