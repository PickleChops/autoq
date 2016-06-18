<?php

/***
 * @var $di Phalcon\Di
 */
$di = new \Phalcon\Di\FactoryDefault();

/**
 * Add the Di container to itself for easy di injection. Turtles all the all way down.
 */
$di->set('di', $di);

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
 * Bind in db connection service
 */
$di->set('dBConnectionMgr', [
    'className' => \Autoq\Services\DbConnectionMgr::class,
    'arguments' => [
        ['type' => 'service', 'name' => 'log'],
        ['type' => 'service', 'name' => 'config']
    ]
]);

/**
 * Bind in our job processor
 */
$di->set('jobProcessor', function () {
    return new \Autoq\Services\JobProcessor\JobDefinitionProcessor();
});

/**
 * Bind in our api helper
 */
$di->set('apiHelper', function () {
    return new \Autoq\Services\ApiHelper();
});


/**
 * JobRepo
 */
$di->set('jobRepo', [
        'className' => \Autoq\Data\Jobs\JobsRepository::class,
        'arguments' => [
            ['type' => 'service', 'name' => 'di']
        ]
    ]
);

/**
 * QueueRepo
 */
$di->set('queueRepo', [
        'className' => \Autoq\Data\Queue\QueueRepository::class,
        'arguments' => [
            ['type' => 'service', 'name' => 'di']
        ]
    ]
);



/**
 * QueueControl
 */
$di->set('queueControl', [
        'className' => \Autoq\Data\Queue\QueueControl::class,
        'arguments' => [
            ['type' => 'service', 'name' => 'config'],
            ['type' => 'service', 'name' => 'log'],
            ['type' => 'service', 'name' => 'queueRepo'],
        ]
    ]
);

/**
 * Cli Scheduler
 */
$di->set('Scheduler', [
    'className' => \Autoq\Cli\Scheduler::class,
    'arguments' => [
        ['type' => 'service', 'name' => 'config'],
        ['type' => 'service', 'name' => 'log'],
        ['type' => 'service', 'name' => 'jobRepo'],
        ['type' => 'service', 'name' => 'queueControl']
    ]
]);

/**
 * Cli Runner
 */
$di->set('Runner', [
    'className' => \Autoq\Cli\Runner::class,
    'arguments' => [
        ['type' => 'service', 'name' => 'config'],
        ['type' => 'service', 'name' => 'log'],
        ['type' => 'service', 'name' => 'queueControl'],
        ['type' => 'service', 'name' => 'dBConnectionMgr']
    ]
]);

/**
 * Cli Sender
 */
$di->set('Sender', [
    'className' => \Autoq\Cli\Sender::class,
    'arguments' => [
        ['type' => 'service', 'name' => 'config'],
        ['type' => 'service', 'name' => 'log'],
        ['type' => 'service', 'name' => 'jobRepo'],
        ['type' => 'service', 'name' => 'queueControl']
    ]
]);


return $di;

