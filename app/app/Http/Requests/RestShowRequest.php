<?php

namespace App\Http\Requests;

use Respect\Validation\Validator;

class RestShowRequest extends BaseRequest
{
    public function __construct(
        protected string $tableName,
        protected ?array $query = null,
        protected ?array $post = null,
        protected ?array $files = null
    ) {
        parent::__construct($query, $post, $files);
    }

    protected function callValidator(): void
    {
        Validator::key('id', Validator::intVal()->positive()->existsInDatabase($this->tableName))
            ->check($this->data);
    }
}
