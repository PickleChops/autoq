<?php

date_default_timezone_set('Europe/London');

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
$loader->registerDirs([
    './controllers/'
]);
$loader->registerNamespaces(['Api\Services' => './services/', 'Api\data' => './data/', 'Api\data\jobs' => './data/jobs']);
$loader->register();


/***
 * @var $di Phalcon\Di
 */
$di = new FactoryDefault();

/**
 * Add logger
 */
$di->set(
    'log',
    function () {
        return new \Phalcon\Logger\Adapter\Stream("php://stdout");
    }
);


/**
 * Add app config 
 */
$di->set(
    'config',
    function () {
        return require __DIR__ . '/config/config.php';
    }
);

/**
 * Add our routing config
 */
$di->set(
    'router',
    function () {
        return require __DIR__ . '/config/routes.php';
    }
);

/**
 * Bind in our job validator
 */
$di->set('jobValidator', function () {
    return new \Api\Services\ValidateJobDefintion();
});

/**
 * Bind in our api helper
 */
$di->set('apiHelper', function () {
    return new \Api\Services\ApiHelper();
});


//Get the Router service
$router = $di->get('router');

//Process routes
$router->handle();

//Get the dispatcher
$dispatcher = $di->get('dispatcher');

// Pass the processed router parameters to the dispatcher
$dispatcher->setControllerName($router->getControllerName());
$dispatcher->setActionName($router->getActionName());
$dispatcher->setParams($router->getParams());

// Dispatch the request
$dispatcher->dispatch();

// Get the returned value by the last executed action
$response = $dispatcher->getReturnedValue();

// Check if the action returned is a response object
if (!($response instanceof Phalcon\Http\ResponseInterface)) {
    
    //If for some reason we do not get a response return an error back to client
    $response = $di->get('apiHelper')->responseError('Unknown request');
}

// Send the response
$response->send();

