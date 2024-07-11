<?php

namespace App\Http\Requests;

use App\Repositories\AgencyRepository;
use App\Repositories\ContactRepository;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Factory;
use Respect\Validation\Validator;

class ContactIndexRequest extends RestIndexRequest
{
    public function fields(): ?array
    {
        return ContactRepository::getFieldsOfFilterBy();
    }

    protected function callValidator(): void
    {
        Validator::key('agency_id', Validator::intVal()->positive()->existsInDatabase('agency'), false)
            ->assert($this->data);
    }
}