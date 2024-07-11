<?php

namespace App\Importers;

use App\Importers\Concerns\Stats;

abstract class AbstractImporter
{
    use Stats;

    protected const array MAP = [];
}
