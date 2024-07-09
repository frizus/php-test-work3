<?php

namespace App\Importers\Concerns;

trait Stats
{
    protected array $stats = [];

    protected function incrStat($name, $incrBy = 1): void
    {
        $this->stats[$name] ??= 0;
        $this->stats[$name] += $incrBy;
    }

    public function getImportStats(): array
    {
        return $this->stats;
    }
}