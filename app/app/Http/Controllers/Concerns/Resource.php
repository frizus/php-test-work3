<?php

namespace App\Http\Controllers\Concerns;

use App\Http\Requests\BaseRequest;
use App\Repositories\IRepository;
use Respect\Validation\Exceptions\ValidationException;

trait Resource
{
    use Common;

    protected function listData(BaseRequest $request, IRepository $repository)
    {
        try {
            $result = convertCollectionToXml($repository->filterBy($request->input()));
        } catch (ValidationException $e) {
            http_response_code(400);
            $result = validationError($e);
        }

        $this->setAsXmlResponse();
        return $result;
    }
}