<?php

use Phalcon\Config;

$settings = [
    "app" => [
        'api_host' => getenv('APP_API_HOST')
    ],
    
    
    "database" => [
        "adapter" => \Phalcon\Db\Adapter\Pdo\Mysql::class,
        "host" => getenv('DATABASE_HOST'),
        "username" => getenv('DATABASE_USER'),
        "password" => getenv('DATABASE_PASSWORD'),
        "dbname" => getenv('DATABASE_NAME')
    ],
];

return new Config($settings);

