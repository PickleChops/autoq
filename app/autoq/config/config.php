<?php

use Phalcon\Config;

$settings = [
    "app" => [
        'api_host' => getenv('APP_API_HOST'),
        'scheduler_horizon' => getenv('APP_SCHEDULER_HORIZON'),
        'scheduler_sleep' => getenv('APP_SCHEDULER_SLEEP'),
        'runner_sleep' => getenv('APP_RUNNER_SLEEP'),
        'runner_staging_dir' => getenv('APP_RUNNER_STAGING_DIR'),
        'sender_sleep' => getenv('APP_SENDER_SLEEP')
    ],
    
    "default_sender_s3" => [
      
        'region' => 'eu-west-1',
        'key' => getenv('S3_DEFAULT_KEY'),
        'secret' => getenv('S3_DEFAULT_SECRET')
    ],
    
    "mysql" => [
        "adapter" => \Phalcon\Db\Adapter\Pdo\Mysql::class,
        "host" => getenv('DATABASE_HOST'),
        "port" => getenv('DATABASE_PORT'),
        "username" => getenv('DATABASE_USER'),
        "password" => getenv('DATABASE_PASSWORD'),
        "database" => getenv('DATABASE_NAME')

    ],

    "default_runner_db" => [
        "adapter" => \Phalcon\Db\Adapter\Pdo\Postgresql::class,
        "host" => getenv('PG_DATABASE_HOST'),
        "port" => getenv('PG_DATABASE_PORT'),
        "username" => getenv('PG_DATABASE_USER'),
        "password" => getenv('PG_DATABASE_PASSWORD'),
        "database" => getenv('PG_DATABASE_NAME')
    ],
];

return new Config($settings);

