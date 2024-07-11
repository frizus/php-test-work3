<?php

namespace App\Importers\SourceReaders;

class AbstractSourceReader
{
    protected int $chunk = 100;

    protected string $fileType;

    public function setFileType(?string $fileType): void
    {
        $this->fileType = mb_strtolower($fileType);
    }
}
