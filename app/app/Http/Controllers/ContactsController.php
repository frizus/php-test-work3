<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Resource;
use App\Http\Requests\ContactIndexRequest;
use App\Http\Requests\RestShowRequest;
use App\Repositories\ContactRepository;
use App\Repositories\IRepository;

class ContactsController extends ApiController
{
    use Resource;

    public function index()
    {
        return $this->listData(new ContactIndexRequest(), $this->repository);
    }

    public function show($id): string
    {
        return $this->itemData(new RestShowRequest($this->repository->getTableName(), ['id' => $id]), $this->repository);
    }

    protected function getRepository(): IRepository
    {
        return new ContactRepository();
    }
}
