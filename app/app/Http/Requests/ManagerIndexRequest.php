<?php

namespace App\Http\Requests;

use App\Repositories\AgencyRepository;
use App\Repositories\ContactRepository;
use App\Repositories\ManagerRepository;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Factory;
use Respect\Validation\Validator;

class ManagerIndexRequest extends RestIndexRequest
{
    public function fields(): ?array
    {
        return ManagerRepository::getFieldsOfFilterBy();
    }

    protected function callValidator(): void
    {
        Validator::key('agency_id', Validator::intVal()->positive()->existsInDatabase('agency'), false)
            ->check($this->data);
    }
}
