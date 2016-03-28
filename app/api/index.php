<?php

use Phalcon\Mvc\Micro;

$app = new Micro();

/** @var $router \Phalcon\Mvc\Router */
$router = $app->getRouter();
$router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);

// Say something
$app->get('/api/sayhello', function () {
    echo 'hello';
});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo 'This is not the page you are looking for.';
});

$app->handle();



