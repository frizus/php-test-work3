<?php

namespace App\Controllers;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator;

class AgenciesController
{
    protected const string TABLE_NAME = 'agency';

    public function index(): string
    {
        return allDataToXml(static::TABLE_NAME);
    }

    public function create()
    {

    }

    public function get($id): string
    {
        try {
            Validator::intVal()->positive()->assert($id);
        } catch (ValidationException $e) {
            return validationError($e);
        }

        $row = db()->table(static::TABLE_NAME, $id);
        return arrayToXml($row->getData());
    }

    public function update($id)
    {

    }

    public function delete($id)
    {

    }
}