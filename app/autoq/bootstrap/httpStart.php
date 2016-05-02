<?php
date_default_timezone_set('Europe/London');

use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;

//Include the composer autoloader
include __DIR__ . '/../vendor/autoload.php';

//Load up .env environment support
(new \Dotenv\Dotenv(__DIR__ . "/../"))->load();

//Load up debugger
(new Phalcon\Debug())->listen();

// Register a Phalcon autoloader for this app
$loader = new Loader();
$loader->registerDirs([
    './controllers/'
]);
$loader->registerNamespaces(['Api\Services' => './services/', 'Api\data' => './data/', 'Api\data\jobs' => './data/jobs']);
$loader->register();

//Add base services to container
require __DIR__  . '/services.php';


