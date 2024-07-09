<?php

namespace App\Importers;

use App\Importers\SourceReaders\AbstractSourceReader;
use App\Importers\SourceReaders\ICanUseBinarySourceReader;
use App\Importers\SourceReaders\ICanUseFilePathSourceReader;
use App\Importers\SourceReaders\ISourceReader;
use App\UnsupportedFeatureException;

class ImporterBuilder
{
    protected AbstractSourceReader $sourceReader;

    public function __construct(?string $fileType = null)
    {
        $fileType = mb_strtolower($fileType);
        $this->sourceReader = (new ImporterFactory($fileType))->getSourceReader();
        $this->setFileType($fileType);
    }

    public function build(): ISourceReader
    {
        return $this->sourceReader;
    }

    public function setFileType(?string $fileType): void
    {
        $this->sourceReader->setFileType($fileType);
    }

    /**
     * @throws UnsupportedFeatureException
     */
    public function setFilePath(string $filePath): static
    {
        if (!$this->sourceReader instanceof ICanUseFilePathSourceReader) {
            throw new UnsupportedFeatureException();
        }

        $this->sourceReader->setFilePath($filePath);

        return $this;
    }

    public function setBinary(string $binary): static
    {
        if (!$this->sourceReader instanceof ICanUseBinarySourceReader) {
            throw new UnsupportedFeatureException();
        }

        $this->sourceReader->setBinary($binary);

        return $this;
    }
}