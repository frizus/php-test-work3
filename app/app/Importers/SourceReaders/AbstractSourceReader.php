<?php

namespace App\Importers\SourceReaders;

class AbstractSourceReader
{
    protected int $chunk = 10;

    protected string $fileType;

    public function setFileType(?string $fileType)
    {
        $this->fileType = mb_strtolower($fileType);
    }
}