<?php
date_default_timezone_set('Europe/London');

use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;

//Include the composer autoloader
include __DIR__ . '/../vendor/autoload.php';

//Load up .env environment support
(new \Dotenv\Dotenv(__DIR__ . "/../"))->load();

// Register a Phalcon autoloader for this app
$loader = new Loader();
$loader->registerNamespaces([
    'Api\Services' => './services/', 
    'Api\data' => './data/',
    'Api\data\jobs' => './data/jobs',
    'Lib\Debug' => './Lib/Debug/',
    'CLI' => './cli'
    ]);
$loader->register();

//Add required services to container and return ioc container
return require __DIR__  . '/services.php';


