<?php

namespace App\Importers;

use App\Importers\SourceReaders\ISourceReader;
use App\Importers\SourceReaders\PhpSpreadSheetSourceReader;

class ImporterFactory
{
    protected ISourceReader $sourceReader;

    public function __construct(?string $fileType = null)
    {
        switch ($fileType) {
            case 'xlsx':
            case 'xls':
            case 'csv':
            default:
                $this->sourceReader = new PhpSpreadSheetSourceReader();
                break;
        }
    }

    public function getSourceReader(): ISourceReader
    {
        return $this->sourceReader;
    }
}
