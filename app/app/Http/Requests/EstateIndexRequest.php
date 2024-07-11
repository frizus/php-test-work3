<?php

namespace App\Http\Requests;

use App\Repositories\AgencyRepository;
use App\Repositories\ContactRepository;
use App\Repositories\EstateRepository;
use App\Repositories\ManagerRepository;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Factory;
use Respect\Validation\Validator;

class EstateIndexRequest extends RestIndexRequest
{
    public function fields(): ?array
    {
        return EstateRepository::getFieldsOfFilterBy();
    }

    public function callValidator(): void
    {
        Validator::key('agency_id', Validator::nullable(Validator::intVal()->positive()->existsInDatabase('agency')), false)
            ->key('contact_id', Validator::nullable(Validator::intVal()->positive()->existsInDatabase('contacts')), false)
            ->key('manager_id', Validator::nullable(Validator::intVal()->positive()->existsInDatabase('manager')), false)
            ->assert($this->data);
    }
}