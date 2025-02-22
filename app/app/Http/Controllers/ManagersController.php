<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Resource;
use App\Http\Requests\ManagerIndexRequest;
use App\Http\Requests\RestShowRequest;
use App\Repositories\IRepository;
use App\Repositories\ManagerRepository;

class ManagersController extends ApiController
{
    use Resource;

    public function index(): string
    {
        return $this->listData(new ManagerIndexRequest(), $this->repository);
    }

    public function show($id): string
    {
        return $this->itemData(new RestShowRequest($this->repository->getTableName(), ['id' => $id]), $this->repository);
    }

    protected function getRepository(): IRepository
    {
        return new ManagerRepository();
    }
}
