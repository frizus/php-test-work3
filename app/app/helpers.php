<?php

use App\Config;
use App\Database\AbstractDatabase;
use App\Database\DatabaseManager;
use App\Database\ILessQL;
use App\Helpers\Arr;
use LessQL\Database;
use LessQL\Row;
use Phroute\Phroute\RouteCollector;
use Respect\Validation\Exceptions\ValidationException;
use Spatie\ArrayToXml\ArrayToXml;

function db_connection(?string $connectionName = null): AbstractDatabase
{
    return DatabaseManager::getInstance()->connection($connectionName);
}

function db(?string $connectionName = null): Database
{
    /** @var ILessQL $connection */
    $connection = db_connection($connectionName);
    return $connection->lessQL();
}

function config(string $key, mixed $default = null): mixed
{
    return Config::getInstance()->get($key, $default);
}

function has_config(string $key): bool
{
    return Config::getInstance()->has($key);
}

function root_path(): string
{
    static $rootPath;

    if (!isset($rootPath)) {
        $rootPath = realpath(__DIR__ . '/..');
    }

    return $rootPath;
}

function validate_int($value): bool
{
    return filter_var($value, FILTER_VALIDATE_INT) !== false;
}

function validate_float($value): bool
{
    return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
}

function resolve_value($value): mixed
{
    if (validate_int($value)) {
        $value = (int)$value;
    } elseif (validate_float($value)) {
        $value = (float)$value;
    }

    return $value;
}

function apiResource($route, $controllerClass, RouteCollector $collector): void
{
    $idPart = '/{id:\d+}';
    $indexPart = '/index';
    $collector->get($route, [$controllerClass, 'index']);
    $collector->get($route . $indexPart, [$controllerClass, 'index']);
    $collector->post($route, [$controllerClass, 'create']);
    $collector->post($route . $indexPart, [$controllerClass, 'create']);
    $collector->get($route . $idPart, [$controllerClass, 'show']);
    $collector->put($route . $idPart, [$controllerClass, 'update']);
    $collector->delete($route . $idPart, [$controllerClass, 'delete']);
}

function convertCollectionToXml($result, $fields = null): string
{
    return arrayToXml(prepareCollectionForXmlRender($result, $fields, fn(Row $row) => $row->getData()));
}

function convertItemToXml($result, $specificColumns = null)
{
    $itemData = getItemData($result, fn(Row $row) => $row->getData());
    $itemData = removeExtraColumns($itemData, $specificColumns);

    return arrayToXml($itemData);
}

function prepareCollectionForXmlRender($result, $specificColumns = null, ?\Closure $getItemFieldsClosure = null): array
{
    $array = [];

    foreach ($result as $item) {
        $itemData = getItemData($item, $getItemFieldsClosure);
        $itemData = removeExtraColumns($itemData, $specificColumns);
        $array['item'][] = $itemData;
    }

    return $array;
}

function removeExtraColumns(mixed $itemData, ?array $specificColumns): mixed
{
    if (!is_array($itemData)) {
        return $itemData;
    }

    $needSpecificColumns = is_array($specificColumns) && !empty($specificColumns);
    if ($needSpecificColumns) {
        $specificColumns = array_fill_keys($specificColumns, null);
    }

    if ($needSpecificColumns) {
        $itemData = array_intersect_key($itemData, $specificColumns);
    }

    return $itemData;
}

function getItemData(mixed $item, ?\Closure $getItemFieldsClosure): mixed
{
    if (is_callable($getItemFieldsClosure)) {
        $itemData = $getItemFieldsClosure($item);
    } else {
        $itemData = $item;
    }

    return $itemData;
}

function validationErrorToXml(ValidationException $e)
{
    $result = [];
    foreach ($e->getMessages() as $message) {
        $result[] = (string)$message;
    }

    return errorToXml($result);
}

function errorToXml($result): string
{
    return arrayToXml(['errors' => prepareCollectionForXmlRender(Arr::wrap($result))]);
}

/**
 * @throws DOMException
 */
function arrayToXml($array): string
{
    $arrayToXml = new ArrayToXml($array, '', true, 'UTF-8');
    $arrayToXml->prettify();
    return $arrayToXml->toXml();
}

function view($viewName, $vars = []): string
{
    return include_buffered(root_path() . '/resources/views/' . $viewName . '.php', $vars);
}

function include_buffered($filePath, $vars = []): string
{
    extract($vars);
    ob_start();
    require $filePath;
    return (string)ob_get_clean();
}

function render_exception(Throwable $e): string
{
    return $e->getFile() . ' строка <strong>' . $e->getLine() . '</strong><br>' .
        '<br>' . $e->getMessage();
}