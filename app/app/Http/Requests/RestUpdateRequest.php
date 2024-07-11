<?php

namespace App\Http\Requests;

class RestUpdateRequest extends RestCreateRequest
{
    protected function fields(): array
    {
        return [];
    }

    protected function getData(): array
    {
        $data = $this->getData();
        $data['id'] = $this->query['id'];

        return $data;
    }
}