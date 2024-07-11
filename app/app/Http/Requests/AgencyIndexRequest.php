<?php

namespace App\Http\Requests;

use App\Repositories\AgencyRepository;

class AgencyIndexRequest extends RestIndexRequest
{
    public function fields(): ?array
    {
        return AgencyRepository::getFieldsOfFilterBy();
    }

    protected function callValidator(): void
    {

    }
}
