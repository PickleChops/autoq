<?php

//Start up code
require __DIR__ . "/bootstrap/httpStart.php";

/**
 * @var $router \Phalcon\Mvc\Router
 */
$router = $di->get('router');

//Process routes
$router->handle();

/**
 * @var $dispatcher \Phalcon\Mvc\Dispatcher
 */
$dispatcher = $di->get('dispatcher');

// Pass the processed router parameters to the dispatcher
$dispatcher->setNamespaceName($router->getNamespaceName());
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

