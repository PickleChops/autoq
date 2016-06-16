<?php

use Phalcon\Config;

$settings = [
    "app" => [
        'api_host' => getenv('APP_API_HOST'),
        'scheduler_horizon' => getenv('APP_SCHEDULER_HORIZON'),
        'scheduler_sleep' => getenv('APP_SCHEDULER_SLEEP')
    ],
    
    
    "mysql" => [
        "adapter" => \Phalcon\Db\Adapter\Pdo\Mysql::class,
        "host" => getenv('DATABASE_HOST'),
        "username" => getenv('DATABASE_USER'),
        "password" => getenv('DATABASE_PASSWORD'),
        "dbname" => getenv('DATABASE_NAME')
    ],
];

return new Config($settings);

