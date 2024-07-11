<?php

namespace App\Importers\SourceReaders;

interface ISourceReader
{
    public function load(): void;

    public function close(): void;

    public function chunkNextRows(?array $map = null): array;
}