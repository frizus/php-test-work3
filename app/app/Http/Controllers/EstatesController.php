<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Resource;
use App\Http\Requests\EstateCreateRequest;
use App\Http\Requests\EstateIndexRequest;
use App\Http\Requests\RestShowRequest;
use App\Repositories\EstateRepository;
use App\Repositories\IRepository;

class EstatesController extends ApiController
{
    use Resource;

    public function index()
    {
        return $this->listData(new EstateIndexRequest(), $this->repository);
    }

    public function create(): string
    {
        return $this->createItem(new EstateCreateRequest(), $this->repository);
    }

    public function show($id): string
    {
        return $this->itemData(new RestShowRequest($this->repository->getTableName(), ['id' => $id]), $this->repository);
    }

    public function update($id)
    {

    }

    public function delete($id)
    {

    }

    protected function getRepository(): IRepository
    {
        return new EstateRepository;
    }
}