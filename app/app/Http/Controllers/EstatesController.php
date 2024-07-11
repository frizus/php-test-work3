<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Resource;
use App\Http\Requests\EstateIndexRequest;
use App\Repositories\EstateRepository;

class EstatesController
{
    use Resource;

    public function index()
    {
        return $this->listData(new EstateIndexRequest(), new EstateRepository());
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