<?php

namespace App\Importers\SourceReaders;

class PhpSpreadSheetSourceReader extends AbstractSourceReader implements ISourceReader, ICanUseFilePathSourceReader
{
    public function __construct(protected ?string $filePath = null, ?string $fileType = null)
    {
        $this->setFileType($fileType);
    }

    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function nextRow(): array|null
    {
        return null;
    }
}