<?php

namespace App\Importers\SourceReaders;

interface ICanUseFilePathSourceReader
{
    public function setFilePath(string $filePath): void;
}