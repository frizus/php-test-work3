<?php

namespace App\Importers\Concerns;

trait Stats
{
    protected array $stats = [];

    protected function incrStat($name): void
    {
        $this->stats[$name] ??= 0;
        $this->stats[$name]++;
    }

    public function getImportStats(): array
    {
        return $this->stats;
    }
}