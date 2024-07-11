<?php

namespace App\Http\Requests;

use Respect\Validation\Validator;

class EstateUpdateRequest extends RestCreateRequest
{
    protected function fields(): array
    {
        return ['external_id', 'address', 'price', 'rooms', 'floor', 'hours_floors', 'description', 'contact_id', 'manager_id', 'agency_id'];
    }

    protected function callValidator(): void
    {
        Validator::key('id', Validator::intVal()->positive()->existsInDatabase('estate'))
            ->key('external_id', Validator::stringType()->length(1,255), false)
            ->key('address', Validator::stringType()->length(1,255))
            ->key('price', Validator::intVal()->positive())
            ->key('rooms', Validator::intVal()->positive())
            ->key('hours_floors', Validator::intVal()->positive())
            ->key('description', Validator::stringType()->length(1,65535))
            ->key('contact_id', Validator::intVal()->positive()->existsInDatabase('contacts'))
            ->key('manager_id', Validator::intVal()->positive()->existsInDatabase('manager'))
            ->key('agency_id', Validator::intVal()->positive()->existsInDatabase('agency'))
            ->assert($this->data);
    }
}