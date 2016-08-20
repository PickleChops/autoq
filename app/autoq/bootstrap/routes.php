<?php

use Phalcon\Mvc\Router;

$router = new Router(false);

//Use url format we want
$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);

// Set 404 paths
$router->notFound(
    array(
        'namespace'  => 'Autoq\Controllers',
        "controller" => "Error",
        "action"     => "notFound"
    )
);

$router->addGet(
    "/jobs/",
    array(
        'namespace'  => 'Autoq\Controllers',
        'controller' => 'Job',
        'action'     => 'getAll'
    )
);

$router->addPost(
    "/jobs/",
    array(
        'namespace'  => 'Autoq\Controllers',
        'controller' => 'Job',
        'action'     => 'add'
    )
);


$router->addGet(
    "/jobs/:int",
    array(
        'namespace'  => 'Autoq\Controllers',
        'controller' => 'Job',
        'action'     => 'get',
        'id'         => 1
    )
);

$router->addDelete(
    "/jobs/:int",
    array(
        'namespace'  => 'Autoq\Controllers',
        'controller' => 'Job',
        'action'     => 'delete',
        'id'         => 1
    )
);

$router->addPut(
    "/jobs/:int",
    array(
        'namespace'  => 'Autoq\Controllers',
        'controller' => 'Job',
        'action'     => 'put',
        'id'         => 1
    )
);

$router->addGet(
    "/queue/",
    array(
        'namespace'  => 'Autoq\Controllers',
        'controller' => 'Queue',
        'action'     => 'getAll'
    )
);

$router->addGet(
    "/queue/:int",
    array(
        'namespace'  => 'Autoq\Controllers',
        'controller' => 'Queue',
        'action'     => 'get',
        'id'         => 1
    )
);

return $router;