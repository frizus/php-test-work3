<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Resource;
use App\Http\Requests\AgencyCreateRequest;
use App\Http\Requests\AgencyIndexRequest;
use App\Http\Requests\EstateIndexRequest;
use App\Http\Requests\RestShowRequest;
use App\Repositories\AgencyRepository;
use App\Repositories\EstateRepository;
use App\Repositories\IRepository;

class AgenciesController extends ApiController
{
    use Resource;

    public function index(): string
    {
        return $this->listData(new AgencyIndexRequest(), $this->repository);
    }

    public function create(): string
    {
        return $this->createItem(new AgencyCreateRequest(), $this->repository);
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
        return new AgencyRepository;
    }
}