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
    'Autoq\Services' => './Services/', 
    'Autoq\Data' => './Data/',
    'Autoq\Data\Jobs' => './Data/Jobs',
    'Autoq\Lib\Debug' => './Lib/Debug/',
    'Autoq\CLI' => './Cli'
    ]);
$loader->register();

//Add required services to container and return ioc container
return require __DIR__  . '/Services.php';


