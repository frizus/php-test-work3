<?php

use App\Http\Controllers\AgenciesController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\EstatesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagersController;
use Phroute\Phroute\Dispatcher;
use Phroute\Phroute\RouteCollector;
use Respect\Validation\Factory;

// ini_set('display_errors', 'On');

require __DIR__ . '/../vendor/autoload.php';

Factory::setDefaultInstance(
    (new Factory())
        ->withRuleNamespace('App\\Http\\Requests\\Validator\\Rules')
);

$collector = new RouteCollector();

$collector->get('/', [HomeController::class, 'home']);
apiShowResource('/agencies', AgenciesController::class, $collector);
apiShowResource('/contacts', ContactsController::class, $collector);
apiShowResource('/managers', ManagersController::class, $collector);
apiShowResource('/estates', EstatesController::class, $collector);

try {
    echo (new Dispatcher($collector->getData()))->dispatch($_SERVER['REQUEST_METHOD'], strtok($_SERVER['REQUEST_URI'], '?'));
} catch (Exception $e) {
    if (!config('app.debug')) {
        throw $e;
    }

    echo render_exception($e);
}