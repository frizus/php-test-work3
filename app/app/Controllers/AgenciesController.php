<?php

namespace App\Controllers;

class AgenciesController
{
    protected const string TABLE_NAME = 'agency';

    public function index()
    {
        return allDataToXml(static::TABLE_NAME);
    }

    public function create()
    {

    }

    public function get(int $id)
    {

    }

    public function update(int $id)
    {

    }

    public function delete(int $id)
    {

    }
}