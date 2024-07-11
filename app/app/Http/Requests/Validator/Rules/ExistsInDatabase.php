<?php

namespace App\Http\Requests\Validator\Rules;

use Respect\Validation\Rules\AbstractRule;

class ExistsInDatabase extends AbstractRule
{
    public function __construct(
        protected string $tableName
    ) {

    }

    public function validate($input): bool
    {
        return (bool)db()->table($this->tableName)->where('id', $input)->select('id')->fetch();
    }
}
