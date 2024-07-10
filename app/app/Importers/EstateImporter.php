<?php

namespace App\Importers;

use App\Helpers\Arr;
use App\Importers\Concerns\ValueNormalizers;
use App\Importers\SourceReaders\ISourceReader;
use LessQL\Row;

class EstateImporter extends AbstractImporter
{
    use ValueNormalizers;

    protected const array MAP = [
        'Агенство Недвижимости' => 'agency.name',
        'Менеджер' => 'manager.name',
        'Продавец' => 'contacts.name',
        'Телефоны продавца' => 'contacts.phones',
        'id' => 'estate.external_id',
        'Цена' => 'estate.price',
        'Описание' => 'estate.description',
        'Адрес' => 'estate.address',
        'Этаж' => 'estate.floor',
        'Этажей' => 'estate.house_floors',
        'Комнат' => 'estate.rooms',
    ];

    protected const array IDENTIFYING_FIELDS = [
        'agency' => 'name',
        'estate' => 'external_id',
        'contacts' => ['name', 'phones'],
        'manager' => ['name', 'agency_id'],
    ];

    protected const array FIELD_NORMALIZERS = [
        'estate' => [
            'price' => 'integer',
        ],
        'contacts' => [
            'phones' => 'phones'
        ],
    ];

    protected const array RELATIONS_1_TO_1 = [
        'manager' => [
            'agency_id' => 'agency',
        ],
        'estate' => [
            'contact_id' => 'contacts',
            'manager_id' => 'manager',
        ]
    ];

    protected array $touched;

    protected ?array $row;

    protected ?array $rowHelper;

    protected ?string $importingTable;

    protected array $importOrder;

    public function __construct(
        protected ISourceReader $sourceReader
    )
    {

    }

    public function run(): void
    {
        $this->sourceReader->load();
        $this->initStats();
        $this->touched = [];
        $this->importOrder = ['agency', 'manager', 'contacts', 'estate'];
        $l = count($this->importOrder);
        while ($rows = $this->sourceReader->chunkNextRows(static::MAP)) {
            $this->normalizeRowsValues($rows);
            foreach ($rows as $row) {
                $this->beforeImport($row);
                foreach ($this->importOrder as $i => $tableName) {
                    $this->importingTable = $tableName;
                    $this->importRow(($i + 1) >= $l);
                }
            }
        }
    }

    protected function statsKeys(): array
    {
        $statsKeys = [];

        foreach (['agency', 'manager', 'contacts', 'estate'] as $tableName) {
            foreach (['same', 'updated', 'created'] as $action) {
                $statsKeys[] = $action . '_' . $tableName;
            }
        }

        return $statsKeys;
    }

    protected function beforeImport(array $row): void
    {
        $this->rowHelper = [];
        $this->row = &$row;

        foreach ($this->row as $tableName => $entityValues) {
            $this->prepareSignatures($entityValues, $tableName);
        }

        $this->prepareExistingRows('estate');
    }

    protected function prepareSignatures(array $entityValues, string $tableName): void
    {
        $signature = $this->getEntityValuesSignature($entityValues, $tableName);
        $this->rowHelper[$tableName]['signature'] = $signature;
    }

    protected function prepareExistingRows(string|array|null $excludeFromCaching = null): void
    {
        $excludeFromCaching = Arr::wrap($excludeFromCaching);

        foreach ($this->importOrder as $tableName) {
            $cache = !in_array($tableName, $excludeFromCaching, true);
            $this->rowHelper[$tableName]['existing'] = $this->selectRow($tableName, $this->row[$tableName], $this->rowHelper[$tableName]['signature'], $cache);
        }
    }

    protected function rememberNewEntity(Row $entity): void
    {
        $this->rowHelper[$this->importingTable]['existing'] = $entity;
    }

    protected function getEntityValuesSignature(array $entityValues, string $tableName): string
    {
        $identifyingFields = $this->getIdentifyingEntityValues($entityValues, $tableName, true);
        return serialize($identifyingFields);
    }

    protected function getIdentifyingEntityValues(mixed $entityValues, string $tableName, bool $noForeignIds = false): array|false
    {
        $identifyingFields = $this->getIdentifyingFieldsOfTable($tableName);
        $identifyingEntityValues = [];

        foreach ($identifyingFields as $identifyingField) {
            if ($theirTableName = $this->get1to1RelationshipTable($tableName, $identifyingField)) {
                if ($noForeignIds) {
                    $identifyingEntityValues[$identifyingField] = $this->getIdentifyingEntityValues($this->row[$theirTableName], $theirTableName, $noForeignIds);
                } else {
                    if (!$this->rowHelper[$theirTableName]['existing']) {
                        return false;
                    }
                    $identifyingEntityValues[$identifyingField] = $this->rowHelper[$theirTableName]['existing']->id;
                }
            } else {
                $identifyingEntityValues[$identifyingField] = $entityValues[$identifyingField];
            }
        }

        return $identifyingEntityValues;
    }

    protected function importRow(bool $isLast): void
    {
        // $this->rowHelper[$this->importingTable] = ['signature' => ..., 'existing' => ...];

        $entityValues = $this->row[$this->importingTable];
        if ($existingEntity = $this->rowHelper[$this->importingTable]['existing']) {
            if ($otherFields = $this->getNotIdentifyingFieldListForTable($this->importingTable, true)) {
                foreach ($otherFields as $field) {
                    $existingEntity[$field] = $entityValues[$field];
                }

                $this->setForeignKeys($existingEntity);

                if ($existingEntity->isClean()) {
                    if ($this->entityNotTouched()) {
                        $this->incrStat('same_' . $this->importingTable);
                        $this->touchedEntity($existingEntity);
                    }
                } else {
                    $existingEntity->save();
                    $this->incrStat('updated_' . $this->importingTable);
                    $this->touchedEntity($existingEntity);
                }
            } elseif ($this->entityNotTouched()) {
                $this->incrStat('same_' . $this->importingTable);
                $this->touchedEntity($existingEntity);
            }
        } else {
            $this->setForeignKeys($entityValues);

            $newEntity = db()->createRow($this->importingTable, $entityValues)->save();

            $this->incrStat('created_' . $this->importingTable);

            if (!$isLast) {
                $this->rememberNewEntity($newEntity);
            }
            $this->touchedEntity($newEntity);
        }
    }

    protected function selectRow(string $tableName, array $entityValues, string $signature, bool $cache = true): Row|null
    {
        static $cached = [];

        if ($cache) {
            $cacheKey = $tableName . ' ' . $signature;
            if (key_exists($cacheKey, $cached)) {
                return $cached[$cacheKey];
            }
        }

        if (!($queryValues = $this->getIdentifyingEntityValues($entityValues, $tableName))) {
            return null;
        }

        $query = db()->table($tableName);
        foreach ($queryValues as $identifyingField => $queryValue) {
            $query = $query->where($identifyingField, $queryValue);
        }

        if (!($item2 = $query->fetch())) {
            return null;
        }

        if ($cache) {
            $cached[$cacheKey] = $item2;
        }

        return $item2;
    }

    protected function normalizeRowsValues(array &$rows): void
    {
        foreach ($rows as &$row) {
            foreach ($row as $tableName => $entityValues) {
                foreach ($entityValues as $fieldKey => $value) {
                    $row[$tableName][$fieldKey] = $this->getNormalizedValue($entityValues, $tableName, $fieldKey);
                }
            }
        }
    }

    protected function getNotIdentifyingFieldListForTable(string $tableName): array
    {
        static $lists = [];
        $key = $tableName;

        if (!key_exists($key, $lists)) {
            $list = &$lists[$key];
            $list = [];
            foreach (static::MAP ?? [] as $field) {
                $parts = explode('.', $field, 2);
                if ($tableName !== $parts[0]) {
                    continue;
                }

                $list[$parts[1]] = $parts[1];
            }

            foreach ($this->getIdentifyingFieldsOfTable($tableName) as $identifyingField) {
                unset($list[$identifyingField]);
            }
        }

        return $lists[$key];
    }

    protected function setForeignKeys(mixed &$entity): void
    {
        foreach ($this->get1to1RelationshipTables($this->importingTable) as $ourTableForeignId => $theirTableName) {
            if ($existingEntity = $this->rowHelper[$theirTableName]['existing']) {
                $entity[$ourTableForeignId] = $existingEntity->id;
            } else {
                $entity[$ourTableForeignId] = null;
            }
        }
    }

    protected function getNormalizedValue(array $entity, string $tableName, string|array $field): mixed
    {
        $value = Arr::get($field, $entity);

        if (is_null(static::FIELD_NORMALIZERS) ||
            !key_exists($tableName, static::FIELD_NORMALIZERS) ||
            !key_exists($field, static::FIELD_NORMALIZERS[$tableName])
        ) {
            return $value;
        }

        $normalizer = static::FIELD_NORMALIZERS[$tableName][$field];

        return $this->{'normalizerFor' . $normalizer}($value);
    }

    protected function getIdentifyingFieldsOfTable(string $tableName): array
    {
        return Arr::wrap(static::IDENTIFYING_FIELDS[$tableName]);
    }

    protected function get1to1RelationshipTable(string $tableName, string|int $ourTableForeignId): ?string
    {
        return $this->get1to1RelationshipTables($tableName)[$ourTableForeignId] ?? null;
    }

    protected function get1to1RelationshipTables(string $tableName): array
    {
        return static::RELATIONS_1_TO_1[$tableName] ?? [];
    }

    protected function touchedEntity(mixed $entity): void
    {
        $signature = $this->rowHelper[$this->importingTable]['signature'];
        $this->touched[$signature] = $this->getIdentifyingEntityValues($entity, $this->importingTable);
    }

    protected function entityNotTouched(): bool
    {
        $signature = $this->rowHelper[$this->importingTable]['signature'];
        return !key_exists($signature, $this->touched);
    }
}