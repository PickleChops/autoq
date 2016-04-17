<?php

use Phalcon\Mvc\Router;

$router = new Router(false);

//Use url format we want
$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);

// Set 404 paths
$router->notFound(
    array(
        "controller" => "Error404",
        "action"     => "index"
    )
);

$router->add(
    "/job/add/",
    array(
        'controller' => 'Job',
        'action'     => 'add'
    )
);

return $router;