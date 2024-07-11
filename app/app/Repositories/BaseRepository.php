<?php

namespace App\Repositories;

use App\Helpers\Arr;
use LessQL\Row;

abstract class BaseRepository implements IRepository
{
    public const array FILTER_BY = [

    ];

    public function __construct(
        protected string $tableName
    )
    {

    }

    public function getAll(): array
    {
        return db()->table($this->tableName)->orderBy('id')->fetchAll();
    }

    public function getById(int $id): Row|null
    {
        return db()->table($this->tableName, $id);
    }

    public function save(mixed $entity): bool
    {
        $entity->save();
        return true;
    }

    public function filterBy(array $queryValues = []): array
    {
        $queryValues = $this->filterQueryValues($queryValues);

        $query = db()->table($this->tableName);
        foreach ($queryValues as $fieldName => $queryValue) {
            $query = $query->where($fieldName, $queryValue);
        }

        return $query->fetchAll();
    }

    protected function filterQueryValues($queryValues): array
    {
        static $filterBy;
        static $set = false;

        if (is_null(static::FILTER_BY)) {
            return $queryValues;
        }

        if (!$set) {
            $set = true;
            $filterBy = array_fill_keys(Arr::wrap(static::FILTER_BY), null);
        }

        return array_intersect_key($queryValues, $filterBy);
    }
}