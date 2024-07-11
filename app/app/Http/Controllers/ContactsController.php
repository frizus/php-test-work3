<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Resource;
use App\Http\Requests\ContactIndexRequest;
use App\Http\Requests\RestShowRequest;
use App\Repositories\ContactRepository;
use App\Repositories\IRepository;

class ContactsController
{
    use Resource;

    public function index()
    {
        return $this->listData(new ContactIndexRequest(), $this->getRepository());
    }

    public function create()
    {

    }

    public function show($id)
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
        return new ContactRepository;
    }
}