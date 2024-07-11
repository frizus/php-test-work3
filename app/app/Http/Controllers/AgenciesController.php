<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Resource;
use App\Http\Requests\AgencyIndexRequest;
use App\Http\Requests\EstateIndexRequest;
use App\Http\Requests\RestShowRequest;
use App\Repositories\AgencyRepository;
use App\Repositories\EstateRepository;
use App\Repositories\IRepository;

class AgenciesController
{
    use Resource;

    public function index(): string
    {
        return $this->listData(new AgencyIndexRequest(), $this->getRepository());
    }

    public function create()
    {

    }

    public function show($id): string
    {
        $repository = $this->getRepository();
        return $this->itemData(new RestShowRequest($repository->getTableName(), ['id' => $id]), $repository);
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