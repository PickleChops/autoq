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

$router->addGet(
    "/jobs/",
    array(
        'controller' => 'Job',
        'action'     => 'getAll'
    )
);

$router->addPost(
    "/jobs/",
    array(
        'controller' => 'Job',
        'action'     => 'add'
    )
);


$router->addGet(
    "/jobs/:int",
    array(
        'controller' => 'Job',
        'action'     => 'get',
        'id'         => 1
    )
);

$router->addDelete(
    "/jobs/:int",
    array(
        'controller' => 'Job',
        'action'     => 'delete',
        'id'         => 1
    )
);

$router->addPut(
    "/jobs/:int",
    array(
        'controller' => 'Job',
        'action'     => 'put',
        'id'         => 1
    )
);

return $router;