<?php

namespace App\Http\Requests;

use Respect\Validation\Validator;

class ManagerUpdateRequest extends RestCreateRequest
{
    protected function fields(): array
    {
        return ['name', 'agency_id'];
    }

    protected function callValidator(): void
    {
        Validator::key('id', Validator::intVal()->positive()->existsInDatabase('manager'))
            ->key('name', Validator::stringType()->length(1,255))
            ->key('agency_id', Validator::intVal()->positive()->existsInDatabase('agency'))
            ->assert($this->data);
    }
}