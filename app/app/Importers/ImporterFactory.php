<?php

namespace App\Importers;

use App\Importers\SourceReaders\ISourceReader;
use App\Importers\SourceReaders\PhpSpreadSheetSourceReader;

class ImporterFactory
{
    protected ISourceReader $sourceReader;

    public function __construct(?string $fileType = null)
    {
        $this->sourceReader = match ($fileType) {
            default => new PhpSpreadSheetSourceReader(),
        };
    }

    public function getSourceReader(): ISourceReader
    {
        return $this->sourceReader;
    }
}
