<?php
date_default_timezone_set('Europe/London');

//Include the composer autoloader
include __DIR__ . '/../vendor/autoload.php';

//Setup error/exception handler
\Autoq\Lib\Debug\Debug::initialize(true, STDOUT);

//Load up .env environment support
(new \Dotenv\Dotenv(__DIR__ . "/../"))->load();

//Add required services to container and return ioc container
return require __DIR__  . '/services.php';


