<?php

namespace App\Repositories;

use App\Helpers\Arr;
use LessQL\Row;

abstract class BaseRepository implements IRepository, IFieldsOfFilterBy
{
    public const array FILTER_BY = [

    ];

    public const string TABLE_NAME = '';

    public function getAll(): array
    {
        return db()->table(static::TABLE_NAME)->orderBy('id')->fetchAll();
    }

    public function getById(int $id): Row|null
    {
        return db()->table(static::TABLE_NAME, $id);
    }

    /**
     * @throws ItemNotFoundException
     */
    public function getByIdOrFail(int $id): Row
    {
        if (!($entity = $this->getById($id))) {
            throw new ItemNotFoundException("Не найдена строка из таблицы " . static::TABLE_NAME);
        }

        return $entity;
    }

    public function save(mixed $entity): bool
    {
        $entity->save();
        return true;
    }

    public function filterBy(array $queryValues = []): array
    {
        $queryValues = $this->filterQueryValues($queryValues);

        $query = db()->table(static::TABLE_NAME);
        foreach ($queryValues as $fieldName => $queryValue) {
            $query = $query->where($fieldName, $queryValue);
        }
        $query->orderBy('id');

        return $query->fetchAll();
    }

    public static function getFieldsOfFilterBy(): ?array
    {
        static $filterBy;
        static $set = false;

        if (!$set) {
            $set = true;

            if (is_null(static::FILTER_BY)) {
                $filterBy = null;
            } else {
                $filterBy = Arr::wrap(static::FILTER_BY);
            }
        }

        return $filterBy;
    }

    protected function filterQueryValues($queryValues): array
    {
        $filterBy = $this::getFieldsOfFilterBy();

        if (is_null($filterBy)) {
            return $queryValues;
        }

        return Arr::filter(array_intersect_key($queryValues, array_fill_keys($filterBy, null)), fn ($value) => !!$value);
    }

    public function getTableName(): string
    {
        return static::TABLE_NAME;
    }
}
