<?php

use App\Controllers\AgenciesController;
use App\Controllers\ContactsController;
use App\Controllers\EstatesController;
use App\Controllers\HomeController;
use App\Controllers\ManagersController;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\RouteCollector;

require __DIR__ . '/../vendor/autoload.php';

$collector = new RouteCollector();

$collector->get('/', [HomeController::class, 'home']);
apiResource('/agencies', AgenciesController::class, $collector);
apiResource('/contacts', ContactsController::class, $collector);
apiResource('/managers', ManagersController::class, $collector);
apiResource('/estates', EstatesController::class, $collector);

try {
    echo $response = (new Dispatcher($collector->getData()))->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Exception $e) {
    if (!config('app.debug')) {
        throw $e;
    }

    echo render_exception($e);
}