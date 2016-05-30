<?php
date_default_timezone_set('Europe/London');

//Include the composer autoloader
include __DIR__ . '/../vendor/autoload.php';

//Load up .env environment support
(new \Dotenv\Dotenv(__DIR__ . "/../"))->load();

//Add base services to container
require __DIR__  . '/services.php';


