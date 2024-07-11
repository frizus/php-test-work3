<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Resource;
use App\Http\Requests\AgencyIndexRequest;
use App\Http\Requests\EstateIndexRequest;
use App\Repositories\AgencyRepository;
use App\Repositories\EstateRepository;

class AgenciesController
{
    use Resource;

    public function index(): string
    {
        return $this->listData(new AgencyIndexRequest(), new AgencyRepository());
    }

    public function create()
    {

    }

    public function get($id): string
    {
        $row = db()->table(static::TABLE_NAME, $id);
        return arrayToXml($row->getData());
    }

    public function update($id)
    {

    }

    public function delete($id)
    {

    }
}