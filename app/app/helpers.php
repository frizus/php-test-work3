<?php

use App\Config;
use App\Database\AbstractDatabase;
use App\Database\DatabaseManager;
use App\Database\ILessQL;
use LessQL\Database;
use Phroute\Phroute\RouteCollector;
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
    $collector->get($route . $idPart, [$controllerClass, 'get']);
    $collector->put($route . $idPart, [$controllerClass, 'update']);
    $collector->delete($route . $idPart, [$controllerClass, 'delete']);
}

function allDataToXml($tableName, $fields = null): string
{
    $result = db()->table($tableName)->fetchAll();
    return \arrayToXml(prepareCollectionForXmlRender($result, $fields));
}

function prepareCollectionForXmlRender($result, $specificColumns = null): array
{
    $array = ['item' => []];

    $needSpecificColumns = is_array($specificColumns) && !empty($specificColumns);
    if ($needSpecificColumns) {
        $specificColumns = array_fill_keys($specificColumns, null);
    }

    foreach ($result as $item) {
        $itemData = $item->getData();
        if ($needSpecificColumns) {
            $itemData = array_intersect_key($itemData, $specificColumns);
        }
        $array['item'][] = $itemData;
    }

    return $array;
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