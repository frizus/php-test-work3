<?php

namespace App\Http\Requests;

use App\Repositories\AgencyRepository;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

class AgencyIndexRequest extends RestIndexRequest
{
    public function fields(): ?array
    {
        return AgencyRepository::getFieldsOfFilterBy();
    }

    public function callValidator(): void
    {

    }
}