<?php

namespace App\Http\Requests;

use Respect\Validation\Validator;

class ContactCreateRequest extends RestCreateRequest
{
    protected function fields(): array
    {
        return ['name', 'phones', 'agency_id'];
    }

    protected function callValidator(): void
    {
        Validator::key('name', Validator::stringType()->length(1,255))
            ->key('phones', Validator::stringType()->length(1,255))
            ->key('agency_id', Validator::intVal()->positive()->existsInDatabase('agency'))
            ->assert($this->data);
    }
}