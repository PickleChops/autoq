<?php

use Phalcon\Config;

$settings = [
    "database" => [
        "adapter" => "Mysql",
        "host" => getenv('DATABASE_HOST'),
        "username" => getenv('DATABASE_USER'),
        "password" => getenv('DATABASE_PASSWORD'),
        "dbname" => getenv('DATABASE_NAME')
    ],
];

return new Config($settings);

