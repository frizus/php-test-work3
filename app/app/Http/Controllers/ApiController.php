<?php

namespace App\Http\Controllers;

use App\Repositories\IRepository;

abstract class ApiController
{
    protected IRepository $repository;

    public function __construct()
    {
        $this->repository = $this->getRepository();
    }

    abstract protected function getRepository(): IRepository;
}
