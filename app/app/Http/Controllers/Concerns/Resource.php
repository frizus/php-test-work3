<?php

namespace App\Http\Controllers\Concerns;

use App\Http\Requests\BaseRequest;
use App\Repositories\IRepository;
use App\Repositories\ItemNotFoundException;
use Respect\Validation\Exceptions\ValidationException;

trait Resource
{
    use Common;

    protected function listData(BaseRequest $request, IRepository $repository): string
    {
        try {
            $request->validate();
            $result = convertCollectionToXml($repository->filterBy($request->input()));
        } catch (ValidationException $e) {
            http_response_code(400);
            $result = validationErrorToXml($e);
        }

        $this->setAsXmlResponse();
        return $result;
    }

    protected function itemData(BaseRequest $request, IRepository $repository): string
    {
        try {
            $request->validate();
            $result = convertItemToXml($repository->getByIdOrFail($request->input('id')));
        } catch (ItemNotFoundException $e) {
            http_response_code(404);
            $result = errorToXml($e->getMessage());
        } catch (ValidationException $e) {
            http_response_code(400);
            $result = validationErrorToXml($e);
        }

        $this->setAsXmlResponse();
        return $result;
    }

    protected function createItem(BaseRequest $request, IRepository $repository): string
    {
        try {
            $entity = db()->createRow($repository->getTableName(), $request->input());
            $repository->save($entity);
        } catch (ValidationException $e) {
            http_response_code(400);
            $result = validationErrorToXml($e);
        } catch (\PDOException $e) {
            http_response_code(500);
            $result = errorToXml('Не удалось создать запись');
        }

        $this->setAsXmlResponse();
        return $result;
    }
}