<?php

namespace App\Database;

use LessQL\Database;

interface ILessQL
{
    public function lessQL(): Database;
}
