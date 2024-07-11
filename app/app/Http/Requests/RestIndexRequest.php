<?php

namespace App\Http\Requests;

class RestIndexRequest extends BaseRequest
{
    protected function fields(): ?array
    {
        return null;
    }

    protected function getData(): array
    {
        if (is_null($fields = $this->fields())) {
            return $this->query;
        }

        $data = [];
        foreach ($fields as $field) {
            if (key_exists($field, $this->query)) {
                $data[$field] = $this->query[$field];
            }
        }

        return $data;
    }
}
