<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Resource;
use App\Http\Requests\ContactIndexRequest;
use App\Repositories\ContactRepository;

class ContactsController
{
    use Resource;

    public function index()
    {
        return $this->listData(new ContactIndexRequest(), new ContactRepository());
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