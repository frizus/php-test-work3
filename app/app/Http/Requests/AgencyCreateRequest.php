<?php

namespace App\Http\Requests;

use Respect\Validation\Validator;

class AgencyCreateRequest extends RestCreateRequest
{
    protected function fields(): array
    {
        return ['name'];
    }

    protected function callValidator(): void
    {
        Validator::key('name', Validator::stringType()->length(1,255))
            ->assert($this->data);
//        Validator::key('id', Validator::intVal()->positive()->existsInDatabase('agency'))
//            ->key('name', Validator::stringType()->length(1,255))
//            ->assert($this->data);
    }
}