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

    protected const array MAIN_FIELDS = [
        'agency' => 'name',
        'estate' => 'external_id',
        'contacts' => 'name',
        'manager' => 'name',
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

    public function __construct(
        protected ISourceReader $sourceReader
    )
    {

    }

    public function run()
    {
        $this->sourceReader->load();
        $this->touched = [];
        while ($rows = $this->sourceReader->chunkNextRows(static::MAP)) {
            $this->importForTable('agency', $rows, true);
            $this->importForTable('manager', $rows, true);
            $this->importForTable('contacts', $rows, true);
            $this->importForTable('estate', $rows);
        }
    }

    protected function importForTable(string $tableName, array $rows, bool $cache = false): void
    {
        $mainField = $this->getMainFieldOfTable($tableName);
        $newEntities = [];
        $relatedMainFieldValuesForEntities = [];
        $relatedMainFieldValues = [];
        $relationsForTable = $this->getRelationsForTable($tableName);

        foreach ($rows as $row) {
            if (!($key = $this->getNormalizedValue($row[$tableName], $tableName, $mainField))) {
                continue;
            }

            $newEntity = Arr::get($tableName, $row);
            foreach ($newEntity as $fieldKey => $field) {
                $newEntity[$fieldKey] = $this->getNormalizedValue($newEntity, $tableName, $fieldKey);
            }
            $newEntities[$key] = $newEntity;

            foreach ($relationsForTable as $baseForeignId => $relationForTable) {
                $relatedMainField = static::MAIN_FIELDS[$relationForTable['table']];

                if (!($foreignKeyValue = $this->getNormalizedValue($row[$relationForTable['table']], $relationForTable['table'], $relatedMainField))) {
                    continue;
                }

                $relatedMainFieldValuesForEntities[$key][$baseForeignId] = $foreignKeyValue;
                $relatedMainFieldValues[$baseForeignId][$foreignKeyValue] = $foreignKeyValue;
            }
        }

        if (!$newEntities) {
            return;
        }

        $existingEntities = $this->selectRows($tableName, $mainField, array_keys($newEntities), $cache);
        $existingRelatedEntities = [];
        foreach ($relatedMainFieldValues as $baseForeignId => $foreignKeyValues) {
            $relatedTable = $relationsForTable[$baseForeignId]['table'];
            $relatedMainField = static::MAIN_FIELDS[$relatedTable];
            $existingRelatedEntities[$baseForeignId] = $this->selectRows($relatedTable, $relatedMainField, $foreignKeyValues);
        }

        if ($existingEntities) {
            if ($tableHasOtherFields = $this->tableHasOtherFields($tableName, $mainField)) {
                foreach ($existingEntities as $key => $existingEntity) {
                    foreach ($this->getFieldListForTable($tableName, true) as $field) {
                        $existingEntity[$field] = $newEntities[$key][$field];
                    }

                    $this->setForeignKeys($key, $existingEntity, $relationsForTable, $existingRelatedEntities, $relatedMainFieldValuesForEntities);

                    if ($existingEntity->isClean()) {
                        $this->incrStat('same_' . $tableName);
                    } else {
                        $existingEntity->save();
                        $this->incrStat('updated_' . $tableName);
                    }
                }
            }

            $countBefore = count($newEntities);
            $newEntities = array_diff_key($newEntities, $existingEntities);
            $countAfter = count($newEntities);
            if (!$tableHasOtherFields) {
                $this->incrStat('same_' . $tableName, $countBefore - $countAfter);
            }
        }

        if (!$newEntities) {
            return;
        }

        foreach ($newEntities as $key => $newEntity) {
            $this->setForeignKeys($key, $newEntity, $relationsForTable, $existingRelatedEntities, $relatedMainFieldValuesForEntities);
            db()->createRow($tableName, $newEntity)->save();
            $this->incrStat('created_' . $tableName);
        }
    }

    protected function getFieldListForTable(string $tableName, bool $excludeMainField = false): array
    {
        $fields = [];
        foreach (static::MAP[$tableName] ?? [] as $field) {
            $parts = explode('.', $field, 2);
            if ($tableName !== $parts[0]) {
                continue;
            }

            $fields[$parts[1]] = $parts[1];
        }

        if ($excludeMainField) {
            unset($fields[$this->getMainFieldOfTable($tableName)]);
        }

        return $fields;
    }

    protected function setForeignKeys(string $key, mixed &$entity, array $relationsForTable, array $existingRelatedEntities, array $relatedMainFieldValuesForEntities): void
    {
        foreach ($relationsForTable as $baseForeignId => $relationForTable) {
            if (!$existingRelatedEntities[$baseForeignId]) {
                continue;
            }

            $relatedMainFieldValue = $relatedMainFieldValuesForEntities[$key][$baseForeignId];
            if ($relatedEntity = $existingRelatedEntities[$baseForeignId][$relatedMainFieldValue]) {
                $entity[$baseForeignId] = $relatedEntity->id;
            }
        }
    }

    protected function getNormalizedValue(array $entity, string $tableName, string|array $field): mixed
    {
        $value = Arr::get($field, $entity);

        if (!key_exists($tableName, static::FIELD_NORMALIZERS) ||
            !key_exists($field, static::FIELD_NORMALIZERS[$tableName])
        ) {
            return $value;
        }

        $normalizer = static::FIELD_NORMALIZERS[$tableName][$field];

        return $this->{'normalizerFor' . $normalizer}($value);
    }

    protected function getMainFieldOfTable(string $tableName): string|null
    {
        return static::MAIN_FIELDS[$tableName] ?? null;
    }

    protected function getRelationsForTable(string $tableName)
    {
        static $relationsForTables = [];

        if (!key_exists($tableName, $relationsForTables)) {
            $relationsForTable = &$relationsForTables[$tableName];
            $relationsForTable = [];

            if (key_exists($tableName, static::RELATIONS_1_TO_1)) {
                foreach (static::RELATIONS_1_TO_1[$tableName] as $baseForeignId => $foreignTable) {
                    $foreignTable = Arr::wrap($foreignTable);
                    $relationsForTable[$baseForeignId] = [
                        'table' => $foreignTable[0],
                        'key' => $foreignTable[1] ?? 'id',
                    ];
                }
            }
        }

        return $relationsForTables[$tableName];
    }

    /**
     * @param string $tableName
     * @param string $mainField
     * @param array $values
     * @param bool $cache
     * @return Row[]
     */
    protected function selectRows(string $tableName, string $mainField, array $values, bool $cache = true): array
    {
        static $cached = [];
        $result = [];

        if (!$values) {
            return $result;
        }

        if ($cache) {
            foreach ($values as $i => $value) {
                if (key_exists($value, $cached)) {
                    $result[$value] = $cached[$value];
                    unset($values[$i]);
                }
            }
        }

        if (!$values) {
            return $result;
        }

        $result2 = db()->table($tableName)->where($mainField, array_values($values))->fetchAll();

        foreach ($result2 as $i => $item2) {
            $value = $item2[$mainField];
            $cached[$value] = &$result2[$i];
            $result[$value] = $cached[$value];
            $this->touched[$tableName][$value] = null;
            unset($result2[$i]);
        }

        return $result;
    }

    protected function tableHasOtherFields(string $tableName, string $mainField = 'name'): bool
    {
        if (isset(static::MAP[$tableName]) &&
            key_exists($mainField, static::MAP[$tableName]) &&
            (count(static::MAP[$tableName]) > 1)
        ) {
            return true;
        }

        if (isset(static::RELATIONS_1_TO_1[$tableName]) &&
            !empty(static::RELATIONS_1_TO_1[$tableName])
        ) {
            return true;
        }

        return false;
    }
}