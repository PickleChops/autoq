<?php

use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;

//Include the composer autoloader
include __DIR__ . '/vendor/autoload.php';

//Load up .env environment support
$env = (new \Dotenv\Dotenv(__DIR__))->load();

//Load up debugger
$debug = (new Phalcon\Debug())->listen();


// Register a Phalcon autoloader for this app
$loader = new Loader();
$loader->registerDirs(array(
    './controllers/',
    './models/'
))->register();

/***
 * @var $di Phalcon\Di
 */
$di = new FactoryDefault();

/**
 * Add our routing config
 */
$di->set(
    'router',
    function () {
        return require __DIR__.'/config/routes.php';
    }
);

//Get the Router service
$router = $di->get('router');

//Process routes
$router->handle();

$dispatcher = $di->get('dispatcher');

// Pass the processed router parameters to the dispatcher
$dispatcher->setControllerName($router->getControllerName());
$dispatcher->setActionName($router->getActionName());
$dispatcher->setParams($router->getParams());

// Dispatch the request
$dispatcher->dispatch();

// Get the returned value by the last executed action
$response = $dispatcher->getReturnedValue();

// Check if the action returned is a 'response' object
if ($response instanceof Phalcon\Http\ResponseInterface) {

    // Send the response
    $response->send();
}

