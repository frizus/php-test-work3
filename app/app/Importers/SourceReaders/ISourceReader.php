<?php

namespace App\Importers\SourceReaders;

interface ISourceReader
{
    public function nextRow(): array|null;
}