<?php

namespace App\Http\Requests;

class RestCreateRequest extends BaseRequest
{
    protected function fields(): array
    {
        return [];
    }

    protected function getData(): array
    {
        $fields = $this->fields();

        $data = [];
        foreach ($fields as $field) {
            if (key_exists($field, $this->post)) {
                $data[$field] = $this->post[$field];
            }
        }

        return $data;
    }
}