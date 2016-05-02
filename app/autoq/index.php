<?php

//Start up code
require __DIR__ . "/bootstrap/httpStart.php";

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

