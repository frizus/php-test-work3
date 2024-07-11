<?php

namespace App\Repositories;

use LessQL\Row;

interface IRepository
{
    public function getAll(): array;

    public function getById(int $id): Row|null;

    public function getByIdOrFail(int $id): Row;

    public function save(mixed $entity): bool;

    public function filterBy(array $queryValues = []): array;

    public function getTableName(): string;
}