<?php

namespace App\Importers\SourceReaders;

use App\Helpers\Arr;
use App\UnsupportedFeatureException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PhpSpreadSheetSourceReader extends AbstractSourceReader implements ISourceReader, ICanUseFilePathSourceReader
{
    protected Spreadsheet $spreadsheet;

    protected array $columns;

    protected int $currentRowIndex;

    protected int $highestRowIndex;

    public function __construct(protected ?string $filePath = null, ?string $fileType = null)
    {
        $this->setFileType($fileType);
    }

    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function load(): void
    {
        if (!$this->fileType) {
            $this->spreadsheet = IOFactory::load($this->filePath, IReader::READ_DATA_ONLY);
        } else {
            switch ($this->fileType) {
                case 'xlsx':
                    $reader = new Xlsx();
                    break;
                case 'xls':
                    $reader = new Xls();
                    break;
                case 'csv':
                    $reader = new Csv();
                    break;
                default:
                    throw new UnsupportedFeatureException("Unsupported file type \"{$this->fileType}\"");
                    break;
            }
            $reader->setReadDataOnly(true);
            $this->spreadsheet = $reader->load($this->filePath);
        }
        $this->readColumnNames();
        $this->prepareForChunkReading();
    }

    public function prepareForChunkReading(): void
    {
        $this->currentRowIndex = 2;
        $this->highestRowIndex = $this->spreadsheet->getActiveSheet()->getHighestRow();
    }

    public function chunkNextRows(?array $map = null): array
    {
        if ($this->currentRowIndex > $this->highestRowIndex) {
            return [];
        }

        $endRowIndex = $this->currentRowIndex + $this->chunk - 1;
        if ($endRowIndex > $this->highestRowIndex) {
            $endRowIndex = $this->highestRowIndex;
        }

        $rows = [];
        foreach ($this->spreadsheet->getActiveSheet()->getRowIterator($this->currentRowIndex, $endRowIndex) as $row) {
            $rowData = [];
            foreach ($row->getColumnIterator() as $column) {
                $columnName = $this->columns[$column->getColumn()];
                $columnName = $this->convertColumnToMappedName($columnName, $map);
                Arr::set($rowData, $columnName, $this->prepareValue($column->getValue()));
            }
            $rows[] = $rowData;
        }
        $this->currentRowIndex = $endRowIndex + 1;

        return $rows;
    }

    protected function convertColumnToMappedName(string $columnName, array|null $map): string|array
    {
        return isset($map) && key_exists($columnName, $map) ? $map[$columnName] : [$columnName];
    }

    protected function prepareValue(mixed $value): mixed
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        if (is_numeric($value)) {
            $value = (string)$value;
        }

        if (is_null($value) || is_bool($value)) {
            $value = '';
        }

        return $value;
    }

    protected function readColumnNames(): void
    {
        $this->spreadsheet->getActiveSheet()->getRowIterator()->resetStart();
        $row = $this->spreadsheet->getActiveSheet()->getRowIterator()->current();
        foreach ($row->getColumnIterator() as $column) {
            $this->columns[$column->getColumn()] = $this->prepareValue($column->getValue());
        }
    }
}