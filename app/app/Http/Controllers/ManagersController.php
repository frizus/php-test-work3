<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Resource;
use App\Http\Requests\ContactIndexRequest;
use App\Http\Requests\ManagerIndexRequest;
use App\Repositories\ContactRepository;
use App\Repositories\ManagerRepository;

class ManagersController
{
    use Resource;

    public function index()
    {
        return $this->listData(new ManagerIndexRequest(), new ManagerRepository());
    }

    public function create()
    {

    }

    public function show($id)
    {

    }

    public function update($id)
    {

    }

    public function delete($id)
    {

    }
}