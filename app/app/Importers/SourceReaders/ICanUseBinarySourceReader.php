<?php

namespace App\Importers\SourceReaders;

interface ICanUseBinarySourceReader
{
    public function setBinary(string $binary): void;
}