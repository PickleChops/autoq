<?php

/***
 * @var $di Phalcon\Di
 */
$di = new \Phalcon\Di\FactoryDefault();

/**
 * Add app config
 */
$di->set(
    'config',
    function () {
        return require __DIR__ . '/../config/config.php';
    }
);

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
 * Add router for web requests
 */
$di->set(
    'router',
    function () {
        return require __DIR__ . '/../bootstrap/routes.php';
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

return $di;

