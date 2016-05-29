<?php
date_default_timezone_set('Europe/London');

//Setup error/exception handler
\Autoq\Lib\Debug\Debug::initialize(true, STDOUT);

//Include the composer autoloader
include __DIR__ . '/../vendor/autoload.php';

//Load up .env environment support
(new \Dotenv\Dotenv(__DIR__ . "/../"))->load();

//Add required services to container and return ioc container
return require __DIR__  . '/Services.php';


